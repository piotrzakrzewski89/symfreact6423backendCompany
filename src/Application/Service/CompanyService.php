<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\CompanyDto;
use App\Application\Dto\CompanyMessageDto;
use App\Application\Factory\CompanyFactory;
use App\Application\Message\CompanyUpdatedMessage;
use App\Domain\Entity\Admin;
use App\Domain\Entity\Company;
use App\Domain\Enum\CompanyUpdateMessageEnum;
use App\Domain\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CompanyService
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em,
        private CompanyFactory $companyFactory,
        private MessageBusInterface $messageBus,
        private CompanyMailer $companyMailer
    ) {}

    public function createCompany(CompanyDto $dto, int $adminId): Company
    {
        // Sprawdzenie unikalności email
        if ($this->companyRepository->findOneBy(['email' => $dto->email])) {
            throw new \DomainException('Firma o tym adresie email już istnieje.');
        }

        // Sprawdzenie unikalności shortName
        if ($this->companyRepository->findOneBy(['shortName' => $dto->shortName])) {
            throw new \DomainException('Firma o tej krótkiej nazwie już istnieje.');
        }

        $company = $this->companyFactory->createFromDto($dto, $this->getAdmin($adminId));

        $this->em->persist($company);
        $this->em->flush();

        $this->companyMailer->sendCreated($company);

        $this->messageBus->dispatch(
            new CompanyUpdatedMessage(
                new CompanyMessageDto(
                    uuid: $company->getUuid(),
                    action: CompanyUpdateMessageEnum::NEW->value, // update, delete, toggleActive
                    shortName: $company->getShortName(),
                    longName: $company->getLongName(),
                    isActive: $company->isActive(),
                    email: $company->getEmail(),
                    isDeleted: $company->isDeleted(),
                )
            )
        );

        return $company;
    }

    public function updateCompany(CompanyDto $dto, int $adminId): ?Company
    {
        $company = $this->companyRepository->find($dto->id);

        if (!$company) {
            return null;
        }

        // Sprawdzenie unikalności email - tylko jeśli zmieniono lub nowy email
        $existingByEmail = $this->companyRepository->findOneBy(['email' => $dto->email]);
        if ($existingByEmail && $existingByEmail->getId() !== $company->getId()) {
            throw new \DomainException('Firma o tym adresie email już istnieje.');
        }

        // Sprawdzenie unikalności shortName
        $existingByShortName = $this->companyRepository->findOneBy(['shortName' => $dto->shortName]);
        if ($existingByShortName && $existingByShortName->getId() !== $company->getId()) {
            throw new \DomainException('Firma o tej krótkiej nazwie już istnieje.');
        }

        $company = $this->companyFactory->updateFromDto($dto, $company, $this->getAdmin($adminId));

        $this->em->persist($company);
        $this->em->flush();

        $this->companyMailer->sendUpdated($company);

        $this->messageBus->dispatch(
            new CompanyUpdatedMessage(
                new CompanyMessageDto(
                    uuid: $company->getUuid(),
                    action: CompanyUpdateMessageEnum::EDIT->value, // update, delete, toggleActive
                    shortName: $company->getShortName(),
                    longName: $company->getLongName(),
                    isActive: $company->isActive(),
                    email: $company->getEmail(),
                    isDeleted: $company->isDeleted(),
                )
            )
        );

        return $company;
    }

    public function changeActive(int $id, int $adminId): ?Company
    {
        $company = $this->companyRepository->find($id);
        if (!$company) {
            return null;
        }

        $admin = $this->getAdmin($adminId);

        if ($company->isActive()) {
            $company->deactivate($admin);
        } else {
            $company->activate($admin);
        }

        $this->em->flush();

        $this->companyMailer->sendChangeActive($company);

        $this->messageBus->dispatch(
            new CompanyUpdatedMessage(
                new CompanyMessageDto(
                    uuid: $company->getUuid(),
                    action: CompanyUpdateMessageEnum::TOGGLE_ACTIVE->value, // update, delete, toggleActive
                    shortName: $company->getShortName(),
                    longName: $company->getLongName(),
                    isActive: $company->isActive(),
                    email: $company->getEmail(),
                    isDeleted: $company->isDeleted(),
                )
            )
        );

        return $company;
    }

    public function deleteCompany(int $id, int $adminId): ?Company
    {
        $company = $this->companyRepository->find($id);
        if (!$company) {
            return null;
        }

        $admin = $this->getAdmin($adminId);
        $company->softDelete($admin);

        $this->em->flush();

        $this->companyMailer->sendDeleted($company);

        $this->messageBus->dispatch(
            new CompanyUpdatedMessage(
                new CompanyMessageDto(
                    uuid: $company->getUuid(),
                    action: CompanyUpdateMessageEnum::DELETE->value, // update, delete, toggleActive
                    shortName: $company->getShortName(),
                    longName: $company->getLongName(),
                    isActive: $company->isActive(),
                    email: $company->getEmail(),
                    isDeleted: $company->isDeleted(),
                )
            )
        );

        return $company;
    }

    private function getAdmin(int $adminId): Admin
    {
        return $this->em->getReference(Admin::class, $adminId);
    }
}
