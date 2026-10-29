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
            (new \ReflectionMethod($surface, 'setArrayOfCoordinates'))->invoke($surface, [[[[3, 4, 5]]], [[[6, 7, 8, 9]]]]);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame($ring, $patch->getRing(0));
            static::assertSame($point, $ring->getPoint(0));
            static::assertSame([[[[0, null, 8]]]], $surface->toArray());
            static::assertSame([[[[0, null, 8]]]], $owner->toArray());
            static::assertSame(4326, $point->getSrid());
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
        static::assertSame($surface, $surface->setArrayOfCoordinates([[[[null, 5, 6]]], []]));
        static::assertSame([[[[null, 5, 6]]], []], $surface->toArray());
        static::assertInstanceOf(Polygon::class, $surface->getPatch(0));
        static::assertInstanceOf(LineString::class, $surface->getPatch(0)->getRing(0));
        static::assertInstanceOf(Point::class, $surface->getPatch(0)->getRing(0)->getPoint(0));
        static::assertSame(2154, $surface->getPatch(0)->getSrid());
        static::assertSame(2154, $surface->getPatch(0)->getRing(0)->getSrid());
        static::assertSame(2154, $surface->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getPatch(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getPatch(0)->getRing(0)->getPoint(0)->getFamily());
        static::assertSame([$patch], $owner->getPatches());
        static::assertSame($ring, $patch->getRing(0));
        static::assertSame($point, $ring->getPoint(0));
        static::assertSame([[[[1, 2, 3]]]], $owner->toArray());
        static::assertSame(4326, $point->getSrid());
    }

    /** Empty patches, rings and incomplete points are retained without geometric checks. */
    public function testEmptyAndIncompleteSurface(): void
    {
        $surface = new PolyhedralSurface();
        static::assertTrue($surface->isEmpty());
        static::assertSame([], $surface->toArray());
        static::assertSame($surface, $surface->setArrayOfCoordinates([[], [[]], [[[0, null, null], [null, 7, 8]]]]));
        static::assertFalse($surface->isEmpty());
        static::assertSame([[], [[]], [[[0, null, null], [null, 7, 8]]]], $surface->toArray());
        static::assertSame($surface, $surface->setArrayOfCoordinates([]));
        static::assertTrue($surface->isEmpty());
    }

    /** All-null points remain EMPTY without removing containing rings or patches. */
    public function testEmptyPoint(): void
    {
        $surface = new PolyhedralSurface([[[[null, null, null]]]]);
        static::assertFalse($surface->isEmpty());
        static::assertSame([[[[]]]], $surface->toArray());
        static::assertTrue($surface->getPatch(0)->getRing(0)->getPoint(0)->isEmpty());
        static::assertSame(CoordinateDimensionEnum::XYZ, $surface->getPatch(0)->getRing(0)->getPoint(0)->getDimension());
    }

    /** Immutable patches are rejected at construction without changing their state. */
    public function testImmutablePatchConstructorIsRejected(): void
    {
        $patch = new PolygonImmutable([[[1, 2, 3]]], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(PolyhedralSurface::class))->newInstance([$patch]);
        } finally {
            static::assertSame([[[1, 2, 3]]], $patch->toArray());
            static::assertSame(4326, $patch->getSrid());
        }
    }

    /** Immutable source data stays isolated when its coordinate array creates mutable descendants. */
    public function testImmutableSourceIsolation(): void
    {
        $immutable = new PolyhedralSurfaceImmutable([[[[1, 2, 3]]]], 4326);
        $surface = (new \ReflectionClass(PolyhedralSurface::class))->newInstance($immutable->toArray(), 4326);
        $surface->getPatch(0)->getRing(0)->getPoint(0)->setX(9);
        static::assertSame([[[[9, 2, 3]]]], $surface->toArray());
        static::assertSame([[[[1, 2, 3]]]], $immutable->toArray());
        static::assertSame(4326, $immutable->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
    }

    /** Immutable aggregate constructors still reject mutable patches. */
    public function testImmutableSurfaceRejectsMutablePatch(): void
    {
        $patch = new Polygon([[[0, null, 8]]], 4326);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionClass(PolyhedralSurfaceImmutable::class))->newInstance([$patch]);
        } finally {
            static::assertSame([[[0, null, 8]]], $patch->toArray());
            static::assertSame(4326, $patch->getSrid());
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
        static::assertSame($surface, $surface->addRing(1, $empty)->insertRing(1, 1, $second));
        static::assertSame([$first, $second, $empty], $patch->getRings());
        static::assertSame($surface, $surface->insertRing(1, 3, $first)->setRing(1, 1, $empty)->removeRing(1, 2));
        static::assertSame([$first, $empty, $first], $patch->getRings());
        $replacement = new Point(9, null, 8, 4326);
        static::assertSame($surface, $surface->setPoint(1, 0, 0, $replacement));
        static::assertSame($replacement, $owner->getPatch(0)->getRing(0)->getPoint(0));
        static::assertSame([[], [[[9, null, 8]], [], [[9, null, 8]]]], $surface->toArray());
        static::assertSame([[[[9, null, 8]], [], [[9, null, 8]]]], $owner->toArray());
    }

    /** Patches retain supplied instances and order on every membership operation. */
    public function testOrderedPatchEditing(): void
    {
        $first = new Polygon([[[1, 2, 3]]], 4326);
        $second = new Polygon([[[4, 5, 6]]], 2154);
        $empty = new Polygon([]);
        $surface = new PolyhedralSurface([7 => $first]);
        static::assertSame($surface, $surface->addPatch($empty)->insertPatch(1, $second));
        static::assertSame([$first, $second, $empty], $surface->getPatches());
        static::assertSame($surface, $surface->insertPatch(3, $first)->setPatch(1, $empty)->removePatch(2));
        static::assertSame([$first, $empty, $first], $surface->getElements());
        static::assertSame([[[[1, 2, 3]]], [], [[[1, 2, 3]]]], $surface->toArray());
        static::assertSame($first, $surface->getPatch(0));
        static::assertSame(4326, $first->getSrid());
        static::assertSame(2154, $second->getSrid());
        $returned = $surface->getPatches();
        $returned[0] = $second;
        static::assertSame($first, $surface->getPatch(0));
    }

    /** Mutable surfaces implement the approved contracts without copy or dimension mutators. */
    public function testPublicContracts(): void
    {
        $surface = new PolyhedralSurface();
        static::assertContains(MutablePolyhedralSurfaceInterface::class, class_implements($surface));
        static::assertContains(MutableSpatialInterface::class, class_implements($surface));
        static::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        static::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $surface->getFamily());
        static::assertFalse((new \ReflectionClass($surface))->hasMethod('withPatch'));
        static::assertFalse((new \ReflectionClass($surface))->hasMethod('withArrayOfCoordinates'));
        static::assertFalse((new \ReflectionClass($surface))->hasMethod('withSrid'));
        static::assertFalse((new \ReflectionClass($surface))->hasMethod('setDimension'));
        static::assertSame('static', (string) (new \ReflectionMethod($surface, 'setPoint'))->getReturnType());
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
        static::assertSame($shared, $surface->getPatch(0)->getRing(0)->getPoint(2));
        static::assertSame($shared, $surface->getPatch(1)->getRing(0)->getPoint(0));
        $shared->setZ(null)->setX(9);
        static::assertSame([[[[9, 2, null], [1, 2, 3], [9, 2, null]]], [[[9, 2, null]]]], $surface->toArray());
        static::assertSame([[[[9, 2, null]]]], $owner->toArray());
        static::assertSame([1, 2, 3], $equal->toArray());
        $replacement = new Point(7, 8, 9);
        $surface->setPoint(0, 0, 0, $replacement);
        static::assertSame([[[[7, 8, 9], [1, 2, 3], [9, 2, null]]], [[[9, 2, null]]]], $surface->toArray());
        static::assertSame($shared, $second->getRing(0)->getPoint(0));
    }

    /** Construction retains supplied nested objects without synchronizing context or layout. */
    public function testSuppliedNestedComponents(): void
    {
        $point = new Point(0, null, 8, 4326, SpatialModelEnum::GEOGRAPHY);
        $ring = new LineString([$point], 3857);
        $surface = new PolyhedralSurface([[$ring], [[$point]], new \LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon([[[1, 2, 9]]])], 2154);
        static::assertSame($ring, $surface->getPatch(0)->getRing(0));
        static::assertSame($point, $surface->getPatch(1)->getRing(0)->getPoint(0));
        static::assertSame(2154, $surface->getPatch(0)->getSrid());
        static::assertSame(3857, $ring->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $surface->getPatch(2)->getDimension());
        static::assertSame([[[[0, null, 8]]], [[[0, null, 8]]], [[[1, 2, 9]]]], $surface->toArray());
    }

    /** XYZM surfaces hydrate the existing measured layout and retain null Z and M ordinates. */
    public function testXyzmLayout(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\PolyhedralSurface([[[[0, null, 8, 9]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        static::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        static::assertSame([[[[0, null, 8, 9]]]], $surface->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon::class, $surface->getPatch(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString::class, $surface->getPatch(0)->getRing(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $surface->getPatch(0)->getRing(0)->getPoint(0));
        static::assertSame($surface, $surface->setArrayOfCoordinates([[[[null, 7, null, 0]]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        static::assertSame([[[[null, 7, null, 0]]]], $surface->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        static::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $surface->getType());
        static::assertSame(2154, $surface->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame(SpatialModelEnum::GEOMETRY, $surface->getPatch(0)->getRing(0)->getPoint(0)->getFamily());
    }
}
