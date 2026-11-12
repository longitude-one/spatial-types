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
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
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
class MutableDimensionAggregateConstructionTest extends TestCase
{
    /** EMPTY components still have a fixed coordinate layout. */
    public function testCircularStringRejectsIncompatibleComponent(): void
    {
        $component = new XYZ\Point(1, 2, 3);
        $this->expectException(InvalidDimensionException::class);

        new XY\CircularString([$component]);
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testCompoundCurveRejectsIncompatibleComponent(): void
    {
        $component = new XYZ\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        new XY\CompoundCurve([$component]);
    }

    /** A custom curve cannot hide an incompatible interior point. */
    public function testCustomCurveDescendantCannotBypassLayout(): void
    {
        $point = new XYZ\Point(1, 2, 3);
        $curve = self::createStub(\LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface::class);
        $curve->method('getDimension')->willReturn(CoordinateDimensionEnum::XY);
        $curve->method('getElements')->willReturn([$point]);
        $curve->method('getStartPoint')->willReturn(null);
        $curve->method('getEndPoint')->willReturn(null);
        $collection = new XY\SpatialCollection([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $collection->addElement($curve);
        } finally {
            self::assertSame([], $collection->getElements());
            self::assertSame([1, 2, 3], $point->toArray());
        }
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testMultiLineStringRejectsIncompatibleComponent(): void
    {
        $component = new XYZ\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        new XYZM\MultiLineString([$component]);
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testMultiPointRejectsIncompatibleComponent(): void
    {
        $component = new XYZ\Point();
        $this->expectException(InvalidDimensionException::class);

        new XYM\MultiPoint([$component]);
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testMultiPolygonRejectsIncompatibleComponent(): void
    {
        $component = new XYZM\Polygon([]);
        $this->expectException(InvalidDimensionException::class);

        new XYZ\MultiPolygon([$component]);
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testPolygonRejectsIncompatibleComponent(): void
    {
        $component = new XYM\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        new XY\Polygon([$component]);
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testPolyhedralSurfaceRejectsIncompatibleComponent(): void
    {
        $component = new XYM\Polygon([]);
        $this->expectException(InvalidDimensionException::class);

        new XYZ\PolyhedralSurface([$component]);
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testSpatialCollectionRejectsIncompatibleComponent(): void
    {
        $component = new XYZ\SpatialCollection([]);
        $this->expectException(InvalidDimensionException::class);

        new XY\SpatialCollection([$component]);
    }

    /** EMPTY components still have a fixed coordinate layout. */
    public function testTriangleRejectsIncompatibleComponent(): void
    {
        $component = new XYZM\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        new XY\Triangle([$component]);
    }
}
