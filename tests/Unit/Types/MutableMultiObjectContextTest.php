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
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
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
class MutableMultiObjectContextTest extends TestCase
{
    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiLineStringFamily(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiLineString([new LineString([$point], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        self::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        self::assertSame($point, $aggregate->getElement(0)->getPoint(0));
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiLineStringSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiLineString([new LineString([$point], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        self::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        self::assertNull($point->getSpatialReference()->authority);
        self::assertSame($point, $aggregate->getElement(0)->getPoint(0));
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        self::assertSame('EPSG', $point->getSpatialReference()->authority);
        self::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiLineStringSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiLineString([new LineString([$point], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSrid(2154, false));
        self::assertSame(2154, $aggregate->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($point, $aggregate->getElement(0)->getPoint(0));
        self::assertSame($aggregate, $aggregate->setSrid(2154));
        self::assertSame(2154, $point->getSrid());
        self::assertSame(2154, $owner->getElement(0)->getSrid());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPointFamily(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPoint([$point], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        self::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        self::assertSame($point, $aggregate->getElement(0));
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPointSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPoint([$point], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        self::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        self::assertNull($point->getSpatialReference()->authority);
        self::assertSame($point, $aggregate->getElement(0));
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        self::assertSame('EPSG', $point->getSpatialReference()->authority);
        self::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPointSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPoint([$point], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSrid(2154, false));
        self::assertSame(2154, $aggregate->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($point, $aggregate->getElement(0));
        self::assertSame($aggregate, $aggregate->setSrid(2154));
        self::assertSame(2154, $point->getSrid());
        self::assertSame(2154, $owner->getElement(0)->getSrid());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPolygonFamily(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        self::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        self::assertSame($point, $aggregate->getElement(0)->getRing(0)->getPoint(0));
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPolygonSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        self::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        self::assertNull($point->getSpatialReference()->authority);
        self::assertSame($point, $aggregate->getElement(0)->getRing(0)->getPoint(0));
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        self::assertSame('EPSG', $point->getSpatialReference()->authority);
        self::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPolygonSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSrid(2154, false));
        self::assertSame(2154, $aggregate->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($point, $aggregate->getElement(0)->getRing(0)->getPoint(0));
        self::assertSame($aggregate, $aggregate->setSrid(2154));
        self::assertSame(2154, $point->getSrid());
        self::assertSame(2154, $owner->getElement(0)->getSrid());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testSpatialCollectionFamily(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new SpatialCollection([new SpatialCollection([new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        self::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        self::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testSpatialCollectionSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new SpatialCollection([new SpatialCollection([new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        self::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        self::assertNull($point->getSpatialReference()->authority);
        self::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        self::assertSame('EPSG', $point->getSpatialReference()->authority);
        self::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testSpatialCollectionSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new SpatialCollection([new SpatialCollection([new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        self::assertSame($aggregate, $aggregate->setSrid(2154, false));
        self::assertSame(2154, $aggregate->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($aggregate, $aggregate->setSrid(2154));
        self::assertSame(2154, $point->getSrid());
        self::assertSame(2154, $owner->getElement(0)->getSrid());
        self::assertSame(0, $owner->getSrid());
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }
}
