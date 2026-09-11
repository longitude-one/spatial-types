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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPoint as ThreeDimensionalMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point as ThreeDimensionalPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon as ThreeDimensionalPolygon;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Covers boundary and compatibility rules shared by spatial aggregates.
 *
 * @internal
 *
 * @coversNothing
 */
class AggregateBoundaryAndValidationTest extends TestCase
{
    /**
     * Aggregate accessors support both circular and negative indexing.
     */
    public function testAggregateAccessorsNormalizePositiveAndNegativeIndexes(): void
    {
        $first = new Point(1, 2);
        $second = new Point(3, 4);
        $third = new Point(5, 6);
        $firstLine = new LineString([$first, $second]);
        $secondLine = new LineString([$second, $third]);
        $firstPolygon = new Polygon([new LineString([$first, $second, $third, $first])]);
        $secondPolygon = new Polygon([new LineString([$second, $third, $first, $second])]);

        $multiLineString = new MultiLineString([$firstLine, $secondLine]);
        static::assertSame($firstLine, $multiLineString->getLineString(2));
        static::assertSame($secondLine, $multiLineString->getLineString(-1));

        $polygon = new Polygon([$firstPolygon->getRing(0), $secondPolygon->getRing(0)]);
        static::assertSame($polygon->getRings(), $polygon->getElements());
        static::assertSame($firstPolygon->getRing(0), $polygon->getRing(2));
        static::assertSame($secondPolygon->getRing(0), $polygon->getRing(-1));

        $multiPolygon = new MultiPolygon([$firstPolygon, $secondPolygon]);
        static::assertFalse($multiPolygon->isEmpty());
        static::assertSame($firstPolygon, $multiPolygon->getPolygon(2));
        static::assertSame($secondPolygon, $multiPolygon->getPolygon(-1));
    }

    /**
     * Construction rejects values that cannot be members of their aggregate.
     */
    public function testAggregatesRejectInvalidMemberValues(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('objects implementing SpatialInterface');

        (new \ReflectionClass(GeometryCollection::class))->newInstance(0, [new \stdClass()]);
    }

    /**
     * Empty aggregates reject reads and immutable replacements consistently.
     *
     * @param \Closure $operation public operation to invoke on an empty aggregate
     */
    #[DataProvider('provideOperationsOnEmptyAggregates')]
    public function testEmptyAggregatesRejectReadAndReplacementOperations(\Closure $operation): void
    {
        self::expectException(OutOfBoundsException::class);
        $operation();
    }

    /**
     * @return \Generator<string, array{0: \Closure}, null, void>
     */
    public static function provideOperationsOnEmptyAggregates(): \Generator
    {
        yield 'line string point replacement' => [static fn () => (new LineString([]))->withPoint(0, Coordinates::xy(1, 2))];

        yield 'multi-point point replacement' => [static fn () => (new MultiPoint([]))->withPoint(0, Coordinates::xy(1, 2))];

        yield 'multi-line string accessor' => [static fn () => (new MultiLineString([]))->getLineString(0)];

        yield 'multi-line string replacement' => [static fn () => (new MultiLineString([]))->withLineString(0, [[1, 2], [3, 4]])];

        yield 'multi-line string point replacement' => [static fn () => (new MultiLineString([]))->withPoint(0, 0, Coordinates::xy(1, 2))];

        yield 'polygon accessor' => [static fn () => (new Polygon([]))->getRing(0)];

        yield 'polygon ring replacement' => [static fn () => (new Polygon([]))->withRing(0, [[0, 0], [1, 1], [0, 0]])];

        yield 'polygon point replacement' => [static fn () => (new Polygon([]))->withPoint(0, 0, Coordinates::xy(1, 2))];

        yield 'multi-polygon accessor' => [static fn () => (new MultiPolygon([]))->getPolygon(0)];

        yield 'multi-polygon replacement' => [static fn () => (new MultiPolygon([]))->withPolygon(0, [[[0, 0], [1, 1], [0, 0]]])];

        yield 'multi-polygon ring replacement' => [static fn () => (new MultiPolygon([]))->withRing(0, 0, [[0, 0], [1, 1], [0, 0]])];

        yield 'multi-polygon point replacement' => [static fn () => (new MultiPolygon([]))->withPoint(0, 0, 0, Coordinates::xy(1, 2))];

        yield 'collection element replacement' => [static fn () => (new GeometryCollection())->withElement(0, new Point(1, 2))];
    }

