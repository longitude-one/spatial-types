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
        self::assertSame($points, $points->setElements([[7, null]]));
        self::assertSame($lines, $lines->setElements([[[8, null]]]));
        self::assertSame($polygons, $polygons->setElements([[[[9, null]]]]));
        self::assertSame([[7, null]], $points->toArray());
        self::assertSame([[[8, null]]], $lines->toArray());
        self::assertSame([[[[9, null]]]], $polygons->toArray());
        self::assertSame([[1, null], [[1, null]], [[[1, null]]]], $owner->toArray());
        self::assertSame([$point, $line, $polygon], $owner->getElements());
        self::assertSame(2154, $points->getElement(0)->getSrid());
        self::assertSame(2154, $lines->getElement(0)->getPoint(0)->getSrid());
        self::assertSame(2154, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $points->getElement(0)->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $lines->getElement(0)->getPoint(0)->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
    }

    /** Immutable multi-objects reject new mutable members without conversion. */
    public function testImmutableAggregateStillRejectsMutableMembers(): void
    {
        $point = new Point(1, null, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(MultiPointImmutable::class)->newInstance([$point]);
        } finally {
            self::assertSame([1, null], $point->toArray());
            self::assertSame(4326, $point->getSrid());
        }
    }

    /** Coordinate-array paths reject immutable descendants instead of converting them. */
    public function testImmutableNestedPoint(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(MultiLineString::class)->newInstance([[$immutable]]);
        } finally {
            self::assertSame([1, 2], $immutable->toArray());
            self::assertSame(4326, $immutable->getSrid());
        }
    }

    /** Construction rejects immutable members without altering their source. */
    public function testMultiLineStringConstructorRejection(): void
    {
        $immutable = new LineStringImmutable([[1, 2]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(MultiLineString::class)->newInstance([$immutable]);
        } finally {
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }

    /** Construction rejects immutable members without altering their source. */
    public function testMultiPointConstructorRejection(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(MultiPoint::class)->newInstance([$immutable]);
        } finally {
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }

    /** Construction rejects immutable members without altering their source. */
    public function testMultiPolygonConstructorRejection(): void
    {
        $immutable = new PolygonImmutable([[[1, 2]]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(MultiPolygon::class)->newInstance([$immutable]);
        } finally {
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }

    /** Supplied mutable objects keep their own family and reference until explicitly changed. */
    public function testSharedMismatchedFamilyAndReference(): void
    {
        $point = new Point(1, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $points = new MultiPoint([$point], 2154);
        self::assertSame($point, $points->getElement(0));
        self::assertSame([[1, null]], $points->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $points->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $points->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($points, $points->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame([1, null], $point->toArray());
    }

    /** Construction rejects immutable members without altering their source. */
    public function testSpatialCollectionConstructorRejection(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(SpatialCollection::class)->newInstance([$immutable]);
        } finally {
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(SpatialModelEnum::GEOMETRY, $immutable->getFamily());
        }
    }
}
