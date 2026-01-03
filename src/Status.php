<?php

namespace Tenthfeet\Enums;

use Tenthfeet\Enums\Traits\InteractWithCases;

enum Status: int
{
    use InteractWithCases;

    case Active = 1;
    case Inactive = 2;
}
