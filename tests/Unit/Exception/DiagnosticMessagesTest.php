<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024-2026
 * Copyright Longitude One 2024-2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Exception;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Validator\DimensionValidation;
use LongitudeOne\SpatialTypes\Validator\FamilyValidation;
use LongitudeOne\SpatialTypes\Validator\SpatialReferenceValidation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class DiagnosticMessagesTest extends TestCase
{
    /**
     * Invalid coordinates must retain a safe representation in the outer exception.
     *
     * @param string $value    Untrusted input
     * @param string $expected Expected safe representation
     */
    #[DataProvider('valueProvider')]
    public function testCoordinate(string $value, string $expected): void
    {
        try {
            new Point($value, 0);
            static::fail('An invalid coordinate must be rejected.');
        } catch (SpatialTypeExceptionInterface $exception) {
            static::assertSame('Invalid coordinate value, got "'.$expected.'".', $exception->getMessage());
            static::assertNotNull($exception->getPrevious());
        }
    }

    /**
     * Custom dimension messages are untrusted diagnostic input.
     *
     * @param string $value    Untrusted input
     * @param string $expected Expected safe representation
     */
    #[DataProvider('valueProvider')]
    public function testDimension(string $value, string $expected): void
    {
        $this->expectExceptionMessage($expected);
        DimensionValidation::assertSame(CoordinateDimensionEnum::XYZ, new Point(1, 2), $value);
    }

    /**
     * Custom family messages are untrusted diagnostic input.
     *
     * @param string $value    Untrusted input
     * @param string $expected Expected safe representation
     */
    #[DataProvider('valueProvider')]
    public function testFamily(string $value, string $expected): void
    {
        $this->expectExceptionMessage($expected);
        FamilyValidation::assertSame(SpatialModelEnum::GEOGRAPHY, new Point(1, 2), $value);
    }

    /**
     * Public exception creation must escape the method name.
     *
     * @param string $value    Untrusted input
     * @param string $expected Expected safe representation
     */
    #[DataProvider('valueProvider')]
    public function testMethod(string $value, string $expected): void
    {
        $exception = BadMethodCallException::create($value, CoordinateDimensionEnum::XY);
        static::assertSame('The method "'.$expected.'" cannot be called with a spatial object with dimensions "XY".', $exception->getMessage());
    }

    /**
     * Check all range-error translations without changing the parsed input.
     *
     * @param string $value      Untrusted coordinate
     * @param string $expected   Expected exception message
     * @param bool   $geographic Whether to enforce geographic ranges
     */
    #[DataProvider('rangeProvider')]
    public function testRange(string $value, string $expected, bool $geographic): void
    {
        try {
            $pointClass = $geographic ? \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point::class : Point::class;
            new $pointClass($value, 0);
            static::fail('An out-of-range coordinate must be rejected.');
        } catch (SpatialTypeExceptionInterface $exception) {
            static::assertSame($expected, $exception->getMessage());
        }
    }

    /**
     * Range errors must also format parser input and geographic coordinate strings.
     *
     * @return iterable<string, array{string, string, bool}>
     */
    public static function rangeProvider(): iterable
    {
        yield 'latitude' => ["91N\n", 'Out of range latitude value, latitude must be between -90 and 90, got "91N\n".', false];

        yield 'longitude' => ["181E\n", 'Out of range longitude value, longitude must be between -180 and 180, got "181E\n".', false];

        yield 'minutes' => ["12°60'N\n", 'Out of range minute value, minute must be between 0 and 59, got "12°60\'N\n".', false];

        yield 'seconds' => ["12°30'60\"N\n", 'Out of range second value, second must be between 0 and 59, got "12°30\'60"N\n".', false];

        yield 'geographic longitude' => ["181\n", 'Out of range longitude value, longitude must be between -180 and 180, got "181\n".', true];
    }

    /**
     * Member labels must be formatted before interpolation into the SRID message.
     *
     * @param string $value    Untrusted input
     * @param string $expected Expected safe representation
     */
    #[DataProvider('valueProvider')]
    public function testReference(string $value, string $expected): void
    {
        $this->expectExceptionMessage('The '.$expected.' spatial reference is not compatible with the spatial reference of this spatial value.');
        SpatialReferenceValidation::assertSame(new SpatialReference(4326), new Point(1, 2), $value);
    }

    /**
     * Untrusted diagnostic values and their visible representations.
     *
     * @return iterable<string, array{string, string}>
     */
    public static function valueProvider(): iterable
    {
        yield 'controls' => ["bad\0\t\r\n\x1B[31m", 'bad\0\t\r\n\u{001B}[31m'];

        yield 'unicode' => ["é\u{0085}\u{202E}\u{2028}\u{2029}", 'é\u{0085}\u{202E}\u{2028}\u{2029}'];

        yield 'invalid UTF-8' => ["é\xFF", 'é\xFF'];

        yield 'long value' => [str_repeat('é', 2050), str_repeat('é', 2047).'…'];
    }
}
