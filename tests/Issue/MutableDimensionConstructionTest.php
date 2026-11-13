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
        self::assertSame($point, $line->getPoint(0));
        self::assertSame([[1, 2], [1, 2]], $line->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $line->getPoint(1)->getDimension());
        self::assertInstanceOf(XY\Point::class, $line->getPoint(1));
        self::assertSame(2154, $line->getPoint(1)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[1, 2]]));
        self::assertSame([[1, 2]], $line->toArray());
    }

    /** Matching objects retain identity and tuples retain the declared layout. */
    public function testXymMatchingObjectsAndTuples(): void
    {
        $point = new XYM\Point(1, 2, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new XYM\LineString([$point, [1, 2, 4]], 2154);
        self::assertSame($point, $line->getPoint(0));
        self::assertSame([[1, 2, 4], [1, 2, 4]], $line->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $line->getPoint(1)->getDimension());
        self::assertInstanceOf(XYM\Point::class, $line->getPoint(1));
        self::assertSame(2154, $line->getPoint(1)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[1, 2, 4]]));
        self::assertSame([[1, 2, 4]], $line->toArray());
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXymRejectsXyPoint(): void
    {
        $point = new XY\Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(SpatialTypeExceptionInterface::class);

        try {
            new XYM\LineString([$point], 2154);
        } finally {
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 3, 4], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 3], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 4], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 3, 4], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 3], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Matching objects retain identity and tuples retain the declared layout. */
    public function testXyzmatchingObjectsAndTuples(): void
    {
        $point = new XYZ\Point(1, 2, 3, 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new XYZ\LineString([$point, [1, 2, 3]], 2154);
        self::assertSame($point, $line->getPoint(0));
        self::assertSame([[1, 2, 3], [1, 2, 3]], $line->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $line->getPoint(1)->getDimension());
        self::assertInstanceOf(XYZ\Point::class, $line->getPoint(1));
        self::assertSame(2154, $line->getPoint(1)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[1, 2, 3]]));
        self::assertSame([[1, 2, 3]], $line->toArray());
    }

    /** Matching objects retain identity and tuples retain the declared layout. */
    public function testXyzmMatchingObjectsAndTuples(): void
    {
        $point = new XYZM\Point(1, 2, 3, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new XYZM\LineString([$point, [1, 2, 3, 4]], 2154);
        self::assertSame($point, $line->getPoint(0));
        self::assertSame([[1, 2, 3, 4], [1, 2, 3, 4]], $line->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $line->getPoint(1)->getDimension());
        self::assertInstanceOf(XYZM\Point::class, $line->getPoint(1));
        self::assertSame(2154, $line->getPoint(1)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $line->getPoint(1)->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertSame($line, $line->setArrayOfCoordinates([[1, 2, 3, 4]]));
        self::assertSame([[1, 2, 3, 4]], $line->toArray());
    }

    /** Existing point objects cannot bypass the receiver's declared layout. */
    public function testXyzmRejectsXymPoint(): void
    {
        $point = new XYM\Point(1, 2, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $this->expectException(InvalidDimensionException::class);

        try {
            new XYZM\LineString([$point], 2154);
        } finally {
            self::assertSame([1, 2, 4], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 3], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 4], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
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
            self::assertSame([1, 2, 3, 4], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }
}
