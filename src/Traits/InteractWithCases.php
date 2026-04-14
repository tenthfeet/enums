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
        return !$this->is($enum);
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
        return !$this->in($enums);
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
        if (method_exists($this, 'label')) {
            return $this->label();
        }

        return preg_replace(
            '/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/',
            ' ',
            $this->name
        ) ?? $this->name;
    }

    /**
     * Return only specific cases by name.
     *
     * @param array<int, string> $names
     * @return static[]
     */
    public static function only(array $names): array
    {
        return array_filter(
            static::cases(),
            fn($case) => in_array($case->name, $names, true)
        );
    }

    /**
     * Return all cases except those specified by name.
     *
     * @param array<int, string> $names
     * @return static[]
     */
    public static function except(array $names): array
    {
        return array_filter(
            static::cases(),
            fn($case) => !in_array($case->name, $names, true)
        );
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
                $nameKey => $label,
            ];
        }

        return $options;
    }

    /**
     * Return a structured map of the enum cases.
     *
     * @param array<int, string> $with
     * @return array<string, array<string, mixed>>
     */
    public static function toDefinition(array $with = []): array
    {
        $definitions = [];

        foreach (static::cases() as $case) {
            $value = $case instanceof BackedEnum ? $case->value : $case->name;

            $data = ['value' => $value];

            foreach ($with as $property) {
                if (method_exists($case, $property)) {
                    $data[$property] = $case->$property();
                } elseif ($property === 'label') {
                    $data['label'] = $case->normalCase();
                }
            }

            $definitions[$case->name] = $data;
        }

        return $definitions;
    }

    /**
     * Get an enum case by its name.
     *
     * @throws UndefinedCaseError
     */
    public static function fromName(string $name): static
    {
        return static::tryFromName($name) ?? throw new UndefinedCaseError(static::class, $name);
    }

    /**
     * Try to get an enum case by its name, or return null.
     */
    public static function tryFromName(string $name): ?static
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
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

        if (!isset($map[$class])) {
            foreach (static::cases() as $case) {
                $map[$class][$case->name] = $case instanceof BackedEnum
                    ? $case->value
                    : $case->name;
            }
        }

        if (!array_key_exists($name, $map[$class])) {
            throw new UndefinedCaseError($class, $name);
        }

        return $map[$class][$name];
    }
}
