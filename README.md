
# Enums

A lightweight PHP trait that adds **powerful, type-safe helper methods** to PHP 8.1+ enums.
Designed for pure PHP projects, with seamless compatibility for frameworks such as Laravel, Symfony, or Slim.

## Requirements

- PHP 8.1 or higher

- Native PHP enums (PureEnum, BackedEnum)

No external dependencies.


## Installation

Via Composer (recommended)

```bash
  composer require tenthfeet/enums
```
    
## Usage/Examples

1.Add the trait to your enum

```php
use Tenthfeet\Enums\Traits\InteractWithCases;

enum Status
{
    use InteractWithCases;

    case Active;
    case Inactive;
}
```
For backed enums:

```php
use Tenthfeet\Enums\Traits\InteractWithCases;

enum PaymentStatus: int
{
    use InteractWithCases;

    case Pending = 1;
    case Paid = 2;
}
```

**Instance Methods**

`is()` / `isNot()`

Strict identity comparison between enum cases.

```php
Status::Active->is(Status::Active);      // true
Status::Active->is(Status::Inactive);    // false

Status::Active->isNot(Status::Inactive); // true
```

`in()` / `notIn()`

Check if an enum exists within an iterable collection.

```php
Status::Active->in([Status::Inactive, Status::Active]); // true
Status::Active->notIn([Status::Inactive]);              // true
```
- Uses strict enum identity comparison

`normalCase()`

Converts the enum case name into a human-readable string (e.g., `UserStatus` into `User Status`). If a `label()` method exists on the enum, it will be used instead.

```php
// Converts PascalCase names:
UserStatus::ActiveMember->normalCase(); // "Active Member"

// Prefers label() method if defined:
PaymentStatus::Pending->normalCase(); // "Not Received"
```

`__invoke()`

Calling an enum case as a function returns:

- the value for backed enums

- the name for pure enums

```php
Status::Active();         // "Active"
PaymentStatus::Paid();   // 2
```

**Static Methods**

`names()`

Returns all enum case names.

```php
Status::names(); // ["Active", "Inactive"]
```

`values()`

Returns:

- enum values for backed enums

- enum names for pure enums

```php
PaymentStatus::values();  // [1, 2]

Status::values();         // ["Active", "Inactive"]
```

`only()` / `except()`

Filter the list of enum cases.

```php
Status::only(['Active']); // [Status::Active]
Status::except(['Inactive']); // [Status::Active]
```

`fromName()` / `tryFromName()`

Retrieve an enum case by its string name.

```php
Status::fromName('Active'); // Status::Active
Status::tryFromName('Unknown'); // null
```

`toDefinition()`

Generates a structured map of the enum, useful for frontend synchronization or complex definitions.

```php
PaymentStatus::toDefinition(['label']);

// Output
[
    'Pending' => ['value' => 1, 'label' => 'Pending'],
    'Paid' => ['value' => 2, 'label' => 'Paid'],
]
```

`options()`

Generates a standardized array of options, ideal for:

- HTML select inputs

- API responses

- Frontend frameworks

```php
Status::options();

// Output
[
    ['id' => 'Active', 'text' => 'Active'],
    ['id' => 'Inactive', 'text' => 'Inactive'],
]
```

Using a custom label method
```php
enum PaymentStatus: int
{
    use InteractWithCases;

    case Pending = 1;
    case Paid = 2;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Not Received',
            self::Paid => 'Received',
        };
    }
}

PaymentStatus::options('label');

// Output
[
    ['id' => 1, 'text' => 'Not Received'],
    ['id' => 2, 'text' => 'Received'],
]
```

Custom keys

```php
PaymentStatus::options(property: 'label', valueKey: 'value', nameKey: 'label');

// Output
[
    ['value' => 1, 'label' => 'Not Received'],
    ['value' => 2, 'label' => 'Received'],
]

```