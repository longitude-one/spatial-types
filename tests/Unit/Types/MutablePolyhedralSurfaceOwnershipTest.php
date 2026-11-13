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
            new \ReflectionClass(PolyhedralSurface::class)->newInstance([[[$invalid]]]);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
            new \ReflectionClass(PolyhedralSurface::class)->newInstance([[$invalid]]);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($surface, 'addPatch')->invoke($surface, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($surface, 'insertPatch')->invoke($surface, 0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($surface, 'setPatch')->invoke($surface, 0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($surface, 'setPoint')->invoke($surface, 0, 0, 0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($surface, 'setRing')->invoke($surface, 0, 0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalidRing = self::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->addPatch($invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalidRing = self::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(PolyhedralSurface::class)->newInstance([$invalid]);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalidRing = self::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->insertPatch(0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalidRing = self::createStub(MutableLineStringInterface::class);
        $invalidRing->method('getPoints')->willReturn([$immutable]);
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$invalidRing]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->setPatch(0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->addPatch($invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(PolyhedralSurface::class)->newInstance([$invalid]);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->insertPatch(0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalid = self::createStub(MutablePolygonInterface::class);
        $invalid->method('getRings')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->setPatch(0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
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
        $invalid = self::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->addRing(0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
            self::assertSame([3, 4, 5], $immutable->toArray());
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
        $invalid = self::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->insertRing(0, 0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
            self::assertSame([3, 4, 5], $immutable->toArray());
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
        $invalid = self::createStub(MutableLineStringInterface::class);
        $invalid->method('getPoints')->willReturn([$immutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $surface->setRing(0, 0, $invalid);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $ring->getSrid());
            self::assertSame(4326, $patch->getSrid());
            self::assertSame(2154, $surface->getSrid());
            self::assertSame([3, 4, 5], $immutable->toArray());
        }
    }
}
