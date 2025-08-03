<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Application\Dto\CompanyDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class CompanyDtoFactory
{
    public function fromRequest(Request $request): CompanyDto
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Nieprawidłowe dane JSON w żądaniu');
        }

        // Wymagane pola - jeśli ich brak, rzuć wyjątek
        $requiredFields = ['uuid', 'email', 'shortName', 'longName', 'taxNumber', 'country', 'city', 'postalCode', 'street', 'buildingNumber', 'isActive'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Brak wymaganego pola: $field");
            }
        }

        return new CompanyDto(
            $data['id'] ?? null,
            Uuid::fromString($data['uuid']),
            $data['email'],
            $data['shortName'],
            $data['longName'],
            $data['taxNumber'],
            $data['country'],
            $data['city'],
            $data['postalCode'],
            $data['street'],
            $data['buildingNumber'],
            $data['apartmentNumber'] ?? null,
            $data['isActive'],
        );
    }
}
