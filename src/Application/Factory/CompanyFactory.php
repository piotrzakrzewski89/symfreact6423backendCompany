<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Application\Dto\CompanyDto;
use App\Domain\Entity\Admin;
use App\Domain\Entity\Company;

class CompanyFactory
{
    public function createFromDto(CompanyDto $dto, Admin $admin): Company
    {
        $company = new Company();
        $this->mapDtoToEntity($dto, $company);
        $company->setIsDeleted(false);
        $company->setCreatedBy($admin);

        return $company;
    }

    public function updateFromDto(CompanyDto $dto, Company $company, Admin $admin): Company
    {
        $this->mapDtoToEntity($dto, $company);
        $company->setIsDeleted(false);
        $company->setCreatedBy($admin);

        return $company;
    }

    private function mapDtoToEntity(CompanyDto $dto, Company $company): void
    {
        $company
            ->setUuid($dto->uuid)
            ->setEmail($dto->email)
            ->setShortName($dto->shortName)
            ->setLongName($dto->longName)
            ->setTaxNumber($dto->taxNumber)
            ->setCountry($dto->country)
            ->setCity($dto->city)
            ->setPostalCode($dto->postalCode)
            ->setStreet($dto->street)
            ->setBuildingNumber($dto->buildingNumber)
            ->setApartmentNumber($dto->apartmentNumber)
            ->setIsActive($dto->isActive);
    }
}
