<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2 as XY;
use LongitudeOne\SpatialTypes\Types\Dimension3m as XYM;
use LongitudeOne\SpatialTypes\Types\Dimension3z as XYZ;
use LongitudeOne\SpatialTypes\Types\Dimension4zm as XYZM;
use PHPUnit\Framework\TestCase;

/** Regression coverage for #78.
 * @internal
 *
 * @coversNothing
 */
class MutableDimensionConstructionTest extends TestCase
{
    /** Matching objects retain identity and tuples retain the declared layout. */
    public function testXymatchingObjectsAndTuples(): void
    {
        $point = new XY\Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new XY\LineString([$point, [1, 2]], 2154);
        static::assertSame($point, $line->getPoint(0));
        static::assertSame([[1, 2], [1, 2]], $line->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $line->getPoint(1)->getDimension());
        static::assertInstanceOf(XY\Point::class, $line->getPoint(1));
        static::assertSame(2154, $line->getPoint(1)->getSrid());
        static::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[1, 2]]));
        static::assertSame([[1, 2]], $line->toArray());
    }

    /** Matching objects retain identity and tuples retain the declared layout. */
    public function testXymMatchingObjectsAndTuples(): void
    {
        $point = new XYM\Point(1, 2, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new XYM\LineString([$point, [1, 2, 4]], 2154);
        static::assertSame($point, $line->getPoint(0));
        static::assertSame([[1, 2, 4], [1, 2, 4]], $line->toArray());
        static::assertSame(CoordinateDimensionEnum::XYM, $line->getPoint(1)->getDimension());
        static::assertInstanceOf(XYM\Point::class, $line->getPoint(1));
        static::assertSame(2154, $line->getPoint(1)->getSrid());
        static::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[1, 2, 4]]));
        static::assertSame([[1, 2, 4]], $line->toArray());
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXymRejectsXyPoint(): void
    {
        $point = new XY\Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(SpatialTypeExceptionInterface::class);

        try {
            new XYM\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXymRejectsXyzmPoint(): void
    {
        $point = new XYZM\Point(1, 2, 3, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYM\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 3, 4], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXymRejectsXyzPoint(): void
    {
        $point = new XYZ\Point(1, 2, 3, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYM\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 3], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyRejectsXymPoint(): void
    {
        $point = new XYM\Point(1, 2, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XY\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 4], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyRejectsXyzmPoint(): void
    {
        $point = new XYZM\Point(1, 2, 3, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XY\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 3, 4], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyRejectsXyzPoint(): void
    {
        $point = new XYZ\Point(1, 2, 3, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XY\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 3], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Matching objects retain identity and tuples retain the declared layout. */
    public function testXyzmatchingObjectsAndTuples(): void
    {
        $point = new XYZ\Point(1, 2, 3, 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new XYZ\LineString([$point, [1, 2, 3]], 2154);
        static::assertSame($point, $line->getPoint(0));
        static::assertSame([[1, 2, 3], [1, 2, 3]], $line->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZ, $line->getPoint(1)->getDimension());
        static::assertInstanceOf(XYZ\Point::class, $line->getPoint(1));
        static::assertSame(2154, $line->getPoint(1)->getSrid());
        static::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[1, 2, 3]]));
        static::assertSame([[1, 2, 3]], $line->toArray());
    }

    /** Matching objects retain identity and tuples retain the declared layout. */
    public function testXyzmMatchingObjectsAndTuples(): void
    {
        $point = new XYZM\Point(1, 2, 3, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new XYZM\LineString([$point, [1, 2, 3, 4]], 2154);
        static::assertSame($point, $line->getPoint(0));
        static::assertSame([[1, 2, 3, 4], [1, 2, 3, 4]], $line->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $line->getPoint(1)->getDimension());
        static::assertInstanceOf(XYZM\Point::class, $line->getPoint(1));
        static::assertSame(2154, $line->getPoint(1)->getSrid());
        static::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertSame($line, $line->setArrayOfCoordinates([[1, 2, 3, 4]]));
        static::assertSame([[1, 2, 3, 4]], $line->toArray());
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyzmRejectsXymPoint(): void
    {
        $point = new XYM\Point(1, 2, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYZM\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 4], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyzmRejectsXyPoint(): void
    {
        $point = new XY\Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYZM\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyzmRejectsXyzPoint(): void
    {
        $point = new XYZ\Point(1, 2, 3, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYZM\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 3], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyzRejectsXymPoint(): void
    {
        $point = new XYM\Point(1, 2, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYZ\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 4], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyzRejectsXyPoint(): void
    {
        $point = new XY\Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYZ\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyzRejectsXyzmPoint(): void
    {
        $point = new XYZM\Point(1, 2, 3, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYZ\LineString([$point], 2154);
        } finally {
            static::assertSame([1, 2, 3, 4], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }
}
