<?php

declare(strict_types=1);

namespace Temkaa\SimpleValidator\Constraint\Validator;

use Countable;
use Stringable;
use Temkaa\SimpleValidator\AbstractConstraintValidator;
use Temkaa\SimpleValidator\Constraint\Assert\NotBlank;
use Temkaa\SimpleValidator\Constraint\ConstraintInterface;
use Temkaa\SimpleValidator\Constraint\Violation;
use Temkaa\SimpleValidator\Exception\UnexpectedTypeException;
use Temkaa\SimpleValidator\Model\ValidatedValueInterface;

final class NotBlankValidator extends AbstractConstraintValidator
{
    public function validate(ValidatedValueInterface $value, ConstraintInterface $constraint): void
    {
        if (!$constraint instanceof NotBlank) {
            throw new UnexpectedTypeException(actualType: $constraint::class, expectedType: NotBlank::class);
        }

        $errorPath = $value->getPath();
        if (!$value->isInitialized()) {
            $this->addViolation(
                new Violation(invalidValue: $value->getValue(), message: $constraint->message, path: $errorPath),
            );

            return;
        }

        $value = $value->getValue();

        $this->validateType($value);

        if ($value === null) {
            if (!$constraint->allowNull) {
                $this->addViolation(
                    new Violation(invalidValue: $value, message: $constraint->message, path: $errorPath),
                );
            }

            return;
        }

        /** @psalm-suppress MixedArgument */
        $length = is_string($value) || $value instanceof Stringable ? mb_strlen((string) $value) : count($value);

        if ($length === 0) {
            $this->addViolation(new Violation(invalidValue: $value, message: $constraint->message, path: $errorPath));
        }
    }

    private function validateType(mixed $value): void
    {
        /** @noinspection PhpConditionCheckedByNextConditionInspection */
        if (
            $value !== null
            && !is_string($value)
            && !is_array($value)
            && !$value instanceof Countable
            && !$value instanceof Stringable
        ) {
            throw new UnexpectedTypeException(
                actualType: gettype($value),
                expectedType: 'array|\Countable|string|\Stringable|null',
            );
        }
    }
}
