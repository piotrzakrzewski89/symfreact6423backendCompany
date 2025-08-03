<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Application\Dto\CompanyDto;
use App\Domain\Entity\Admin;
use App\Domain\Entity\Company;
use App\Domain\Repository\CompanyRepository;
use OpenApi\Attributes as OA;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Delete()
    ]
)]
class CompanyController
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[OA\Get(
        path: '/api/list-company',
        summary: 'Lista Firm',
        tags: ['Companies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista Firm',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CompanyDto')
                )
            )
        ]
    )]
    #[Route('/api/list-company', name: 'api_company_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->companyRepository->getAllCompanies();

        return new JsonResponse(CompanyDto::fromEntities($users));
    }

    #[OA\Get(
        path: '/api/new-company',
        summary: 'Tworzenie firmy',
        tags: ['Companies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tworzenie firmy',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CompaniesDto')
                )
            )
        ]
    )]
    #[Route('/api/new-company', name: 'api_company_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $company = new Company();
        $company->setUuid(Uuid::fromString($data['uuid']))
            ->setEmail($data['email'])
            ->setShortName($data['shortName'])
            ->setLongName($data['longName'])
            ->setTaxNumber($data['taxNumber'])
            ->setCountry($data['country'])
            ->setCity($data['city'])
            ->setPostalCode($data['postalCode'])
            ->setStreet($data['street'])
            ->setBuildingNumber($data['buildingNumber'])
            ->setApartmentNumber($data['apartmentNumber'])
            ->setIsActive($data['isActive'])
            ->setIsDeleted(false)
            ->setCreatedBy($this->em->getReference(Admin::class, 1));

        $this->em->persist($company);
        $this->em->flush();

        return new JsonResponse(['saved' => 'ok'], 200);
    }

    #[OA\Get(
        path: '/api/edit-company',
        summary: 'Edycja firmy',
        tags: ['Companies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Edycja firmy',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CompaniesDto')
                )
            )
        ]
    )]
    #[Route('/api/edit-company/{id}', name: 'api_company_edit', methods: ['POST'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $id]);

        if (!$company) {
            return new JsonResponse(['error' => 'Firma nie znaleziona'], 404);
        }

        $company->setUuid(Uuid::fromString($data['uuid']))
            ->setEmail($data['email'])
            ->setShortName($data['shortName'])
            ->setLongName($data['longName'])
            ->setTaxNumber($data['taxNumber'])
            ->setCountry($data['country'])
            ->setCity($data['city'])
            ->setPostalCode($data['postalCode'])
            ->setStreet($data['street'])
            ->setBuildingNumber($data['buildingNumber'])
            ->setApartmentNumber($data['apartmentNumber'])
            ->setIsActive($data['isActive'])
            ->setIsDeleted(false)
            ->setCreatedBy($this->em->getReference(Admin::class, 1));

        $this->em->persist($company);
        $this->em->flush();

        return new JsonResponse(['saved' => 'ok'], 200);
    }

    #[OA\Get(
        path: '/api/delete-company',
        summary: 'Usuwanie firmy',
        tags: ['Companies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuwanie firmy',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CompaniesDto')
                )
            )
        ]
    )]
    #[Route('/api/delete-company/{id}', name: 'api_company_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $id]);

        if (!$company) {
            return new JsonResponse(['error' => 'Firma nie znaleziona'], 404);
        }

        $company->setIsDeleted(true)
            ->setIsActive(false)
            ->setUpdatedBy($this->em->getReference(Admin::class, 1))
            ->setUpdatedAt(new DateTimeImmutable())
            ->setDeletedAt(new DateTimeImmutable());

        $this->em->persist($company);
        $this->em->flush();

        return new JsonResponse(['api_company_delete' => 'ok'], 200);
    }

    #[OA\Get(
        path: '/api/change-active-company',
        summary: 'Zmiana aktywności firmy',
        tags: ['Companies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Zmiana aktywności  firmy',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CompaniesDto')
                )
            )
        ]
    )]
    #[Route('/api/change-active-company/{id}', name: 'api_company_change-active', methods: ['POST'])]
    public function changeActive(Request $request, int $id): JsonResponse
    {
        $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $id]);

        if (!$company) {
            return new JsonResponse(['error' => 'Firma nie znaleziona'], 404);
        }

        if (true === $company->isActive()) {
            $company->setIsActive(false);
        } else {
            $company->setIsActive(true);
        }

        $company->setUpdatedBy($this->em->getReference(Admin::class, 1))
            ->setUpdatedAt(new DateTimeImmutable());

        $this->em->persist($company);
        $this->em->flush();

        return new JsonResponse(['api_company_change' => 'ok'], 200);
    }
}
