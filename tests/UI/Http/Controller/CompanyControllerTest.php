<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Controller;

use App\Domain\Repository\CompanyRepository;
use App\Tests\BaseTestController;
use Symfony\Component\Uid\Uuid;

class CompanyControllerTest extends BaseTestController
{
    public function testOfTestInit(): void
    {
        self::assertTrue(true);
    }

    public function testCreateCompany(): void
    {
        $payload = $this->createCompany();

        $companyRepo = self::getContainer()->get(CompanyRepository::class);
        $companies = $companyRepo->findBy(['email' => $payload['email']]);

        $this->assertCount(1, $companies);
        $this->assertSame($payload['longName'], $companies[0]->getLongName());
    }

    public function testEditCompany(): void
    {
        // Najpierw tworzymy firmę (wykorzystując helper)
        $payloadCreate = $this->createCompany();

        $companyRepo = self::getContainer()->get(CompanyRepository::class);
        $company = $companyRepo->findOneBy(['uuid' => $payloadCreate['uuid']]);

        $companyId = $company->getId();

        // Przygotowujemy payload do edycji, zmieniając np. email i nazwę
        $payloadEdit = [
            'uuid' => $company->getUuid()->toRfc4122(),
            'email' => 'firma@example.com',
            'shortName' => 'ZZ',
            'longName' => 'Zmodyfikowana Sp. z o.o.',
            'taxNumber' => '0987654321',
            'country' => 'Polska',
            'city' => 'Warszawa',
            'postalCode' => '00-001',
            'street' => 'Nowa',
            'buildingNumber' => '1',
            'apartmentNumber' => '10',
            'isActive' => true,
        ];

        $this->request(
            'POST',
            '/api/edit-company/' . $companyId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payloadEdit)
        );

        $response = $this->response();
        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);
        $this->assertSame('ok', $data['saved'] ?? null);

        // Sprawdzenie czy edycja zadziałała
        $companyUpdated = $companyRepo->find($companyId);
        $this->assertSame('firma@example.com', $companyUpdated->getEmail());
        $this->assertSame('Zmodyfikowana Sp. z o.o.', $companyUpdated->getLongName());
        $this->assertSame('Warszawa', $companyUpdated->getCity());
        $this->assertTrue($companyUpdated->isActive());
    }

    public function testDeleteCompany(): void
    {
        $payloadCreate = $this->createCompany();

        $this->request(
            'POST',
            '/api/delete-company/' . 1,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null
        );

        $companyRepo = self::getContainer()->get(CompanyRepository::class);
        $company = $companyRepo->findOneBy(['uuid' => $payloadCreate['uuid']]);

        $this->assertFalse($company->isActive());
        $this->assertTrue($company->isDeleted());
    }

    public function testChangeActivityCompany(): void
    {
        $payloadCreate = $this->createCompany();

        $companyRepo = self::getContainer()->get(CompanyRepository::class);
        $company = $companyRepo->findOneBy(['uuid' => $payloadCreate['uuid']]);

        $this->assertTrue($company->isActive());

        $this->request(
            'POST',
            '/api/change-active-company/' . 1,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null
        );


        $companyRepo = self::getContainer()->get(CompanyRepository::class);
        $company = $companyRepo->findOneBy(['uuid' => $payloadCreate['uuid']]);

        $this->assertFalse($company->isActive());

        $this->request(
            'POST',
            '/api/change-active-company/' . 1,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null
        );

        $this->assertFalse($company->isActive());
    }


    private function createCompany(array $override = []): array
    {
        $payload = array_merge(
            [
            'uuid' => Uuid::v4()->toRfc4122(),
            'email' => 'firma@example.com',
            'shortName' => 'TT',
            'longName' => 'Testowa Sp. z o.o.',
            'taxNumber' => '1234567890',
            'country' => 'Polska',
            'city' => 'Wrocław',
            'postalCode' => '50-001',
            'street' => 'Piłsudskiego',
            'buildingNumber' => '10A',
            'apartmentNumber' => '5',
            'isActive' => true,
            ], $override
        );

        $this->request(
            'POST',
            '/api/new-company',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $response = $this->response();
        $this->assertSame(200, $response->getStatusCode());

        return $payload;
    }
}
