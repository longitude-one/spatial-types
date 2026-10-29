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
        static::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertEquals(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertEquals(SpatialModelEnum::GEOMETRY, $other->getFamily());
        static::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $other->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $shared->getPoint(0)->getFamily());
        static::assertEquals(SpatialModelEnum::GEOMETRY, $shared->getFamily());
        static::assertSame([$point, $other], $line->getPoints());
        static::assertSame([[0, null], [3, 4]], $line->toArray());
        static::assertSame([[0, null]], $shared->toArray());
    }

    /** CircularString setSpatialReference propagates by default through shared references. */
    public function testCircularStringSpatialReferencePropagationAndLocalSetting(): void
    {
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, SpatialReference::epsg(4326));
        $other = new Point(3, 4, SpatialReference::epsg(4326));
        $line = new CircularString([$point, $other], SpatialReference::epsg(4326));
        $shared = new CircularString([$point], 4326);
        static::assertSame($line, $line->setSpatialReference($reference, false));
        static::assertSame($reference, $line->getSpatialReference());
        static::assertEquals(SpatialReference::epsg(4326), $point->getSpatialReference());
        static::assertEquals(SpatialReference::epsg(4326), $other->getSpatialReference());
        static::assertSame($line, $line->setSpatialReference($reference));
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame($reference, $other->getSpatialReference());
        static::assertSame($reference, $shared->getPoint(0)->getSpatialReference());
        static::assertEquals(SpatialReference::fromSrid(4326), $shared->getSpatialReference());
        static::assertSame([$point, $other], $line->getPoints());
        static::assertSame([[0, null], [3, 4]], $line->toArray());
        static::assertSame([[0, null]], $shared->toArray());
    }

    /** CircularString setSrid propagates by default through shared references. */
    public function testCircularStringSridPropagationAndLocalSetting(): void
    {
        $point = new Point(0, null, 4326);
        $other = new Point(3, 4, 4326);
        $line = new CircularString([$point, $other], 4326);
        $shared = new CircularString([$point], 4326);
        static::assertSame($line, $line->setSrid(2154, false));
        static::assertSame(2154, $line->getSrid());
        static::assertEquals(4326, $point->getSrid());
        static::assertEquals(4326, $other->getSrid());
        static::assertSame($line, $line->setSrid(2154));
        static::assertSame(2154, $point->getSrid());
        static::assertSame(2154, $other->getSrid());
        static::assertSame(2154, $shared->getPoint(0)->getSrid());
        static::assertEquals(4326, $shared->getSrid());
        static::assertSame([$point, $other], $line->getPoints());
        static::assertSame([[0, null], [3, 4]], $line->toArray());
        static::assertSame([[0, null]], $shared->toArray());
    }

    /** CircularString XY preserves supplied nullable ordinates and family. */
    public function testCircularStringXyCoordinatesAndFamily(): void
    {
        $line = new CircularString([[0, null]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null]], $line->toArray());
        static::assertInstanceOf(Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XY, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null]]));
        static::assertSame([[0, null]], $line->toArray());
        $point = new Point(0, null);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null], [0, null]], $line->toArray());
    }

    /** CircularString XYM preserves supplied nullable ordinates and family. */
    public function testCircularStringXymCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\CircularString([[0, null, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 4]], $line->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XYM, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null, 4]]));
        static::assertSame([[0, null, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(0, null, 4);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null, 4], [0, null, 4]], $line->toArray());
    }

    /** CircularString XYZ preserves supplied nullable ordinates and family. */
    public function testCircularStringXyzCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\CircularString([[0, null, 3]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 3]], $line->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XYZ, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3]]));
        static::assertSame([[0, null, 3]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(0, null, 3);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null, 3], [0, null, 3]], $line->toArray());
    }

    /** CircularString XYZM preserves supplied nullable ordinates and family. */
    public function testCircularStringXyzmCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\CircularString([[0, null, 3, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 3, 4]], $line->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XYZM, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3, 4]]));
        static::assertSame([[0, null, 3, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(0, null, 3, 4);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null, 3, 4], [0, null, 3, 4]], $line->toArray());
    }

    /** LineString setFamily propagates by default through shared references. */
    public function testLineStringFamilyPropagationAndLocalSetting(): void
    {
        $point = new Point(0, null, 4326);
        $other = new Point(3, 4, 4326);
        $line = new LineString([$point, $other], 4326);
        $shared = new LineString([$point], 4326);
        static::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertEquals(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertEquals(SpatialModelEnum::GEOMETRY, $other->getFamily());
        static::assertSame($line, $line->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $other->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $shared->getPoint(0)->getFamily());
        static::assertEquals(SpatialModelEnum::GEOMETRY, $shared->getFamily());
        static::assertSame([$point, $other], $line->getPoints());
        static::assertSame([[0, null], [3, 4]], $line->toArray());
        static::assertSame([[0, null]], $shared->toArray());
    }

    /** LineString setSpatialReference propagates by default through shared references. */
    public function testLineStringSpatialReferencePropagationAndLocalSetting(): void
    {
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, SpatialReference::epsg(4326));
        $other = new Point(3, 4, SpatialReference::epsg(4326));
        $line = new LineString([$point, $other], SpatialReference::epsg(4326));
        $shared = new LineString([$point], 4326);
        static::assertSame($line, $line->setSpatialReference($reference, false));
        static::assertSame($reference, $line->getSpatialReference());
        static::assertEquals(SpatialReference::epsg(4326), $point->getSpatialReference());
        static::assertEquals(SpatialReference::epsg(4326), $other->getSpatialReference());
        static::assertSame($line, $line->setSpatialReference($reference));
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame($reference, $other->getSpatialReference());
        static::assertSame($reference, $shared->getPoint(0)->getSpatialReference());
        static::assertEquals(SpatialReference::fromSrid(4326), $shared->getSpatialReference());
        static::assertSame([$point, $other], $line->getPoints());
        static::assertSame([[0, null], [3, 4]], $line->toArray());
        static::assertSame([[0, null]], $shared->toArray());
    }

    /** LineString setSrid propagates by default through shared references. */
    public function testLineStringSridPropagationAndLocalSetting(): void
    {
        $point = new Point(0, null, 4326);
        $other = new Point(3, 4, 4326);
        $line = new LineString([$point, $other], 4326);
        $shared = new LineString([$point], 4326);
        static::assertSame($line, $line->setSrid(2154, false));
        static::assertSame(2154, $line->getSrid());
        static::assertEquals(4326, $point->getSrid());
        static::assertEquals(4326, $other->getSrid());
        static::assertSame($line, $line->setSrid(2154));
        static::assertSame(2154, $point->getSrid());
        static::assertSame(2154, $other->getSrid());
        static::assertSame(2154, $shared->getPoint(0)->getSrid());
        static::assertEquals(4326, $shared->getSrid());
        static::assertSame([$point, $other], $line->getPoints());
        static::assertSame([[0, null], [3, 4]], $line->toArray());
        static::assertSame([[0, null]], $shared->toArray());
    }

    /** LineString XY preserves supplied nullable ordinates and family. */
    public function testLineStringXyCoordinatesAndFamily(): void
    {
        $line = new LineString([[0, null]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null]], $line->toArray());
        static::assertInstanceOf(Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XY, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null]]));
        static::assertSame([[0, null]], $line->toArray());
        $point = new Point(0, null);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null], [0, null]], $line->toArray());
    }

    /** LineString XYM preserves supplied nullable ordinates and family. */
    public function testLineStringXymCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, null, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 4]], $line->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XYM, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null, 4]]));
        static::assertSame([[0, null, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(0, null, 4);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null, 4], [0, null, 4]], $line->toArray());
    }

    /** LineString XYZ preserves supplied nullable ordinates and family. */
    public function testLineStringXyzCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, null, 3]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 3]], $line->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XYZ, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3]]));
        static::assertSame([[0, null, 3]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(0, null, 3);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null, 3], [0, null, 3]], $line->toArray());
    }

    /** LineString XYZM preserves supplied nullable ordinates and family. */
    public function testLineStringXyzmCoordinatesAndFamily(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, null, 3, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 3, 4]], $line->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $line->getPoint(0));
        static::assertSame(CoordinateDimensionEnum::XYZM, $line->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null, 3, 4]]));
        static::assertSame([[0, null, 3, 4]], $line->toArray());
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(0, null, 3, 4);
        static::assertSame($line, $line->addPoint($point)->setPoint(0, $point)->insertPoint(2, $point)->removePoint(1));
        static::assertSame([$point, $point], $line->getPoints());
        static::assertSame([[0, null, 3, 4], [0, null, 3, 4]], $line->toArray());
    }
}
