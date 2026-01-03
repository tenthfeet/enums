<?php

namespace Tests\Fixtures\Enums;

use Tenthfeet\Enums\Traits\InteractWithCases;


enum PaymentStatus: int
{
    use InteractWithCases;

    case PENDING = 1;
    case PAID = 2;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
        };
    }
}