    /**
     * A multi-line string rejects line strings from another spatial family.
     */
    public function testMultiLineStringRejectsAnIncompatibleFamily(): void
    {
        $lineString = new GeographicLineString([
            new GeographicPoint(1, 2),
            new GeographicPoint(3, 4),
        ]);

        self::expectException(InvalidFamilyException::class);
        self::expectExceptionMessageIsOrContains('line string family is not compatible');

        new MultiLineString([$lineString]);
    }

    /**
     * @param \Closure $operation construction that violates one multi-polygon compatibility rule
     * @param string   $exception expected compatibility exception
     *
     * @phpstan-param class-string<\Throwable> $exception
     */
    #[DataProvider('provideIncompatiblePolygons')]
    public function testMultiPolygonRejectsIncompatiblePolygons(\Closure $operation, string $exception): void
    {
        self::expectException($exception);
        $operation();
    }

    /**
     * @return \Generator<string, array{0: \Closure, 1: string}, null, void>
     *
     * @phpstan-return \Generator<string, array{0: \Closure, 1: class-string<\Throwable>}, null, void>
     */
    public static function provideIncompatiblePolygons(): \Generator
    {
        $ring = [[0, 0], [1, 1], [2, 2], [0, 0]];

        yield 'different SRID' => [
            static fn () => new MultiPolygon([new Polygon([$ring], 4327)], 4326),
            InvalidSridException::class,
        ];

        yield 'different family' => [
            static fn () => new MultiPolygon([new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon([$ring])]),
            InvalidFamilyException::class,
        ];

        yield 'different dimension' => [
            static fn () => new MultiPolygon([new ThreeDimensionalPolygon([[[0, 0, 1], [1, 1, 2], [2, 2, 3], [0, 0, 1]]])]),
            InvalidDimensionException::class,
        ];
    }

    /**
     * A polygon can contain only rings from its own family.
     */
    public function testPolygonRejectsAnIncompatibleRingFamily(): void
    {
        $ring = new GeographicLineString([
            new GeographicPoint(1, 2),
            new GeographicPoint(3, 4),
            new GeographicPoint(1, 2),
        ]);

        self::expectException(InvalidFamilyException::class);
        self::expectExceptionMessageIsOrContains('ring family is not compatible');

        new Polygon([$ring]);
    }

    /**
     * A polygon rejects values that are neither rings nor coordinate arrays.
     */
    public function testPolygonRejectsAnInvalidRingValue(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('objects implementing LineStringInterface');

        (new \ReflectionClass(Polygon::class))->newInstance([new \stdClass()]);
    }

    /**
     * Replacing an interior ring point must not alter the two closure endpoints.
     */
    public function testPolygonWithPointLeavesClosureEndpointsUntouchedForAnInteriorPoint(): void
    {
        $polygon = new Polygon([[[0, 0], [10, 0], [0, 10], [0, 0]]]);
        $replacement = $polygon->withPoint(0, 1, Coordinates::xy(8, 9));

        static::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]]], $polygon->toArray());
        static::assertSame([[[0, 0], [8, 9], [0, 10], [0, 0]]], $replacement->toArray());
        static::assertTrue($replacement->getRing(0)->getPoint(0)->equalsTo($replacement->getRing(0)->getPoint(-1)));
    }

    /**
     * XYZ multi-points accept both point objects and indexed coordinates.
     */
    public function testThreeDimensionalMultiPointConstructsItsPoints(): void
    {
        $multiPoint = new ThreeDimensionalMultiPoint([
            new ThreeDimensionalPoint(1, 2, 3),
            [4, 5, 6],
        ]);

        static::assertSame([[1, 2, 3], [4, 5, 6]], $multiPoint->toArray());
    }
}
