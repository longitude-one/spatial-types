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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use PHPUnit\Framework\TestCase;

/** Mutable aggregate contracts.
 * @internal
 *
 * @coversNothing
 */
class MutableMultiObjectOwnershipTest extends TestCase
{
    /** Complete array replacement detaches previous members without changing their other owners. */
    public function testCoordinateReplacementCreatesNewMembers(): void
    {
        $point = new Point(1, null, 4326);
        $line = new LineString([$point], 4326);
        $polygon = new Polygon([$line], 4326);
        $points = new MultiPoint([$point], 2154, SpatialModelEnum::GEOGRAPHY);
        $lines = new MultiLineString([$line], 2154, SpatialModelEnum::GEOGRAPHY);
        $polygons = new MultiPolygon([$polygon], 2154, SpatialModelEnum::GEOGRAPHY);
        $owner = new SpatialCollection([$point, $line, $polygon]);
        static::assertSame($points, $points->setElements([[7, null]]));
        static::assertSame($lines, $lines->setElements([[[8, null]]]));
        static::assertSame($polygons, $polygons->setElements([[[[9, null]]]]));
        static::assertSame([[7, null]], $points->toArray());
        static::assertSame([[[8, null]]], $lines->toArray());
        static::assertSame([[[[9, null]]]], $polygons->toArray());
        static::assertSame([[1, null], [[1, null]], [[[1, null]]]], $owner->toArray());
        static::assertSame([$point, $line, $polygon], $owner->getElements());
        static::assertSame(2154, $points->getElement(0)->getSrid());
        static::assertSame(2154, $lines->getElement(0)->getPoint(0)->getSrid());
        static::assertSame(2154, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $points->getElement(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $lines->getElement(0)->getPoint(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
    }

    /** Immutable multi-objects reject new mutable members without conversion. */
    public function testImmutableAggregateStillRejectsMutableMembers(): void
    {
        $point = new Point(1, null, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(MultiPointImmutable::class))->newInstance([$point]);
        } finally {
            static::assertSame([1, null], $point->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** Coordinate-array paths reject immutable descendants instead of converting them. */
    public function testImmutableNestedPoint(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(MultiLineString::class))->newInstance([[$immutable]]);
        } finally {
            static::assertSame([1, 2], $immutable->toArray());
            static::assertSame(4326, $immutable->getSrid());
        }
    }

    /** Construction rejects immutable members without altering their source. */
    public function testMultiLineStringConstructorRejection(): void
    {
        $immutable = new LineStringImmutable([[1, 2]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(MultiLineString::class))->newInstance([$immutable]);
        } finally {
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }

    /** Construction rejects immutable members without altering their source. */
    public function testMultiPointConstructorRejection(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(MultiPoint::class))->newInstance([$immutable]);
        } finally {
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }

    /** Construction rejects immutable members without altering their source. */
    public function testMultiPolygonConstructorRejection(): void
    {
        $immutable = new PolygonImmutable([[[1, 2]]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(MultiPolygon::class))->newInstance([$immutable]);
        } finally {
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }

    /** Supplied mutable objects keep their own context and layout until explicitly changed. */
    public function testSharedMismatchedContext(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1, null, 7, 4326, SpatialModelEnum::GEOGRAPHY);
        $points = new MultiPoint([$point], 2154);
        static::assertSame($point, $points->getElement(0));
        static::assertSame([[1, null, 7]], $points->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $points->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $points->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($points, $points->setFamily(SpatialModelEnum::GEOMETRY));
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame([1, null, 7], $point->toArray());
    }

    /** Construction rejects immutable members without altering their source. */
    public function testSpatialCollectionConstructorRejection(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(SpatialCollection::class))->newInstance([$immutable]);
        } finally {
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }
}
