<?php

declare(strict_types=1);

namespace App\Application\Message;

use App\Application\Dto\CompanyMessageDto;

class CompanyUpdatedMessage
{
    public function __construct(
        public readonly CompanyMessageDto $payload
    ) {}
}
