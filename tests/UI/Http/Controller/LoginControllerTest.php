<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Controller;

use App\Domain\Entity\Admin;
use App\Domain\Enum\AdminRoleEnum;
use App\Domain\Repository\AdminRepository;
use App\Tests\BaseTestController;
use Symfony\Component\Uid\Uuid;

class LoginControllerTest extends BaseTestController
{
    public function testOfTestInit(): void
    {
        self::assertTrue(true);
    }

    public function testCheckAdminExistingQuery(): void
    {

        $connection = static::getContainer()->get('database_connection');

        $result = $connection->prepare("SELECT * FROM admin a WHERE a.uuid = 'dd2f7b38-bf2d-47c0-8cb6-1894b348df12'")->executeQuery()->fetchAssociative();

        $this->assertNotNull($result);
        $this->assertSame('dd2f7b38-bf2d-47c0-8cb6-1894b348df12', $result['uuid']);
        $this->assertSame('admin@example.com', $result['email']);
    }

    public function testCheckAdminExistingDoctrine(): void
    {

        $connection = static::getContainer()->get('database_connection');

        $result = $connection->prepare("SELECT * FROM admin a WHERE a.uuid = 'dd2f7b38-bf2d-47c0-8cb6-1894b348df12'")->executeQuery()->fetchAssociative();

        $adminRepo = self::getContainer()->get(AdminRepository::class);

        $uuid = Uuid::fromString('dd2f7b38-bf2d-47c0-8cb6-1894b348df12');

        $result = $adminRepo->findOneBy(['uuid' => $uuid]);

        $this->assertNotNull($result);
        $this->assertSame('admin@example.com', $result->getEmail());
        $this->assertSame($uuid->toRfc4122(), $result->getUuid()->toRfc4122());
    }

    public function testSettersAndGetters(): void
    {
        $admin = new Admin();

        $uuid = Uuid::v4();
        $admin->setUuid($uuid);
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('Systemowy');
        $admin->setIsActive(true);
        $admin->setRoles([AdminRoleEnum::ADMIN->value]);
        $admin->setPassword('hashed');
        $now = new \DateTimeImmutable();
        $admin->setCreatedAt($now);

        $this->assertSame($uuid, $admin->getUuid());
        $this->assertSame('admin@example.com', $admin->getEmail());
        $this->assertSame('Admin', $admin->getFirstName());
        $this->assertSame('Systemowy', $admin->getLastName());
        $this->assertSame(true, $admin->isActive());
        $this->assertSame(['ROLE_ADMIN'], $admin->getRoles());
        $this->assertSame('hashed', $admin->getPassword());
        $this->assertSame($now, $admin->getCreatedAt());
    }

    public function testLoginSuccess(): void
    {
        $this->request(
            'POST', '/api/company/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            ], json_encode(
                [
                'email' => 'admin@example.com',
                'password' => 'admin', // zakładamy że to hasło odpowiada hashowi z fixtures
                ]
            )
        );

        $response = $this->response();

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);

        $this->assertSame('admin@example.com', $data['email']);
        $this->assertContains('ROLE_ADMIN', $data['roles']);
        $this->assertSame('Zalogowano poprawnie', $data['message']);
    }

    public function testLoginWithInvalidEmail(): void
    {
        $this->request(
            'POST', '/api/company/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            ], json_encode(
                [
                'email' => 'wrong@example.com',
                'password' => 'admin',
                ]
            )
        );

        $this->assertSame(400, $this->response()->getStatusCode());
    }

    public function testLoginWithInvalidPassword(): void
    {
        $this->request(
            'POST', '/api/company/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            ], json_encode(
                [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
                ]
            )
        );

        $this->assertSame(400, $this->response()->getStatusCode());
    }

    public function testLoginWithEmptyData(): void
    {
        $this->request(
            'POST', '/api/company/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            ], json_encode([])
        );

        $this->assertSame(400, $this->response()->getStatusCode());
    }

    public function testLoginFailsWithoutAdminRole(): void
    {
        // Tworzymy kopię admina z inną rolą
        $admin = new Admin();
        $admin->setEmail('notadmin@example.com')
            ->setFirstName('Not')
            ->setLastName('Admin')
            ->setIsActive(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setRoles(['ROLE_USER']) // nie ma ROLE_ADMIN
            ->setPassword(password_hash('secret', PASSWORD_BCRYPT))
            ->setUuid(Uuid::v4());

        self::getContainer()->get(\Doctrine\ORM\EntityManagerInterface::class)->persist($admin);
        self::getContainer()->get(\Doctrine\ORM\EntityManagerInterface::class)->flush();

        $this->request(
            'POST', '/api/company/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            ], json_encode(
                [
                'email' => 'notadmin@example.com',
                'password' => 'secret',
                ]
            )
        );

        $this->assertSame(403, $this->response()->getStatusCode());
    }
}
