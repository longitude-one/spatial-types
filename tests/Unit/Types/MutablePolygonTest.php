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
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableTriangleInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Triangle;
use PHPUnit\Framework\TestCase;

/** Mutable surface editing preserves identity and representable states.
 * @internal
 *
 * @coversNothing
 */
class MutablePolygonTest extends TestCase
{
    /** All-null coordinate tuples create empty mutable points without removing their ring. */
    public function testAllNullPointRemainsInRing(): void
    {
        $polygon = new Polygon([[[null, null]]]);
        static::assertSame([[[]]], $polygon->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $polygon->getRing(0)->getPoint(0)->getDimension());
    }

    /** A late invalid tuple never commits earlier hydrated rings. */
    public function testCoordinateFailureIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $owner = new Triangle([$ring]);
        $this->expectException(InvalidDimensionException::class);

        try {
            (new \ReflectionMethod($polygon, 'setArrayOfCoordinates'))->invoke($polygon, [[[3, 4]], [[5, 6, 7]]]);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([[[0, null]]], $owner->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** Coordinate replacement creates descendants against current context and leaves former owners unchanged. */
    public function testCoordinateReplacementIsolation(): void
    {
        $point = new Point(1, 2, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154, SpatialModelEnum::GEOGRAPHY);
        $owner = new Polygon([$ring]);
        static::assertSame($polygon, $polygon->setArrayOfCoordinates([[[0, null], [null, 5]], []]));
        static::assertInstanceOf(LineString::class, $polygon->getRing(0));
        static::assertInstanceOf(Point::class, $polygon->getRing(0)->getPoint(0));
        static::assertSame([[[0, null], [null, 5]], []], $polygon->toArray());
        static::assertSame(2154, $polygon->getRing(0)->getSrid());
        static::assertSame(2154, $polygon->getRing(0)->getPoint(0)->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygon->getRing(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygon->getRing(0)->getPoint(0)->getFamily());
        static::assertSame([[[1, 2]]], $owner->toArray());
        static::assertSame($ring, $owner->getRing(0));
        static::assertSame($point, $ring->getPoint(0));
        static::assertSame(4326, $point->getSrid());
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testCountAccessIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->getRing(1);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testCountPointIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->setPoint(0, 1, $point);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testCountRemovalIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->removeRing(1);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testCountReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->setRing(1, $ring);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Supplied components keep their own layout, family and reference. */
    public function testDifferentRingContextRemainsRepresentable(): void
    {
        $ring = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, null, 9]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygon = new Polygon([$ring], 2154);
        static::assertSame($ring, $polygon->getRing(0));
        static::assertSame([[[0, null, 9]]], $polygon->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $polygon->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $ring->getDimension());
        static::assertSame(4326, $ring->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $ring->getFamily());
    }

    /** Empty and incomplete boundaries remain representable, including triangles with holes. */
    public function testEmptyAndInvalidTriangleStates(): void
    {
        $triangle = new Triangle([]);
        static::assertSame([], $triangle->toArray());
        static::assertSame($triangle, $triangle->setArrayOfCoordinates([[], [[0, null]], [[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]]]));
        static::assertSame([[], [[0, null]], [[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]]], $triangle->toArray());
        static::assertSame(GeometryTypeEnum::TRIANGLE, $triangle->getType());
        static::assertSame($triangle, $triangle->setArrayOfCoordinates([]));
    }

    /** Replacing a ring endpoint never closes or repairs the ring. */
    public function testEndpointReplacementDoesNotRepairClosure(): void
    {
        $ring = new LineString([[1, 2], [3, 4], [1, 2]]);
        $polygon = new Polygon([$ring]);
        $point = new Point(9, null);
        static::assertSame($polygon, $polygon->setPoint(0, 0, $point));
        static::assertSame([[[9, null], [3, 4], [1, 2]]], $polygon->toArray());
        static::assertSame($point, $ring->getPoint(0));
    }

    /** Typed ring editing rejects immutable components without mutation. */
    public function testImmutableAdditionIsRejected(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $polygon = new Polygon([$ring]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($polygon, 'addRing'))->invoke($polygon, $immutable);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** Immutable rings are rejected at construction. */
    public function testImmutableConstructorIsRejected(): void
    {
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(Polygon::class))->newInstance([$immutable]);
        } finally {
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(2154, $immutable->getSrid());
        }
    }

    /** Typed ring editing rejects immutable components without mutation. */
    public function testImmutableInsertionIsRejected(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $polygon = new Polygon([$ring]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($polygon, 'insertRing'))->invoke($polygon, 0, $immutable);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** Nested immutable points cannot enter through point-array construction. */
    public function testImmutablePointArrayIsRejected(): void
    {
        $point = new PointImmutable(3, 4, 2154);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(Triangle::class))->newInstance([[$point]]);
        } finally {
            static::assertSame([3, 4], $point->toArray());
            static::assertSame(2154, $point->getSrid());
        }
    }

    /** Immutable point replacement cannot alter a shared ring. */
    public function testImmutablePointReplacementIsRejected(): void
    {
        $ring = new LineString([[0, null]]);
        $polygon = new Polygon([$ring]);
        $point = new PointImmutable(3, 4);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($polygon, 'setPoint'))->invoke($polygon, 0, 0, $point);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([3, 4], $point->toArray());
        }
    }

    /** Immutable aggregate paths continue to reject mutable rings. */
    public function testImmutablePolygonRejectsMutableRing(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(PolygonImmutable::class))->newInstance([$ring]);
        } finally {
            static::assertSame([[0, null]], $ring->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** Typed ring editing rejects immutable components without mutation. */
    public function testImmutableReplacementIsRejected(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $polygon = new Polygon([$ring]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($polygon, 'setRing'))->invoke($polygon, 0, $immutable);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testMissingRingPointIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->setPoint(1, 0, $point);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testNegativeAccessIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->getRing(-1);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testNegativeInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->insertRing(-1, $ring);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testNegativePointIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->setPoint(0, -1, $point);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testNegativeRemovalIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->removeRing(-1);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testNegativeReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->setRing(-1, $ring);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** A custom mutable ring cannot introduce immutable point descendants. */
    public function testNestedImmutablePointAdditionIsRejected(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $polygon = new Polygon([$ring], 2154);
        $immutable = new PointImmutable(3, 4, 4326);
        $invalid = static::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $polygon->addRing($invalid);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([3, 4], $immutable->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** A custom mutable ring cannot introduce immutable point descendants. */
    public function testNestedImmutablePointConstructionIsRejected(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $polygon = new Polygon([$ring], 2154);
        $immutable = new PointImmutable(3, 4, 4326);
        $invalid = static::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(Polygon::class))->newInstance([$invalid]);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([3, 4], $immutable->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** A custom mutable ring cannot introduce immutable point descendants. */
    public function testNestedImmutablePointInsertionIsRejected(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $polygon = new Polygon([$ring], 2154);
        $immutable = new PointImmutable(3, 4, 4326);
        $invalid = static::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $polygon->insertRing(0, $invalid);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([3, 4], $immutable->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** A custom mutable ring cannot introduce immutable point descendants. */
    public function testNestedImmutablePointReplacementIsRejected(): void
    {
        $ring = new LineString([[0, null]], 4326);
        $polygon = new Polygon([$ring], 2154);
        $immutable = new PointImmutable(3, 4, 4326);
        $invalid = static::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $polygon->setRing(0, $invalid);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame([3, 4], $immutable->toArray());
            static::assertSame(4326, $ring->getSrid());
        }
    }

    /** Ring edits retain supplied instances and preserve ordering. */
    public function testOrderedEditingAndSharing(): void
    {
        $point = new Point(0, null, 4326);
        $first = new LineString([$point], 4326);
        $second = new LineString([[3, 4]]);
        $third = new LineString([]);
        $polygon = new Polygon([7 => $first]);
        $owner = new Triangle([$first]);
        static::assertSame($polygon, $polygon->addRing($third)->insertRing(1, $second));
        static::assertSame([$first, $second, $third], $polygon->getRings());
        static::assertSame($polygon, $polygon->insertRing(3, $first));
        static::assertSame($polygon, $polygon->setRing(1, $third)->removeRing(2));
        static::assertSame([$first, $third, $first], $polygon->getElements());
        static::assertSame($first, $polygon->getRing(0));
        static::assertSame($point, $polygon->getRing(0)->getPoint(0));
        $replacement = new Point(null, 5, 2154);
        static::assertSame($polygon, $polygon->setPoint(0, 0, $replacement));
        static::assertSame($replacement, $first->getPoint(0));
        $replacement->setX(8);
        static::assertSame([[[8, 5]]], $owner->toArray());
        static::assertSame([[[8, 5]], [], [[8, 5]]], $polygon->toArray());
        static::assertSame(4326, $point->getSrid());
        static::assertSame([0, null], $point->toArray());
        $returned = $polygon->getRings();
        $returned[0] = $second;
        static::assertSame($first, $polygon->getRing(0));
    }

    /** Invalid addressing preserves the receiver and all referenced descendants. */
    public function testOversizedInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 4326);
        $ring = new LineString([$point], 4326);
        $polygon = new Polygon([$ring], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $polygon->insertRing(2, $ring);
        } finally {
            static::assertSame([$ring], $polygon->getRings());
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[0, null]]], $polygon->toArray());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(2154, $polygon->getSrid());
        }
    }

    /** Point-array construction retains supplied point objects without changing their context. */
    public function testPointArrayConstruction(): void
    {
        $point = new Point(0, null, 4326, SpatialModelEnum::GEOGRAPHY);
        $polygon = new Polygon([[$point]], 2154);
        static::assertSame($point, $polygon->getRing(0)->getPoint(0));
        static::assertSame(2154, $polygon->getRing(0)->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame([[[0, null]]], $polygon->toArray());
    }

    /** Mutable variants have the approved public contracts and fixed layout/type. */
    public function testPublicContracts(): void
    {
        $polygon = new Polygon([]);
        $triangle = new Triangle([]);
        static::assertContains(MutablePolygonInterface::class, class_implements($polygon));
        static::assertContains(MutableSpatialInterface::class, class_implements($polygon));
        static::assertContains(MutableTriangleInterface::class, class_implements($triangle));
        static::assertContains(MutableSpatialInterface::class, class_implements($triangle));
        static::assertSame(GeometryTypeEnum::POLYGON, $polygon->getType());
        static::assertSame(GeometryTypeEnum::TRIANGLE, $triangle->getType());
        static::assertSame(CoordinateDimensionEnum::XY, $polygon->getDimension());
        static::assertSame(CoordinateDimensionEnum::XY, $triangle->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $polygon->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $triangle->getFamily());
        static::assertFalse((new \ReflectionClass($polygon))->hasMethod('withPoint'));
        static::assertFalse((new \ReflectionClass($triangle))->hasMethod('withRing'));
        static::assertFalse((new \ReflectionClass($polygon))->hasMethod('withSrid'));
        static::assertFalse((new \ReflectionClass($triangle))->hasMethod('setDimension'));
        static::assertSame('static', (string) (new \ReflectionMethod($polygon, 'setPoint'))->getReturnType());
    }
}
