<?php

declare(strict_types=1);

namespace Temkaa\SimpleValidator;

use Temkaa\SimpleValidator\Constraint\ConstraintInterface;
use Temkaa\SimpleValidator\Constraint\ConstraintValidatorInterface;
use Temkaa\SimpleValidator\Constraint\ViolationInterface;
use Temkaa\SimpleValidator\Constraint\ViolationList;
use Temkaa\SimpleValidator\Constraint\ViolationListInterface;
use Temkaa\SimpleValidator\Model\ValidatedValueInterface;

abstract class AbstractConstraintValidator implements ConstraintValidatorInterface
{
    private readonly ViolationListInterface $violations;

    /**
     * @psalm-api
     */
    public function __construct()
    {
        $this->violations = new ViolationList();
    }

    public function getViolations(): ViolationListInterface
    {
        return $this->violations;
    }

    abstract public function validate(ValidatedValueInterface $value, ConstraintInterface $constraint): void;

    protected function addViolation(ViolationInterface $violation): void
    {
        $this->violations->add($violation);
    }
}
