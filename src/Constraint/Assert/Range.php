<?php

declare(strict_types=1);

namespace Temkaa\SimpleValidator\Constraint\Assert;

use Attribute;
use Temkaa\SimpleValidator\Constraint\ConstraintInterface;
use Temkaa\SimpleValidator\Constraint\Validator\RangeValidator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Range implements ConstraintInterface
{
    /**
     * @psalm-api
     */
    public function __construct(
        public float|int|null $min = null,
        public float|int|null $max = null,
        public ?string $minMessage = null,
        public ?string $maxMessage = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getHandler(): string
    {
        return RangeValidator::class;
    }
}
