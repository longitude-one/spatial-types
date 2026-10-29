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
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        static::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        static::assertSame($point, $aggregate->getElement(0)->getPoint(0));
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiLineStringSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiLineString([new LineString([$point], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        static::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        static::assertNull($point->getSpatialReference()->authority);
        static::assertSame($point, $aggregate->getElement(0)->getPoint(0));
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        static::assertSame('EPSG', $point->getSpatialReference()->authority);
        static::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiLineStringSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiLineString([new LineString([$point], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSrid(2154, false));
        static::assertSame(2154, $aggregate->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($point, $aggregate->getElement(0)->getPoint(0));
        static::assertSame($aggregate, $aggregate->setSrid(2154));
        static::assertSame(2154, $point->getSrid());
        static::assertSame(2154, $owner->getElement(0)->getSrid());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPointFamily(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPoint([$point], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        static::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        static::assertSame($point, $aggregate->getElement(0));
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPointSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPoint([$point], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        static::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        static::assertNull($point->getSpatialReference()->authority);
        static::assertSame($point, $aggregate->getElement(0));
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        static::assertSame('EPSG', $point->getSpatialReference()->authority);
        static::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPointSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPoint([$point], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSrid(2154, false));
        static::assertSame(2154, $aggregate->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($point, $aggregate->getElement(0));
        static::assertSame($aggregate, $aggregate->setSrid(2154));
        static::assertSame(2154, $point->getSrid());
        static::assertSame(2154, $owner->getElement(0)->getSrid());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPolygonFamily(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        static::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        static::assertSame($point, $aggregate->getElement(0)->getRing(0)->getPoint(0));
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPolygonSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        static::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        static::assertNull($point->getSpatialReference()->authority);
        static::assertSame($point, $aggregate->getElement(0)->getRing(0)->getPoint(0));
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        static::assertSame('EPSG', $point->getSpatialReference()->authority);
        static::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testMultiPolygonSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSrid(2154, false));
        static::assertSame(2154, $aggregate->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($point, $aggregate->getElement(0)->getRing(0)->getPoint(0));
        static::assertSame($aggregate, $aggregate->setSrid(2154));
        static::assertSame(2154, $point->getSrid());
        static::assertSame(2154, $owner->getElement(0)->getSrid());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testSpatialCollectionFamily(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new SpatialCollection([new SpatialCollection([new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $aggregate->getFamily());
        static::assertSame([SpatialModelEnum::GEOMETRY], [$point->getFamily()]);
        static::assertSame($aggregate, $aggregate->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getElement(0)->getFamily());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testSpatialCollectionSpatialReference(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new SpatialCollection([new SpatialCollection([new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154), false));
        static::assertSame('EPSG', $aggregate->getSpatialReference()->authority);
        static::assertNull($point->getSpatialReference()->authority);
        static::assertSame($aggregate, $aggregate->setSpatialReference(SpatialReference::epsg(2154)));
        static::assertSame('EPSG', $point->getSpatialReference()->authority);
        static::assertSame('EPSG', $owner->getElement(0)->getSpatialReference()->authority);
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }

    /** Both propagation settings preserve coordinates and shared instance identity. */
    public function testSpatialCollectionSrid(): void
    {
        $point = new Point(0, null, 4326);
        $aggregate = new SpatialCollection([new SpatialCollection([new MultiPolygon([new Polygon([new LineString([$point], 4326)], 4326)], 4326)], 4326)], 4326);
        $owner = new SpatialCollection([$point, $aggregate]);
        static::assertSame($aggregate, $aggregate->setSrid(2154, false));
        static::assertSame(2154, $aggregate->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($aggregate, $aggregate->setSrid(2154));
        static::assertSame(2154, $point->getSrid());
        static::assertSame(2154, $owner->getElement(0)->getSrid());
        static::assertSame(0, $owner->getSrid());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $aggregate->getDimension());
    }
}
