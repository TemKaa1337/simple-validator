### This is a simple Validator implementation.

### This package provides the following constraints:
##### \#[Count]
Checks whether the specific value as exactly given count.

##### \#[GreaterThan]
Checks whether the specific value is greater than expected.

##### \#[Initialized]
Checks whether the specific property of object is initialized with any value.

##### \#[Length]
Checks whether the specific value is in specified length range.

##### \#[LessThan]
Checks whether the specific value is less than expected.

##### \#[Negative]
Checks whether the specific value is negative (strictly less than 0).

##### \#[NotBlank]
Checks whether the specific value is not blank (empty array/blank string/is not initialized).

##### \#[Positive]
Checks whether the specific value is positive (strictly greater than 0).

##### \#[Range]
The same as Length but for `int` and `float`.

##### \#[Regex]
Checks whether the specific value matches given regexp expression.

### Usage:
```php
<?php

declare(strict_types=1);

namespace App;

use Temkaa\SimpleValidator\Constraint\Assert;
use Temkaa\SimpleValidator\Constraint\ViolationInterface;
use Temkaa\SimpleValidator\Constraint\ViolationListInterface;
use Temkaa\SimpleValidator\Validator;

final class Test
{
    #[Assert\Length(minLength: 2, minMessage: 'min message')]
    #[Assert\NotBlank(message: 'message')]
    public string $name;
    
    #[Assert\Count(expected: 1, message: 'message')]
    public array $preferences;
    
    #[Assert\Positive(message: 'message')]
    #[Assert\GreaterThan(threshold: 18, message: 'message', allowEquality: true)]
    public int $age;
    
    #[Assert\Initialized(message: 'message')]
    public string $middleName;
    
    #[Assert\LessThan(threshold: 95.5, message: 'message')]
    public float $weight;
    
    public 
    
    #[Assert\Regex('/any_pattern/', message: 'message')]
    public string $username;
}

$validator = new Validator();
/** @var ViolationListInterface<ViolationInterface> $errors */
$errors = $validator->validate(new Test());

// or to perform specific assertions

$validator = new Validator();
/** @var ViolationListInterface<ViolationInterface> $errors */
$errors = $validator->validate(new Test(), new CustomAssertion());
```

### Writing custom validators:
```php
<?php

declare(strict_types=1);

namespace App;

use Attribute;
use Temkaa\SimpleValidator\AbstractConstraintValidator;
use Temkaa\SimpleValidator\Constraint\ConstraintInterface;
use Temkaa\SimpleValidator\Constraint\ViolationInterface;
use Temkaa\SimpleValidator\Constraint\ViolationListInterface;
use Temkaa\SimpleValidator\Validator;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Constraint implements ConstraintInterface
{
    public function __construct(
        public string $message,
    ) {
    }

    public function getHandler(): AbstractConstraintValidator
    {
        return new ConstraintHandler();
    }
}

final class ConstraintHandler extends AbstractConstraintValidator
{
    public function validate(mixed $value, ConstraintInterface $constraint): void
    {
        if (!$constraint instanceof Constraint) {
            throw new UnexpectedTypeException(actualType: $constraint::class, expectedType: Constraint::class);
        }

        if ($value->age !== 18) {
            $this->addViolation(new Violation(invalidValue: $value, message: $constraint->message, path: null));
        }
    }
}

#[Constraint(message: 'message')]
final class Test
{
    public int $age = 17;
}

$validator = new Validator();
/** @var ViolationListInterface<ViolationInterface> $errors */
$errors = $validator->validate(new Test());
```

