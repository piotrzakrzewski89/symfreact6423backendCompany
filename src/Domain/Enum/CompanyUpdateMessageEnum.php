<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum CompanyUpdateMessageEnum: string
{
    case NEW = 'new';
    case EDIT = 'edit';
    case DELETE = 'delete';
    case TOGGLE_ACTIVE = 'toggleActive';
}
