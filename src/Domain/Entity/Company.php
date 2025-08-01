<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Repository\CompanyRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: '`company`')]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;
    #[ORM\Column(type: 'uuid', nullable: false)]
    private Uuid $uuid;
    #[ORM\Column(length: 255, nullable: false)]
    private string $longName;
    #[ORM\Column(length: 255, nullable: false)]
    private string $shortName;
    #[ORM\Column(length: 255, nullable: false)]
    private string $taxNumber;
    #[ORM\Column(length: 255, nullable: false)]
    private string $country;
    #[ORM\Column(length: 255, nullable: false)]
    private string $city;
    #[ORM\Column(length: 255, nullable: false)]
    private string $postalCode;
    #[ORM\Column(length: 255, nullable: false)]
    private string $street;
    #[ORM\Column(length: 255, nullable: false)]
    private string $buildingNumber;
    #[ORM\Column(length: 255, nullable: true)]
    private string $apartmentNumber;
    private DateTimeImmutable $createdAt;
    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updateAt;
    #[ORM\Column()]
    private Admin $createdBy;
    #[ORM\Column(nullable: true)]
    private ?Admin $updatedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getLongName(): ?string
    {
        return $this->longName;
    }

    public function setLongName(string $longName): static
    {
        $this->longName = $longName;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): static
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string $taxNumber): static
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function setBuildingNumber(string $buildingNumber): static
    {
        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(?string $apartmentNumber): static
    {
        $this->apartmentNumber = $apartmentNumber;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getCreatedBy(): ?Admin
    {
        return $this->createdBy;
    }

    public function setCreatedBy(Admin $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

}
