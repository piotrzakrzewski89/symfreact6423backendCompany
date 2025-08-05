<?php

declare(strict_types=1);

namespace App\Application\Message;

use Symfony\Component\Uid\Uuid;

class CompanyUpdatedMessage
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly string $shortName,
        public readonly string $longName
    ) {}
}
