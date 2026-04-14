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
            'Active',
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

    public function test_normal_case_prefers_label_method(): void
    {
        $this->assertSame(
            'Pending',
            PaymentStatus::PENDING->normalCase()
        );
    }

    /* ---------------------------------
     | only() / except()
     |---------------------------------*/

    public function test_only_returns_specified_cases(): void
    {
        $cases = Status::only(['ACTIVE']);

        $this->assertCount(1, $cases);
        $this->assertSame(Status::ACTIVE, reset($cases));
    }

    public function test_except_returns_all_but_specified_cases(): void
    {
        $cases = Status::except(['INACTIVE']);

        $this->assertCount(1, $cases);
        $this->assertSame(Status::ACTIVE, reset($cases));
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
     | toDefinition()
     |---------------------------------*/

    public function test_to_definition_returns_structured_map(): void
    {
        $definition = PaymentStatus::toDefinition(['label']);

        $this->assertArrayHasKey('PENDING', $definition);
        $this->assertSame(1, $definition['PENDING']['value']);
        $this->assertSame('Pending', $definition['PENDING']['label']);
    }

    public function test_to_definition_handles_unit_enum(): void
    {
        $definition = Status::toDefinition(['label']);

        $this->assertArrayHasKey('ACTIVE', $definition);
        $this->assertSame('ACTIVE', $definition['ACTIVE']['value']);
        $this->assertSame('Active', $definition['ACTIVE']['label']);
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

    /* ---------------------------------
     | fromName() / tryFromName()
     |---------------------------------*/

    public function test_from_name_returns_case(): void
    {
        $this->assertSame(Status::ACTIVE, Status::fromName('ACTIVE'));
        $this->assertSame(PaymentStatus::PAID, PaymentStatus::fromName('PAID'));
    }

    public function test_from_name_throws_for_invalid_name(): void
    {
        $this->expectException(UndefinedCaseError::class);

        Status::fromName('INVALID');
    }

    public function test_try_from_name_returns_null_for_invalid_name(): void
    {
        $this->assertNull(Status::tryFromName('INVALID'));
    }
}
