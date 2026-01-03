<?php

namespace Tests\Fixtures\Enums;

use Tenthfeet\Enums\Traits\InteractWithCases;

enum Status
{
    use InteractWithCases;

    case ACTIVE;
    case INACTIVE;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }
}
