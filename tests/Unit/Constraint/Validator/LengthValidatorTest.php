<?php

declare(strict_types=1);

namespace Tests\Unit\Constraint\Validator;

use Countable;
use stdClass;
use Stringable;
use Temkaa\SimpleValidator\Constraint\Assert;
use Temkaa\SimpleValidator\Constraint\ConstraintInterface;
use Temkaa\SimpleValidator\Constraint\Validator\LengthValidator;
use Temkaa\SimpleValidator\Constraint\ViolationInterface;
use Temkaa\SimpleValidator\Exception\InvalidConstraintConfigurationException;
use Temkaa\SimpleValidator\Exception\UnexpectedTypeException;
use Temkaa\SimpleValidator\Validator;

final class LengthValidatorTest extends AbstractValidatorTestCase
{
    public static function getDataForInvalidTest(): iterable
    {
        $object = new class {
            #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
            public string $test = '';
        };
        yield [$object, ''];

        $object = new class {
            #[Assert\Length(maxLength: 1, maxMessage: 'validation exception')]
            public string $test = 'aa';
        };
        yield [$object, 'aa'];

        $object = new class {
            #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
            public array $test = [];
        };
        yield [$object, []];

        $object = new class {
            #[Assert\Length(maxLength: 1, maxMessage: 'validation exception')]
            public array $test = ['test', 'test'];
        };
        yield [$object, ['test', 'test']];

        $countable = new class implements Countable {
            public function count(): int
            {
                return 0;
            }
        };
        $object = new class ($countable) {
            public function __construct(
                #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
                public readonly Countable $test,
            ) {
            }
        };
        yield [$object, $countable];

        $countable = new class implements Countable {
            public function count(): int
            {
                return 2;
            }
        };
        $object = new class ($countable) {
            public function __construct(
                #[Assert\Length(maxLength: 1, maxMessage: 'validation exception')]
                public readonly Countable $test,
            ) {
            }
        };
        yield [$object, $countable];

        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return '';
            }
        };
        $object = new class ($stringable) {
            public function __construct(
                #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
                public readonly Stringable $test,
            ) {
            }
        };
        yield [$object, $stringable];
    }

    public static function getDataForValidTest(): iterable
    {
        $object = new class {
            #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
            public string $test = 'a';
        };
        yield [$object];

        $object = new class {
            #[Assert\Length(minLength: 1, maxLength: 2, minMessage: 'validation exception', maxMessage: 'validation exception')]
            public string $test = 'aa';
        };
        yield [$object];

        $object = new class {
            #[Assert\Length(maxLength: 2, maxMessage: 'validation exception')]
            public string $test = 'aa';
        };
        yield [$object];

        $object = new class {
            #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
            public array $test = ['test1'];
        };
        yield [$object];

        $object = new class {
            #[Assert\Length(maxLength: 1, maxMessage: 'validation exception')]
            public array $test = ['test1'];
        };
        yield [$object];

        $object = new class {
            #[Assert\Length(minLength: 1, maxLength: 2, minMessage: 'validation exception', maxMessage: 'validation exception')]
            public array $test = ['test1'];
        };
        yield [$object];

        $countable = new class implements Countable {
            public function count(): int
            {
                return 1;
            }
        };
        $object = new class ($countable) {
            public function __construct(
                #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
                public readonly Countable $test,
            ) {
            }
        };
        yield [$object];

        $countable = new class implements Countable {
            public function count(): int
            {
                return 1;
            }
        };
        $object = new class ($countable) {
            public function __construct(
                #[Assert\Length(maxLength: 1, maxMessage: 'validation exception')]
                public readonly Countable $test,
            ) {
            }
        };
        yield [$object];

        $countable = new class implements Countable {
            public function count(): int
            {
                return 1;
            }
        };
        $object = new class ($countable) {
            public function __construct(
                #[Assert\Length(minLength: 1, maxLength: 2, minMessage: 'validation exception', maxMessage: 'validation exception')]
                public readonly Countable $test,
            ) {
            }
        };
        yield [$object];

        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'a';
            }
        };
        $object = new class ($stringable) {
            public function __construct(
                #[Assert\Length(minLength: 1, minMessage: 'validation exception')]
                public readonly Stringable $test,
            ) {
            }
        };
        yield [$object];
    }

    public static function getDataForValidateWithInvalidConstraintSettingsTest(): iterable
    {
        yield [
            InvalidConstraintConfigurationException::class,
            'Length constraint must have one of "minLength" or "maxLength" argument set.',
            new Assert\Length(),
        ];

        yield [
            InvalidConstraintConfigurationException::class,
            'Length constraint must have one of "minLength" or "maxLength" argument set.',
            new Assert\Length(minLength: 1, maxLength: 1),
        ];

        yield [
            InvalidConstraintConfigurationException::class,
            'Length constraint must have both "minLength" and "minMessage" arguments set.',
            new Assert\Length(minLength: 1, maxMessage: 'test'),
        ];

        yield [
            InvalidConstraintConfigurationException::class,
            'Length constraint must have both "maxLength" and "maxMessage" arguments set.',
            new Assert\Length(minLength: 1, maxLength: 1, minMessage: 'test'),
        ];

        yield [
            InvalidConstraintConfigurationException::class,
            'Argument "maxLength" of Length constraint must be equal or greater than "minLength" value.',
            new Assert\Length(minLength: 1, maxLength: 0, minMessage: 'test', maxMessage: 'test'),
        ];

        yield [
            InvalidConstraintConfigurationException::class,
            'Argument "minLength" of Length constraint must be equal or greater than 0.',
            new Assert\Length(minLength: -1, minMessage: 'test'),
        ];

        yield [
            InvalidConstraintConfigurationException::class,
            'Argument "maxLength" of Length constraint must be equal or greater than 0.',
            new Assert\Length(maxLength: -1, maxMessage: 'test'),
        ];
    }

    public static function getDataForValidateWithUnsupportedValueTypeTest(): iterable
    {
        $object = new class {
            #[Assert\Length(minLength: 1, minMessage: '')]
            public bool $test = true;
        };
        yield [
            $object,
            UnexpectedTypeException::class,
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                'array|\Countable|string|\Stringable',
                'boolean',
            ),
        ];

        $object = new class {
            #[Assert\Length(minLength: 1, minMessage: '')]
            public int $test = 1;
        };
        yield [
            $object,
            UnexpectedTypeException::class,
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                'array|\Countable|string|\Stringable',
                'integer',
            ),
        ];

        $object = new class {
            #[Assert\Length(minLength: 1, minMessage: '')]
            public float $test = 1.1;
        };
        yield [
            $object,
            UnexpectedTypeException::class,
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                'array|\Countable|string|\Stringable',
                'double',
            ),
        ];
    }

    /**
     * @dataProvider getDataForInvalidTest
     */
    public function testInvalid(object $value, mixed $invalidValue): void
    {
        $errors = (new Validator())->validate($value);

        $this->assertCount(1, $errors);
        /** @var ViolationInterface $error */
        foreach ($errors as $error) {
            self::assertEquals('validation exception', $error->getMessage());
            self::assertNull($error->getPath());
            self::assertEquals($invalidValue, $error->getInvalidValue());
        }
    }

    /**
     * @dataProvider getDataForValidTest
     */
    public function testValid(object $value): void
    {
        $errors = (new Validator())->validate($value);

        $this->assertEmpty($errors);
    }

    public function testValidateInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                Assert\Length::class,
                Assert\Positive::class,
            ),
        );

        (new LengthValidator())->validate(new stdClass(), new Assert\Positive(message: ''));
    }

    /**
     * @dataProvider getDataForValidateWithInvalidConstraintSettingsTest
     */
    public function testValidateWithInvalidConstraintSettings(
        string $exception,
        string $exceptionMessage,
        ConstraintInterface $constraint,
    ): void {
        $this->expectException($exception);
        $this->expectExceptionMessage($exceptionMessage);

        (new LengthValidator())->validate(new stdClass(), $constraint);
    }

    /**
     * @dataProvider getDataForValidateWithUnsupportedValueTypeTest
     */
    public function testValidateWithUnsupportedValueType(
        object $value,
        string $exception,
        string $exceptionMessage,
    ): void {
        $this->expectException($exception);
        $this->expectExceptionMessage($exceptionMessage);

        (new Validator())->validate($value);
    }
}
