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
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurfaceImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\PolyhedralSurface;
use PHPUnit\Framework\TestCase;

/** Mutable patches preserve ordered data and shared ownership.
 * @internal
 *
 * @coversNothing
 */
class MutablePolyhedralSurfaceTest extends TestCase
{
    /** A late invalid tuple never commits an earlier hydrated patch. */
    public function testCoordinateFailureIsAtomic(): void
    {
        $point = new Point(0, null, 8, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154);
        $owner = new PolyhedralSurface([$patch]);
        $this->expectException(InvalidDimensionException::class);

        try {
            new \ReflectionMethod($surface, 'setArrayOfCoordinates')->invoke($surface, [[[[3, 4, 5]]], [[[6, 7, 8, 9]]]]);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame($ring, $patch->getRing(0));
            self::assertSame($point, $ring->getPoint(0));
            self::assertSame([[[[0, null, 8]]]], $surface->toArray());
            self::assertSame([[[[0, null, 8]]]], $owner->toArray());
            self::assertSame(4326, $point->getSrid());
        }
    }

    /** Complete coordinate replacement creates descendants and detaches previous shared patches. */
    public function testCoordinateReplacementIsolation(): void
    {
        $point = new Point(1, 2, 3, 4326);
        $ring = new LineString([$point], 4326);
        $patch = new Polygon([$ring], 4326);
        $surface = new PolyhedralSurface([$patch], 2154, SpatialModelEnum::GEOGRAPHY);
        $owner = new PolyhedralSurface([$patch]);
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[[null, 5, 6]]], []]));
        self::assertSame([[[[null, 5, 6]]], []], $surface->toArray());
        self::assertInstanceOf(Polygon::class, $surface->getPatch(0));
        self::assertInstanceOf(LineString::class, $surface->getPatch(0)->getRing(0));
        self::assertInstanceOf(Point::class, $surface->getPatch(0)->getRing(0)->getPoint(0));
        self::assertSame(2154, $surface->getPatch(0)->getSrid());
        self::assertSame(2154, $surface->getPatch(0)->getRing(0)->getSrid());
        self::assertSame(2154, $surface->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getPatch(0)->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getPatch(0)->getRing(0)->getPoint(0)->getFamily());
        self::assertSame([$patch], $owner->getPatches());
        self::assertSame($ring, $patch->getRing(0));
        self::assertSame($point, $ring->getPoint(0));
        self::assertSame([[[[1, 2, 3]]]], $owner->toArray());
        self::assertSame(4326, $point->getSrid());
    }

    /** Empty patches, rings and incomplete points are retained without geometric checks. */
    public function testEmptyAndIncompleteSurface(): void
    {
        $surface = new PolyhedralSurface();
        self::assertSame([], $surface->toArray());
        self::assertSame($surface, $surface->setArrayOfCoordinates([[], [[]], [[[0, null, null], [null, 7, 8]]]]));
        self::assertSame([[], [[]], [[[0, null, null], [null, 7, 8]]]], $surface->toArray());
        self::assertSame($surface, $surface->setArrayOfCoordinates([]));
    }

    /** All-null points remain EMPTY without removing containing rings or patches. */
    public function testEmptyPoint(): void
    {
        $surface = new PolyhedralSurface([[[[null, null, null]]]]);
        self::assertSame([[[[]]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getPatch(0)->getRing(0)->getPoint(0)->getDimension());
    }

    /** Immutable patches are rejected at construction without changing their state. */
    public function testImmutablePatchConstructorIsRejected(): void
    {
        $patch = new PolygonImmutable([[[1, 2, 3]]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(PolyhedralSurface::class)->newInstance([$patch]);
        } finally {
            self::assertSame([[[1, 2, 3]]], $patch->toArray());
            self::assertSame(4326, $patch->getSrid());
        }
    }

    /** Immutable source data stays isolated when its coordinate array creates mutable descendants. */
    public function testImmutableSourceIsolation(): void
    {
        $immutable = new PolyhedralSurfaceImmutable([[[[1, 2, 3]]]], 4326);
        $surface = new \ReflectionClass(PolyhedralSurface::class)->newInstance($immutable->toArray(), 4326);
        $surface->getPatch(0)->getRing(0)->getPoint(0)->setX(9);
        self::assertSame([[[[9, 2, 3]]]], $surface->toArray());
        self::assertSame([[[[1, 2, 3]]]], $immutable->toArray());
        self::assertSame(4326, $immutable->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
    }

    /** Immutable aggregate constructors still reject mutable patches. */
    public function testImmutableSurfaceRejectsMutablePatch(): void
    {
        $patch = new Polygon([[[0, null, 8]]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(PolyhedralSurfaceImmutable::class)->newInstance([$patch]);
        } finally {
            self::assertSame([[[0, null, 8]]], $patch->toArray());
            self::assertSame(4326, $patch->getSrid());
        }
    }

    /** Nested editing addresses patch, ring, then point and exposes shared changes. */
    public function testNestedEditing(): void
    {
        $first = new LineString([[1, 2, 3]]);
        $second = new LineString([[4, 5, 6]]);
        $empty = new LineString([]);
        $patch = new Polygon([$first]);
        $surface = new PolyhedralSurface([new Polygon([]), $patch]);
        $owner = new PolyhedralSurface([$patch]);
        self::assertSame($surface, $surface->addRing(1, $empty)->insertRing(1, 1, $second));
        self::assertSame([$first, $second, $empty], $patch->getRings());
        self::assertSame($surface, $surface->insertRing(1, 3, $first)->setRing(1, 1, $empty)->removeRing(1, 2));
        self::assertSame([$first, $empty, $first], $patch->getRings());
        $replacement = new Point(9, null, 8, 4326);
        self::assertSame($surface, $surface->setPoint(1, 0, 0, $replacement));
        self::assertSame($replacement, $owner->getPatch(0)->getRing(0)->getPoint(0));
        self::assertSame([[], [[[9, null, 8]], [], [[9, null, 8]]]], $surface->toArray());
        self::assertSame([[[[9, null, 8]], [], [[9, null, 8]]]], $owner->toArray());
    }

    /** Patches retain supplied instances and order on every membership operation. */
    public function testOrderedPatchEditing(): void
    {
        $first = new Polygon([[[1, 2, 3]]], 4326);
        $second = new Polygon([[[4, 5, 6]]], 2154);
        $empty = new Polygon([]);
        $surface = new PolyhedralSurface([7 => $first]);
        self::assertSame($surface, $surface->addPatch($empty)->insertPatch(1, $second));
        self::assertSame([$first, $second, $empty], $surface->getPatches());
        self::assertSame($surface, $surface->insertPatch(3, $first)->setPatch(1, $empty)->removePatch(2));
        self::assertSame([$first, $empty, $first], $surface->getElements());
        self::assertSame([[[[1, 2, 3]]], [], [[[1, 2, 3]]]], $surface->toArray());
        self::assertSame($first, $surface->getPatch(0));
        self::assertSame(4326, $first->getSrid());
        self::assertSame(2154, $second->getSrid());
        $returned = $surface->getPatches();
        $returned[0] = $second;
        self::assertSame($first, $surface->getPatch(0));
    }

    /** Mutable surfaces implement the approved contracts without copy or dimension mutators. */
    public function testPublicContracts(): void
    {
        $surface = new PolyhedralSurface();
        self::assertContains(MutablePolyhedralSurfaceInterface::class, class_implements($surface));
        self::assertContains(MutableSpatialInterface::class, class_implements($surface));
        self::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getFamily());
        self::assertFalse(new \ReflectionClass($surface)->hasMethod('withPatch'));
        self::assertFalse(new \ReflectionClass($surface)->hasMethod('withArrayOfCoordinates'));
        self::assertFalse(new \ReflectionClass($surface)->hasMethod('withSrid'));
        self::assertFalse(new \ReflectionClass($surface)->hasMethod('setDimension'));
        self::assertSame('static', (string) new \ReflectionMethod($surface, 'setPoint')->getReturnType());
    }

    /** Shared point identity changes every occurrence; equal-valued points remain independent. */
    public function testSharedVertexIdentity(): void
    {
        $shared = new Point(1, 2, 3);
        $equal = new Point(1, 2, 3);
        $first = new Polygon([new LineString([$shared, $equal, $shared])]);
        $second = new Polygon([new LineString([$shared])]);
        $surface = new PolyhedralSurface([$first, $second]);
        $owner = new PolyhedralSurface([$second]);
        self::assertSame($shared, $surface->getPatch(0)->getRing(0)->getPoint(2));
        self::assertSame($shared, $surface->getPatch(1)->getRing(0)->getPoint(0));
        $shared->setZ(null)->setX(9);
        self::assertSame([[[[9, 2, null], [1, 2, 3], [9, 2, null]]], [[[9, 2, null]]]], $surface->toArray());
        self::assertSame([[[[9, 2, null]]]], $owner->toArray());
        self::assertSame([1, 2, 3], $equal->toArray());
        $replacement = new Point(7, 8, 9);
        $surface->setPoint(0, 0, 0, $replacement);
        self::assertSame([[[[7, 8, 9], [1, 2, 3], [9, 2, null]]], [[[9, 2, null]]]], $surface->toArray());
        self::assertSame($shared, $second->getRing(0)->getPoint(0));
    }

    /** Construction retains supplied nested objects without synchronizing family or reference. */
    public function testSuppliedNestedComponents(): void
    {
        $point = new Point(0, null, 8, 4326, SpatialModelEnum::GEOGRAPHY);
        $ring = new LineString([$point], 3857);
        $surface = new PolyhedralSurface([[$ring], [[$point]], new Polygon([[[1, 2, 9]]])], 2154);
        self::assertSame($ring, $surface->getPatch(0)->getRing(0));
        self::assertSame($point, $surface->getPatch(1)->getRing(0)->getPoint(0));
        self::assertSame(2154, $surface->getPatch(0)->getSrid());
        self::assertSame(3857, $ring->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getPatch(2)->getDimension());
        self::assertSame([[[[0, null, 8]]], [[[0, null, 8]]], [[[1, 2, 9]]]], $surface->toArray());
    }

    /** XYZM surfaces hydrate the existing measured layout and retain null Z and M ordinates. */
    public function testXyzmLayout(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\PolyhedralSurface([[[[0, null, 8, 9]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[[0, null, 8, 9]]]], $surface->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon::class, $surface->getPatch(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString::class, $surface->getPatch(0)->getRing(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $surface->getPatch(0)->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[[null, 7, null, 0]]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[[null, 7, null, 0]]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        self::assertSame(2154, $surface->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getPatch(0)->getRing(0)->getPoint(0)->getFamily());
    }
}
