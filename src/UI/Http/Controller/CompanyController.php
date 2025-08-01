<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Dto\CompanyDto;
use App\Domain\Entity\Company;
use App\Domain\Repository\CompanyRepository;
use OpenApi\Attributes as OA;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class CompanyController extends AbstractController
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[OA\Get(
        path: '/api/list-companies',
        summary: 'Lista Firm',
        tags: ['Companies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista Firm',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/CompaniesDto')
                )
            )
        ]
    )]
    #[Route('/api/list-companies', name: 'api_companies_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->companyRepository->getAllCompanies();

        return $this->json(CompanyDto::fromEntities($users));
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

        $user = new Company();
        $user->setEmail($data['email'] ?? '');
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password'] ?? ''));
        $user->setFirstName($data['firstName'] ?? '');
        $user->setLastName($data['lastName'] ?? '');
        $user->setEmployeeNumber($data['employeeNumber'] ?? '');
        $user->setIsActive((bool)$data['isActive']);
        $user->setCreatedBy($this->em->getReference(User::class, 1));

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(['saved' => 'ok'], 200);
    }
}
