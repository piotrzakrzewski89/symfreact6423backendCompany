<?php

declare(strict_types=1);

namespace App\Application\Message;

class SendMailMessage
{
    public function __construct(
        public readonly string $recipient,
        public readonly string $subject,
        public readonly string $body
    ) {
    }
}
