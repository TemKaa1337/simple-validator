<?php

declare(strict_types=1);

namespace Tests\Unit\Constraint\Validator;

use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use stdClass;
use Temkaa\SimpleValidator\Constraint\Assert;
use Temkaa\SimpleValidator\Constraint\Validator\LessThanValidator;
use Temkaa\SimpleValidator\Exception\UnexpectedTypeException;
use Temkaa\SimpleValidator\Model\ValidatedValue;
use Temkaa\SimpleValidator\Validator;

final class LessThanValidatorTest extends AbstractValidatorTestCase
{
    public static function getDataForInvalidTest(): iterable
    {
        $object = new class {
            #[Assert\LessThan(threshold: 1, message: 'validation exception', allowEquality: true)]
            public int $test = 2;
        };
        yield [
            $object,
            [
                [
                    'message'      => 'validation exception',
                    'invalidValue' => 2,
                    'path'         => $object::class.'.test',
                ],
            ],
            1,
        ];

        $object = new class {
            #[Assert\LessThan(threshold: 1, message: 'validation exception')]
            public int $test = 1;
        };
        yield [
            $object,
            [
                [
                    'message'      => 'validation exception',
                    'invalidValue' => 1,
                    'path'         => $object::class.'.test',
                ],
            ],
            1,
        ];

        $object = new class {
            #[Assert\LessThan(threshold: 1, message: 'validation exception')]
            public float $test = 1.0;
        };
        yield [
            $object,
            [
                [
                    'message'      => 'validation exception',
                    'invalidValue' => 1.0,
                    'path'         => $object::class.'.test',
                ],
            ],
            1,
        ];

        $object = new class {
            #[Assert\LessThan(threshold: 1, message: 'validation exception')]
            public float $test = 2;
        };
        yield [
            $object,
            [
                [
                    'message'      => 'validation exception',
                    'invalidValue' => 2,
                    'path'         => $object::class.'.test',
                ],
            ],
            1,
        ];
    }

    public static function getDataForValidTest(): iterable
    {
        $object = new class {
            #[Assert\LessThan(threshold: 1, message: 'validation exception', allowEquality: true)]
            public int $test = 1;
        };
        yield [$object];

        $object = new class {
            #[Assert\LessThan(threshold: 1, message: 'validation exception')]
            public int $test = 0;
        };
        yield [$object];

        $object = new class {
            #[Assert\LessThan(threshold: 1, message: 'validation exception')]
            public float $test = 0.99;
        };
        yield [$object];

        $object = new class {
            #[Assert\LessThan(threshold: 1.01, message: 'validation exception', allowEquality: true)]
            public float $test = 1.01;
        };
        yield [$object];
    }

    public static function getDataForValidateWithUnsupportedValueTypeTest(): iterable
    {
        $object = new class {
            #[Assert\LessThan(threshold: 10, message: '')]
            public string $test = 'test';
        };
        yield [
            $object,
            UnexpectedTypeException::class,
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                'float|int',
                'string',
            ),
        ];

        $object = new class {
            #[Assert\LessThan(threshold: 10, message: '')]
            public bool $test = true;
        };
        yield [
            $object,
            UnexpectedTypeException::class,
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                'float|int',
                'boolean',
            ),
        ];

        $object = new class {
            /** @noinspection PropertyInitializationFlawsInspection */
            #[Assert\LessThan(threshold: 10, message: '')]
            public null $test = null;
        };
        yield [
            $object,
            UnexpectedTypeException::class,
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                'float|int',
                'NULL',
            ),
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    #[DataProvider('getDataForValidTest')]
    public function testValid(object $value): void
    {
        $errors = (new Validator())->validate($value);

        /** @psalm-suppress TypeDoesNotContainType */
        $this->assertEmpty($errors);
    }

    public function testValidateInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Unexpected argument type exception, expected "%s" but got "%s".',
                Assert\LessThan::class,
                Assert\Positive::class,
            ),
        );

        (new LessThanValidator())->validate(
            new ValidatedValue(new stdClass(), path: 'path', isInitialized: true),
            new Assert\Positive(message: ''),
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    #[DataProvider('getDataForValidateWithUnsupportedValueTypeTest')]
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
