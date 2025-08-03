<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Entity\Company;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyDto
{
    public ?int $id;

    #[Assert\NotBlank(message: 'UUID jest wymagany')]
    public Uuid $uuid;

    #[Assert\NotBlank(message: 'Email jest wymagany')]
    #[Assert\Email(message: 'Nieprawidłowy adres email')]
    public string $email;

    #[Assert\NotBlank(message: 'Krótka nazwa jest wymagana')]
    #[Assert\Length(max: 255, maxMessage: 'Krótka nazwa może mieć maksymalnie {{ limit }} znaków')]
    public string $shortName;

    #[Assert\NotBlank(message: 'Pełna nazwa jest wymagana')]
    public string $longName;

    #[Assert\NotBlank(message: 'NIP jest wymagany')]
    #[Assert\Length(min: 10, max: 10, exactMessage: 'NIP musi mieć dokładnie 10 znaków')]
    public string $taxNumber;

    #[Assert\NotBlank(message: 'Kraj jest wymagany')]
    public string $country;

    #[Assert\NotBlank(message: 'Miasto jest wymagane')]
    public string $city;

    #[Assert\NotBlank(message: 'Kod pocztowy jest wymagany')]
    public string $postalCode;

    #[Assert\NotBlank(message: 'Ulica jest wymagana')]
    public string $street;

    #[Assert\NotBlank(message: 'Numer budynku jest wymagany')]
    public string $buildingNumber;

    public ?string $apartmentNumber;

    public bool $isActive;

    public function __construct(
        ?int $id,
        Uuid $uuid,
        string $email,
        string $shortName,
        string $longName,
        string $taxNumber,
        string $country,
        string $city,
        string $postalCode,
        string $street,
        string $buildingNumber,
        ?string $apartmentNumber,
        bool $isActive,
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->email = $email;
        $this->shortName = $shortName;
        $this->longName = $longName;
        $this->taxNumber = $taxNumber;
        $this->country = $country;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->street = $street;
        $this->buildingNumber = $buildingNumber;
        $this->apartmentNumber = $apartmentNumber;
        $this->isActive = $isActive;
    }


    public static function fromEntity(Company $company): self
    {
        return new self(
            $company->getId(),
            $company->getUuid(),
            $company->getEmail(),
            $company->getShortName(),
            $company->getLongName(),
            $company->getTaxNumber(),
            $company->getCountry(),
            $company->getCity(),
            $company->getPostalCode(),
            $company->getStreet(),
            $company->getBuildingNumber(),
            $company->getApartmentNumber(),
            $company->isActive()
        );
    }

    public static function fromEntities(array $companies): array
    {
        return array_map(fn(Company $company) => self::fromEntity($company), $companies);
    }
}
