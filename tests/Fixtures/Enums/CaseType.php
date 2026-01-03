<?php

namespace Tests\Fixtures\Enums;

use Tenthfeet\Enums\Traits\InteractWithCases;

enum CaseType: int
{
    use InteractWithCases;

    case PascalType = 1;
    case GAPAnalysisAndReporting  = 2;
    case ABC  = 3;
}
