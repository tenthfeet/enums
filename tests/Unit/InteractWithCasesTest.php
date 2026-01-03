<?php

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tenthfeet\Enums\Exceptions\UndefinedCaseError;
use Tests\Fixtures\Enums\CaseType;
use Tests\Fixtures\Enums\PaymentStatus;
use Tests\Fixtures\Enums\Status;

class InteractWithCasesTest extends TestCase
{
    /* ---------------------------------
     | is() / isNot()
     |---------------------------------*/

    public function test_is_compares_enum_identity(): void
    {
        $this->assertTrue(Status::ACTIVE->is(Status::ACTIVE));
        $this->assertFalse(Status::ACTIVE->is(Status::INACTIVE));
    }

    public function test_is_not(): void
    {
        $this->assertTrue(Status::ACTIVE->isNot(Status::INACTIVE));
        $this->assertFalse(Status::ACTIVE->isNot(Status::ACTIVE));
    }

    /* ---------------------------------
     | in() / notIn()
     |---------------------------------*/

    public function test_in_returns_true_when_present(): void
    {
        $this->assertTrue(
            Status::ACTIVE->in([Status::INACTIVE, Status::ACTIVE])
        );
    }

    public function test_in_returns_false_when_not_present(): void
    {
        $this->assertFalse(
            Status::ACTIVE->in([Status::INACTIVE, PaymentStatus::PAID])
        );
    }

    public function test_in_ignores_non_enum_values(): void
    {
        $this->assertTrue(
            Status::ACTIVE->in(['foo', 123, Status::ACTIVE])
        );
    }

    public function test_not_in(): void
    {
        $this->assertTrue(
            Status::ACTIVE->notIn([Status::INACTIVE])
        );
    }

    /* ---------------------------------
     | names() / values()
     |---------------------------------*/

    public function test_names_returns_all_case_names(): void
    {
        $this->assertSame(
            ['ACTIVE', 'INACTIVE'],
            Status::names()
        );
    }

    public function test_values_for_pure_enum_falls_back_to_names(): void
    {
        $this->assertSame(
            ['ACTIVE', 'INACTIVE'],
            Status::values()
        );
    }

    public function test_values_for_backed_enum_returns_values(): void
    {
        $this->assertSame(
            [1, 2],
            PaymentStatus::values()
        );
    }

    /* ---------------------------------
     | normalCase()
     |---------------------------------*/

    public function test_normal_case_formats_name(): void
    {
        $this->assertSame(
            'ACTIVE',
            Status::ACTIVE->normalCase()
        );

        $this->assertSame(
            'Pascal Type',
            CaseType::PascalType->normalCase()
        );

        $this->assertSame(
            'GAP Analysis And Reporting',
            CaseType::GAPAnalysisAndReporting->normalCase()
        );
    }

    /* ---------------------------------
     | options()
     |---------------------------------*/

    public function test_options_default(): void
    {
        $this->assertSame(
            [
                ['id' => 'ACTIVE', 'text' => 'ACTIVE'],
                ['id' => 'INACTIVE', 'text' => 'INACTIVE'],
            ],
            Status::options()
        );
    }

    public function test_options_with_label_method(): void
    {
        $this->assertSame(
            [
                ['id' => 1, 'text' => 'Pending'],
                ['id' => 2, 'text' => 'Paid'],
            ],
            PaymentStatus::options('label')
        );
    }

    public function test_options_with_custom_name_and_value_keys(): void
    {
        $this->assertSame(
            [
                ['value' => 1, 'label' => 'Pending'],
                ['value' => 2, 'label' => 'Paid'],
            ],
            PaymentStatus::options('label','value','label')
        );
    }

    public function test_options_throws_exception_for_invalid_property(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Status::options('invalidProperty');
    }

    /* ---------------------------------
     | __invoke()
     |---------------------------------*/

    public function test_invoke_returns_name_for_pure_enum(): void
    {
        $this->assertSame(
            'ACTIVE',
            Status::ACTIVE()
        );
    }

    public function test_invoke_returns_value_for_backed_enum(): void
    {
        $this->assertSame(
            1,
            PaymentStatus::PENDING()
        );
    }

    /* ---------------------------------
     | __callStatic()
     |---------------------------------*/

    public function test_call_static_returns_value_or_name(): void
    {
        $this->assertSame('ACTIVE', Status::ACTIVE());
        $this->assertSame(2, PaymentStatus::PAID());
    }

    public function test_call_static_throws_for_invalid_case(): void
    {
        $this->expectException(UndefinedCaseError::class);

        Status::UNKNOWN();
    }
}
