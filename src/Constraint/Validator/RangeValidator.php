<?php

declare(strict_types=1);

namespace Temkaa\SimpleValidator\Constraint\Validator;

use Temkaa\SimpleValidator\AbstractConstraintValidator;
use Temkaa\SimpleValidator\Constraint\Assert\Range;
use Temkaa\SimpleValidator\Constraint\ConstraintInterface;
use Temkaa\SimpleValidator\Constraint\Violation;
use Temkaa\SimpleValidator\Exception\InvalidConstraintConfigurationException;
use Temkaa\SimpleValidator\Exception\UnexpectedTypeException;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
final class RangeValidator extends AbstractConstraintValidator
{
    public function validate(mixed $value, ConstraintInterface $constraint): void
    {
        $this->validateConstraint($constraint);
        $this->validateValue($value);

        $value = (float) $value;
        /** @psalm-suppress NoInterfaceProperties */
        if ($constraint->min !== null && $constraint->min > $value) {
            $this->addViolation(new Violation(invalidValue: $value, message: $constraint->minMessage, path: null));
        } else if ($constraint->max !== null && $constraint->max < $value) {
            $this->addViolation(new Violation(invalidValue: $value, message: $constraint->maxMessage, path: null));
        }
    }

    private function validateConstraint(ConstraintInterface $constraint): void
    {
        if (!$constraint instanceof Range) {
            throw new UnexpectedTypeException(actualType: $constraint::class, expectedType: Range::class);
        }

        if ($constraint->min === null && $constraint->max === null) {
            throw new InvalidConstraintConfigurationException(
                'Length constraint must have one of "min" or "max" argument set.',
            );
        }

        if ($constraint->minMessage === null && $constraint->maxMessage === null) {
            throw new InvalidConstraintConfigurationException(
                'Length constraint must have one of "min" or "max" argument set.',
            );
        }

        if (
            $constraint->min !== null && $constraint->minMessage === null
            || $constraint->min === null && $constraint->minMessage !== null
        ) {
            throw new InvalidConstraintConfigurationException(
                'Length constraint must have both "min" and "minMessage" arguments set.',
            );
        }

        if (
            $constraint->max !== null && $constraint->maxMessage === null
            || $constraint->max === null && $constraint->maxMessage !== null
        ) {
            throw new InvalidConstraintConfigurationException(
                'Length constraint must have both "max" and "maxMessage" arguments set.',
            );
        }

        if ($constraint->min !== null && $constraint->max !== null && $constraint->max < $constraint->min) {
            throw new InvalidConstraintConfigurationException(
                'Argument "max" of Length constraint must be equal or greater than "min" value.',
            );
        }
    }

    private function validateValue(mixed $value): void
    {
        if (!is_numeric($value)) {
            throw new UnexpectedTypeException(
                actualType: gettype($value), expectedType: 'float|int',
            );
        }
    }
}
