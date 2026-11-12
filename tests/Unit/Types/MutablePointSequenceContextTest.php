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
use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/** Coordinate layouts and declared-context propagation.
 * @internal
 *
 * @coversNothing
 */
class MutablePointSequenceContextTest extends TestCase
{
    /** CircularString setFamily propagates by default through shared references. */
    public function testCircularStringFamilyPropagationAndLocalSetting(): void
    {
        $point = new Point(0, null, 4326);
        $other = new Point(3, 4, 4326);
        $line = new CircularString([$point, $other], 4326);
        $shared = new CircularString([$point], 4326);
        self::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertEquals(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertEquals(SpatialModelEnum::GEOMETRY, $other->getFamily());
        self::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $other->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $shared->getPoint(0)->getFamily());
        self::assertEquals(SpatialModelEnum::GEOMETRY, $shared->getFamily());
        self::assertSame([$point, $other], $line->getPoints());
        self::assertSame([[0, null], [3, 4]], $line->toArray());
        self::assertSame([[0, null]], $shared->toArray());
    }

    /** CircularString setSpatialReference propagates by default through shared references. */
    public function testCircularStringSpatialReferencePropagationAndLocalSetting(): void
    {
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, SpatialReference::epsg(4326));
        $other = new Point(3, 4, SpatialReference::epsg(4326));
        $line = new CircularString([$point, $other], SpatialReference::epsg(4326));
        $shared = new CircularString([$point], 4326);
        self::assertSame($line, $line->setSpatialReference($reference, false));
        self::assertSame($reference, $line->getSpatialReference());
        self::assertEquals(SpatialReference::epsg(4326), $point->getSpatialReference());
        self::assertEquals(SpatialReference::epsg(4326), $other->getSpatialReference());
        self::assertSame($line, $line->setSpatialReference($reference));
        self::assertSame($reference, $point->getSpatialReference());
        self::assertSame($reference, $other->getSpatialReference());
        self::assertSame($reference, $shared->getPoint(0)->getSpatialReference());
        self::assertEquals(SpatialReference::fromSrid(4326), $shared->getSpatialReference());
        self::assertSame([$point, $other], $line->getPoints());
        self::assertSame([[0, null], [3, 4]], $line->toArray());
        self::assertSame([[0, null]], $shared->toArray());
    }

    /** CircularString setSrid propagates by default through shared references. */
    public function testCircularStringSridPropagationAndLocalSetting(): void
    {
        $point = new Point(0, null, 4326);
        $other = new Point(3, 4, 4326);
        $line = new CircularString([$point, $other], 4326);
        $shared = new CircularString([$point], 4326);
        self::assertSame($line, $line->setSrid(2154, false));
        self::assertSame(2154, $line->getSrid());
        self::assertEquals(4326, $point->getSrid());
        self::assertEquals(4326, $other->getSrid());
        self::assertSame($line, $line->setSrid(2154));
        self::assertSame(2154, $point->getSrid());
        self::assertSame(2154, $other->getSrid());
        self::assertSame(2154, $shared->getPoint(0)->getSrid());
        self::assertEquals(4326, $shared->getSrid());
        self::assertSame([$point, $other], $line->getPoints());
        self::assertSame([[0, null], [3, 4]], $line->toArray());
        self::assertSame([[0, null]], $shared->toArray());
    }

    /** CircularString XY preserves supplied nullable ordinates and family. */
    public function testCircularStringXyCoordinatesAndFamily(): void
    {
        $line = new CircularString([[0, null]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null]], $line->toArray());
        self::assertInstanceOf(Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XY, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null]]));
        self::assertSame([[0, null]], $line->toArray());
        $point = new Point(0, null);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null], [0, null]], $line->toArray());
    }

    /** CircularString XYM preserves supplied nullable ordinates and family. */
    public function testCircularStringXymCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\CircularString([[0, null, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 4]], $line->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XYM, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null, 4]]));
        self::assertSame([[0, null, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(0, null, 4);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null, 4], [0, null, 4]], $line->toArray());
    }

    /** CircularString XYZ preserves supplied nullable ordinates and family. */
    public function testCircularStringXyzCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\CircularString([[0, null, 3]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 3]], $line->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XYZ, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3]]));
        self::assertSame([[0, null, 3]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(0, null, 3);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null, 3], [0, null, 3]], $line->toArray());
    }

    /** CircularString XYZM preserves supplied nullable ordinates and family. */
    public function testCircularStringXyzmCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\CircularString([[0, null, 3, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 3, 4]], $line->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XYZM, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3, 4]]));
        self::assertSame([[0, null, 3, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(0, null, 3, 4);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null, 3, 4], [0, null, 3, 4]], $line->toArray());
    }

    /** LineString setFamily propagates by default through shared references. */
    public function testLineStringFamilyPropagationAndLocalSetting(): void
    {
        $point = new Point(0, null, 4326);
        $other = new Point(3, 4, 4326);
        $line = new LineString([$point, $other], 4326);
        $shared = new LineString([$point], 4326);
        self::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertEquals(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertEquals(SpatialModelEnum::GEOMETRY, $other->getFamily());
        self::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $other->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $shared->getPoint(0)->getFamily());
        self::assertEquals(SpatialModelEnum::GEOMETRY, $shared->getFamily());
        self::assertSame([$point, $other], $line->getPoints());
        self::assertSame([[0, null], [3, 4]], $line->toArray());
        self::assertSame([[0, null]], $shared->toArray());
    }

    /** LineString setSpatialReference propagates by default through shared references. */
    public function testLineStringSpatialReferencePropagationAndLocalSetting(): void
    {
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, SpatialReference::epsg(4326));
        $other = new Point(3, 4, SpatialReference::epsg(4326));
        $line = new LineString([$point, $other], SpatialReference::epsg(4326));
        $shared = new LineString([$point], 4326);
        self::assertSame($line, $line->setSpatialReference($reference, false));
        self::assertSame($reference, $line->getSpatialReference());
        self::assertEquals(SpatialReference::epsg(4326), $point->getSpatialReference());
        self::assertEquals(SpatialReference::epsg(4326), $other->getSpatialReference());
        self::assertSame($line, $line->setSpatialReference($reference));
        self::assertSame($reference, $point->getSpatialReference());
        self::assertSame($reference, $other->getSpatialReference());
        self::assertSame($reference, $shared->getPoint(0)->getSpatialReference());
        self::assertEquals(SpatialReference::fromSrid(4326), $shared->getSpatialReference());
        self::assertSame([$point, $other], $line->getPoints());
        self::assertSame([[0, null], [3, 4]], $line->toArray());
        self::assertSame([[0, null]], $shared->toArray());
    }

    /** LineString setSrid propagates by default through shared references. */
    public function testLineStringSridPropagationAndLocalSetting(): void
    {
        $point = new Point(0, null, 4326);
        $other = new Point(3, 4, 4326);
        $line = new LineString([$point, $other], 4326);
        $shared = new LineString([$point], 4326);
        self::assertSame($line, $line->setSrid(2154, false));
        self::assertSame(2154, $line->getSrid());
        self::assertEquals(4326, $point->getSrid());
        self::assertEquals(4326, $other->getSrid());
        self::assertSame($line, $line->setSrid(2154));
        self::assertSame(2154, $point->getSrid());
        self::assertSame(2154, $other->getSrid());
        self::assertSame(2154, $shared->getPoint(0)->getSrid());
        self::assertEquals(4326, $shared->getSrid());
        self::assertSame([$point, $other], $line->getPoints());
        self::assertSame([[0, null], [3, 4]], $line->toArray());
        self::assertSame([[0, null]], $shared->toArray());
    }

    /** LineString XY preserves supplied nullable ordinates and family. */
    public function testLineStringXyCoordinatesAndFamily(): void
    {
        $line = new LineString([[0, null]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null]], $line->toArray());
        self::assertInstanceOf(Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XY, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null]]));
        self::assertSame([[0, null]], $line->toArray());
        $point = new Point(0, null);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null], [0, null]], $line->toArray());
    }

    /** LineString XYM preserves supplied nullable ordinates and family. */
    public function testLineStringXymCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, null, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 4]], $line->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XYM, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null, 4]]));
        self::assertSame([[0, null, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(0, null, 4);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null, 4], [0, null, 4]], $line->toArray());
    }

    /** LineString XYZ preserves supplied nullable ordinates and family. */
    public function testLineStringXyzCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, null, 3]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 3]], $line->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XYZ, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3]]));
        self::assertSame([[0, null, 3]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(0, null, 3);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null, 3], [0, null, 3]], $line->toArray());
    }

    /** LineString XYZM preserves supplied nullable ordinates and family. */
    public function testLineStringXyzmCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, null, 3, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 3, 4]], $line->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $line->getPoint(0));
        self::assertSame(CoordinateDimensionEnum::XYZM, $line->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3, 4]]));
        self::assertSame([[0, null, 3, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(0, null, 3, 4);
        self::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        self::assertSame([$point, $point], $line->getPoints());
        self::assertSame([[0, null, 3, 4], [0, null, 3, 4]], $line->toArray());
    }
}
