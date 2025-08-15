<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Application\Dto\CompanyDto;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Application\Factory\CompanyDtoFactory;
use App\Application\Service\CompanyService;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Delete()
    ]
)]
#[Route('/api/company', name: 'api_company_')]
class CompanyController
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private CompanyService $companyService,
        private CompanyDtoFactory $companyDtoFactory,
        private ValidatorInterface $validator
    ) {}

    #[OA\Get(
        path: '/review/{id}',
        summary: 'Firma po id',
        tags: ['Companies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Firma po id',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CompanyDto')
                )
            )
        ]
    )]
    #[Route('/review/{id}', name: 'review', methods: ['GET'])]
    public function review(int $id): JsonResponse
    {
        return new JsonResponse(CompanyDto::fromEntity($this->companyRepository->getCompany($id)));
    }

    #[OA\Get(
        path: '/deleted',
        summary: 'Lista Firm usuniętych',
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
    #[Route('/deleted', name: 'deleted', methods: ['GET'])]
    public function deleted(): JsonResponse
    {
        return new JsonResponse(CompanyDto::fromEntities($this->companyRepository->getAllCompaniesDeleted()));
    }

    #[OA\Get(
        path: '/active',
        summary: 'Lista Firm aktywnych',
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
    #[Route('/active', name: 'active', methods: ['GET'])]
    public function active(): JsonResponse
    {
        return new JsonResponse(CompanyDto::fromEntities($this->companyRepository->getAllCompaniesActive()));
    }

    #[OA\Get(
        path: '/new',
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
    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        try {
            $dto = $this->companyDtoFactory->fromRequest($request);
        } catch (\InvalidArgumentException $e) {
            // Obsłuż brakujące pola lub inne błędy konstrukcji DTO
            return new JsonResponse(['errors' => $e->getMessage()], 400);
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        try {
            $company = $this->companyService->createCompany($dto, 1);
              
        } catch (\DomainException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 400);
        }

        return new JsonResponse(['saved' => 'ok', 'id' => $company->getId()]);
    }

    #[OA\Get(
        path: '/edit',
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
    #[Route('/edit/{id}', name: 'edit', methods: ['POST'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        try {
            $dto = $this->companyDtoFactory->fromRequest($request);
            $dto->id = $id;
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 400);
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        try {
            $company = $this->companyService->updateCompany($dto, 1);
        } catch (\DomainException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 400);
        }

        if (!$company) {
            return new JsonResponse(['errors' => 'Firma nie znaleziona'], 404);
        }

        return new JsonResponse(['saved' => 'ok', 'id' => $company->getId()]);
    }

    #[OA\Get(
        path: '/delete',
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
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $id): JsonResponse
    {
        $company = $this->companyService->deleteCompany($id, 1);

        if (!$company) {
            return new JsonResponse(['errors' => 'Firma nie znaleziona'], 404);
        }

        return new JsonResponse(['api_company_delete' => 'ok'], 200);
    }

    #[OA\Get(
        path: '/toggle-active',
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
    #[Route('/toggle-active/{id}', name: 'toggle-active', methods: ['POST'])]
    public function changeActive(int $id): JsonResponse
    {
        $company = $this->companyService->changeActive($id, 1);

        if (!$company) {
            return new JsonResponse(['errors' => 'Firma nie znaleziona'], 404);
        }

        return new JsonResponse(['api_company_change' => 'ok'], 200);
    }
}
