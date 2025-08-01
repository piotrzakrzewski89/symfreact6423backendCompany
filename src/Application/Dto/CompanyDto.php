<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Entity\Company;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class CompanyDto
{
    public int $id;
    public string $email;
    public array $roles;
    public bool $isActive;

    public string $firstName;
    public string $lastName;

    public Uuid $uuid;
    public DateTimeImmutable $createdAt;
    public ?DateTime $updatedAt;
    public ?DateTimeImmutable $deletedAt;
    public ?DateTime $lastLogin;
    public string $employeeNumber;


    public function __construct(private Company $company)
    {
        $this->id = $company->getId();
        $this->email = $company->getEmail();
        $this->roles = $company->getRoles();
        $this->isActive = $company->isActive();
        $this->firstName = $company->getFirstName();
        $this->lastName = $company->getLastName();
        $this->uuid = $company->getUuid();
        $this->createdAt = $company->getCreatedAt();
        $this->updatedAt = $company->getUpdatedAt();
        $this->deletedAt = $company->getDeletedAt();
        $this->lastLogin = $company->getLastLoginAt();
        $this->employeeNumber = $company->getEmployeeNumber();
    }

    public static function fromEntities(array $companies): array
    {
        return array_map(fn(Company $company) => new self($company), $companies);
    }
}
