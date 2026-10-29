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

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\PolyhedralSurface;
use PHPUnit\Framework\TestCase;

/** Technical input and indexed failures never partially modify shared descendants.
 * @internal
 *
 * @coversNothing
 */
class MutablePolyhedralSurfaceOwnershipTest extends TestCase
{
    /** Coordinate-array construction still rejects immutable nested spatial values. */
    public function testArrayImmutablePointIsRejected(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $invalid = new PointImmutable(3, 4, 5);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(PolyhedralSurface::class))->newInstance([[[$invalid]]]);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Coordinate-array construction still rejects immutable nested spatial values. */
    public function testArrayImmutableRingIsRejected(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $invalid = new LineStringImmutable([[3, 4, 5]]);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(PolyhedralSurface::class))->newInstance([[$invalid]]);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Typed editing rejects immutable spatial components without implicit conversion. */
    public function testImmutablePatchAdditionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $invalid = new PolygonImmutable([[[3, 4, 5]]]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($surface, 'addPatch'))->invoke($surface, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Typed editing rejects immutable spatial components without implicit conversion. */
    public function testImmutablePatchInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $invalid = new PolygonImmutable([[[3, 4, 5]]]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($surface, 'insertPatch'))->invoke($surface, 0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Typed editing rejects immutable spatial components without implicit conversion. */
    public function testImmutablePatchReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $invalid = new PolygonImmutable([[[3, 4, 5]]]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($surface, 'setPatch'))->invoke($surface, 0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Typed editing rejects immutable spatial components without implicit conversion. */
    public function testImmutablePointReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $invalid = new PointImmutable(3, 4, 5);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($surface, 'setPoint'))->invoke($surface, 0, 0, 0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Typed editing rejects immutable spatial components without implicit conversion. */
    public function testImmutableRingReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $invalid = new LineStringImmutable([[3, 4, 5]]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($surface, 'setRing'))->invoke($surface, 0, 0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutablePointAdditionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new PointImmutable(3, 4, 5);
        $invalidRing = static::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->addPatch($invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutablePointConstructionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new PointImmutable(3, 4, 5);
        $invalidRing = static::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(PolyhedralSurface::class))->newInstance([$invalid]);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutablePointInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new PointImmutable(3, 4, 5);
        $invalidRing = static::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->insertPatch(0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutablePointReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new PointImmutable(3, 4, 5);
        $invalidRing = static::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->setPatch(0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutableRingAdditionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new LineStringImmutable([[3, 4, 5]]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->addPatch($invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutableRingConstructionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new LineStringImmutable([[3, 4, 5]]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(PolyhedralSurface::class))->newInstance([$invalid]);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutableRingInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new LineStringImmutable([[3, 4, 5]]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->insertPatch(0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Custom mutable components cannot introduce immutable descendants. */
    public function testNestedImmutableRingReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new LineStringImmutable([[3, 4, 5]]);
        $invalid = static::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->setPatch(0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
        }
    }

    /** Invalid descendants are rejected before editing a shared patch. */
    public function testNestedRingAdditionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new PointImmutable(3, 4, 5);
        $invalid = static::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->addRing(0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
            static::assertSame([3, 4, 5], $immutable->toArray());
        }
    }

    /** Invalid descendants are rejected before editing a shared patch. */
    public function testNestedRingInsertionIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new PointImmutable(3, 4, 5);
        $invalid = static::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->insertRing(0, 0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
            static::assertSame([3, 4, 5], $immutable->toArray());
        }
    }

    /** Invalid descendants are rejected before editing a shared patch. */
    public function testNestedRingReplacementIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $immutable = new PointImmutable(3, 4, 5);
        $invalid = static::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->setRing(0, 0, $invalid);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $ring->getSrid());
            static::assertSame(4326, $patch->getSrid());
            static::assertSame(2154, $surface->getSrid());
            static::assertSame([3, 4, 5], $immutable->toArray());
        }
    }
}
