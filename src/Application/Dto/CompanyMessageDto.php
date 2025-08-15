<?php

declare(strict_types=1);

namespace App\Application\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;
use App\Domain\Enum\CompanyUpdateMessageEnum;

class CompanyMessageDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly Uuid $uuid,

        #[Assert\Choice(choices: [
            CompanyUpdateMessageEnum::NEW->value,
            CompanyUpdateMessageEnum::EDIT->value,
            CompanyUpdateMessageEnum::DELETE->value,
            CompanyUpdateMessageEnum::TOGGLE_ACTIVE->value,
        ])]
        public readonly string $action,

        #[Assert\NotBlank]
        public readonly string $shortName,

        #[Assert\NotBlank]
        public readonly string $longName,

        #[Assert\NotNull]
        public readonly bool $isActive,

        #[Assert\Email]
        public readonly string $email,

        #[Assert\NotNull]
        public readonly bool $isDeleted,
    ) {}

    public function toArray(): array
    {
        return [
            'uuid' => (string)$this->uuid,
            'action' => $this->action,
            'shortName' => $this->shortName,
            'longName' => $this->longName,
            'isActive' => $this->isActive,
            'email' => $this->email,
            'isDeleted' => $this->isDeleted,
        ];
    }
}
