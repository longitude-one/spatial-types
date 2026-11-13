<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable as ThreeDimensionalMultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable as ThreeDimensionalPoint;
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
     * @return \Generator<string, array{0: \Closure}, null, void>
     */
    public static function provideOperationsOnEmptyAggregates(): \Generator
    {
        yield 'line string point replacement' => [static fn () => new LineStringImmutable([])->withPoint(0, Coordinates::xy(1, 2))];

        yield 'multi-point point replacement' => [static fn () => new MultiPointImmutable([])->withPoint(0, Coordinates::xy(1, 2))];

        yield 'multi-line string accessor' => [static fn () => new MultiLineStringImmutable([])->getLineString(0)];

        yield 'multi-line string replacement' => [static fn () => new MultiLineStringImmutable([])->withLineString(0, [[1, 2], [3, 4]])];

        yield 'multi-line string point replacement' => [static fn () => new MultiLineStringImmutable([])->withPoint(0, 0, Coordinates::xy(1, 2))];

        yield 'polygon accessor' => [static fn () => new PolygonImmutable([])->getRing(0)];

        yield 'polygon ring replacement' => [static fn () => new PolygonImmutable([])->withRing(0, [[0, 0], [1, 1], [0, 0]])];

        yield 'polygon point replacement' => [static fn () => new PolygonImmutable([])->withPoint(0, 0, Coordinates::xy(1, 2))];

        yield 'multi-polygon accessor' => [static fn () => new MultiPolygonImmutable([])->getPolygon(0)];

        yield 'multi-polygon replacement' => [static fn () => new MultiPolygonImmutable([])->withPolygon(0, [[[0, 0], [1, 1], [0, 0]]])];

        yield 'multi-polygon ring replacement' => [static fn () => new MultiPolygonImmutable([])->withRing(0, 0, [[0, 0], [1, 1], [0, 0]])];

        yield 'multi-polygon point replacement' => [static fn () => new MultiPolygonImmutable([])->withPoint(0, 0, 0, Coordinates::xy(1, 2))];

        yield 'collection element replacement' => [static fn () => new GeometryCollectionImmutable()->withElement(0, new PointImmutable(1, 2))];
    }

    /**
     * Aggregate accessors support both circular and negative indexing.
     */
    public function testAggregateAccessorsNormalizePositiveAndNegativeIndexes(): void
    {
        $first = new PointImmutable(1, 2);
        $second = new PointImmutable(3, 4);
        $third = new PointImmutable(5, 6);
        $firstLine = new LineStringImmutable([$first, $second]);
        $secondLine = new LineStringImmutable([$second, $third]);
        $firstPolygon = new PolygonImmutable([new LineStringImmutable([$first, $second, $third, $first])]);
        $secondPolygon = new PolygonImmutable([new LineStringImmutable([$second, $third, $first, $second])]);

        $multiLineString = new MultiLineStringImmutable([$firstLine, $secondLine]);
        self::assertSame($firstLine, $multiLineString->getLineString(2));
        self::assertSame($secondLine, $multiLineString->getLineString(-1));

        $polygon = new PolygonImmutable([$firstPolygon->getRing(0), $secondPolygon->getRing(0)]);
        self::assertSame($polygon->getRings(), $polygon->getElements());
        self::assertSame($firstPolygon->getRing(0), $polygon->getRing(2));
        self::assertSame($secondPolygon->getRing(0), $polygon->getRing(-1));

        $multiPolygon = new MultiPolygonImmutable([$firstPolygon, $secondPolygon]);
        self::assertSame($firstPolygon, $multiPolygon->getPolygon(2));
        self::assertSame($secondPolygon, $multiPolygon->getPolygon(-1));
    }

    /**
     * Construction rejects values that cannot be members of their aggregate.
     */
    public function testAggregatesRejectInvalidMemberValues(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('objects implementing ImmutableSpatialInterface');

        new \ReflectionClass(GeometryCollectionImmutable::class)->newInstance(0, [new \stdClass()]);
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
     * A polygon rejects values that are neither rings nor coordinate arrays.
     */
    public function testPolygonRejectsAnInvalidRingValue(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('objects implementing ImmutableLineStringInterface');

        new \ReflectionClass(PolygonImmutable::class)->newInstance([new \stdClass()]);
    }

    /**
     * Replacing an interior ring point must not alter the two closure endpoints.
     */
    public function testPolygonWithPointLeavesClosureEndpointsUntouchedForAnInteriorPoint(): void
    {
        $polygon = new PolygonImmutable([[[0, 0], [10, 0], [0, 10], [0, 0]]]);
        $replacement = $polygon->withPoint(0, 1, Coordinates::xy(8, 9));

        self::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]]], $polygon->toArray());
        self::assertSame([[[0, 0], [8, 9], [0, 10], [0, 0]]], $replacement->toArray());
        self::assertTrue($replacement->getRing(0)->getPoint(0)->equalsTo($replacement->getRing(0)->getPoint(-1)));
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

        self::assertSame([[1, 2, 3], [4, 5, 6]], $multiPoint->toArray());
    }
}
