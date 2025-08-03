<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Domain\Enum\AdminRoleEnum;
use App\Domain\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private AdminRepository $adminRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email | !$password) {
            return new JsonResponse(['error' => 'Email i haslo sa wymagane'], 400);
        }

        $admin = $this->adminRepository->findOneBy(['email' => $email]);

        if (!$admin) {
            return new JsonResponse(['error' => 'Admin po danym mailu nie istnieje'], 400);
        }

        if (!$this->passwordHasher->isPasswordValid($admin, $password)) {
            return new JsonResponse(['error' => 'Nie prawidlowe haslo'], 400);
        }

        if (!in_array(AdminRoleEnum::ADMIN->value, $admin->getRoles())) {
            return new JsonResponse(['error' => 'Brak dostępu - wymagana rola ADMIN'], 403);
        }

        return new JsonResponse(
            [
                'email' => $admin->getEmail(),
                'roles' => $admin->getRoles(),
                'message' => 'Zalogowano poprawnie',
            ]
        );
    }
}
