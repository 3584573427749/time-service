<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum Permission : string {
    case ADD_ENTRY = 'time.addentry';
    case VIEW = 'time.view';
    case UPDATE = 'time.update';
    case DELETE = 'time.delete';
    case APPROVE = 'time.approve';
    case EXPORT = 'time.export';
}
