<?php

namespace Tenthfeet\Enums\Traits;

use BackedEnum;
use InvalidArgumentException;
use Tenthfeet\Enums\Exceptions\UndefinedCaseError;
use UnitEnum;

trait InteractWithCases
{
    public function is(UnitEnum $enum): bool
    {
        return $this === $enum;
    }

    public function isNot(UnitEnum $enum): bool
    {
        return ! $this->is($enum);
    }

    public function in(iterable $enums): bool
    {
        foreach ($enums as $item) {
            if ($item instanceof UnitEnum && $this === $item) {
                return true;
            }
        }

        return false;
    }

    public function notIn(iterable $enums): bool
    {
        return ! $this->in($enums);
    }

    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function values(): array
    {
        if (is_subclass_of(static::class, BackedEnum::class)) {
            return array_column(static::cases(), 'value');
        }

        return static::names();
    }

    public function normalCase(): string
    {
        return preg_replace(
            '/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/',
            ' ',
            $this->name
        ) ?? $this->name;
    }

    public static function options(
        string $property = 'name',
        string $valueKey = 'id',
        string $nameKey = 'text'
    ): array {
        $options = [];

        foreach (static::cases() as $case) {
            $value = $case instanceof BackedEnum
                ? $case->value
                : $case->name;

            if (method_exists($case, $property)) {
                $label = $case->$property();
            } elseif (property_exists($case, $property)) {
                $label = $case->$property;
            } else {
                throw new InvalidArgumentException(
                    sprintf('Property or method [%s] does not exist on enum %s', $property, static::class)
                );
            }

            $options[] = [
                $valueKey => $value,
                $nameKey  => $label,
            ];
        }

        return $options;
    }

    /** Return the enum's value when it's $invoked(). */
    public function __invoke(): string|int
    {
        return $this instanceof BackedEnum ? $this->value : $this->name;
    }

    /** Return the enum's value or name when it's called ::STATICALLY(). */
    public static function __callStatic(string $name, array $args): string|int
    {
        static $map = [];

        $class = static::class;

        if (! isset($map[$class])) {
            foreach (static::cases() as $case) {
                $map[$class][$case->name] = $case instanceof BackedEnum
                    ? $case->value
                    : $case->name;
            }
        }

        if (! array_key_exists($name, $map[$class])) {
            throw new UndefinedCaseError($class, $name);
        }

        return $map[$class][$name];
    }
}
