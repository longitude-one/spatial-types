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
class MutablePolyhedralSurfaceDimensionMutationTest extends TestCase
{
    /** Rejection preserves membership and subsequent context propagation. */
    public function testAddRejectsAtomically(): void
    {
        $member = new XYZ\Polygon([[[1, 2, 3]]]);
        $aggregate = new XYZ\PolyhedralSurface([$member], 2154);
        $incompatible = new XYZM\Polygon([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->addPatch($incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getPatches());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Nested editing uses the same dimensional check before replacement. */
    public function testAddRingRejectsIncompatibleDescendant(): void
    {
        $point = new XYZ\Point(1, 2, 3);
        $ring = new XYZ\LineString([$point]);
        $patch = new XYZ\Polygon([$ring]);
        $surface = new XYZ\PolyhedralSurface([$patch], 2154);
        $incompatible = new XYM\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $surface->addRing(0, $incompatible);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame([$ring], $patch->getRings());
            static::assertSame([$point], $ring->getPoints());
            static::assertSame([[[[1, 2, 3]]]], $surface->toArray());
            $surface->setSrid(4326);
            static::assertSame(4326, $point->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testInsertRejectsAtomically(): void
    {
        $member = new XYZ\Polygon([[[1, 2, 3]]]);
        $aggregate = new XYZ\PolyhedralSurface([$member], 2154);
        $incompatible = new XYZM\Polygon([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->insertPatch(1, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getPatches());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Nested editing uses the same dimensional check before replacement. */
    public function testInsertRingRejectsIncompatibleDescendant(): void
    {
        $point = new XYZ\Point(1, 2, 3);
        $ring = new XYZ\LineString([$point]);
        $patch = new XYZ\Polygon([$ring]);
        $surface = new XYZ\PolyhedralSurface([$patch], 2154);
        $incompatible = new XYZM\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $surface->insertRing(0, 1, $incompatible);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame([$ring], $patch->getRings());
            static::assertSame([$point], $ring->getPoints());
            static::assertSame([[[[1, 2, 3]]]], $surface->toArray());
            $surface->setSrid(4326);
            static::assertSame(4326, $point->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Nested editing uses the same dimensional check before replacement. */
    public function testSetPointRejectsIncompatibleDescendant(): void
    {
        $point = new XYZ\Point(1, 2, 3);
        $ring = new XYZ\LineString([$point]);
        $patch = new XYZ\Polygon([$ring]);
        $surface = new XYZ\PolyhedralSurface([$patch], 2154);
        $incompatible = new XYM\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $surface->setPoint(0, 0, 0, $incompatible);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame([$ring], $patch->getRings());
            static::assertSame([$point], $ring->getPoints());
            static::assertSame([[[[1, 2, 3]]]], $surface->toArray());
            $surface->setSrid(4326);
            static::assertSame(4326, $point->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testSetRejectsAtomically(): void
    {
        $member = new XYZ\Polygon([[[1, 2, 3]]]);
        $aggregate = new XYZ\PolyhedralSurface([$member], 2154);
        $incompatible = new XYZM\Polygon([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->setPatch(0, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getPatches());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Nested editing uses the same dimensional check before replacement. */
    public function testSetRingRejectsIncompatibleDescendant(): void
    {
        $point = new XYZ\Point(1, 2, 3);
        $ring = new XYZ\LineString([$point]);
        $patch = new XYZ\Polygon([$ring]);
        $surface = new XYZ\PolyhedralSurface([$patch], 2154);
        $incompatible = new XY\LineString([]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $surface->setRing(0, 0, $incompatible);
        } finally {
            static::assertSame([$patch], $surface->getPatches());
            static::assertSame([$ring], $patch->getRings());
            static::assertSame([$point], $ring->getPoints());
            static::assertSame([[[[1, 2, 3]]]], $surface->toArray());
            $surface->setSrid(4326);
            static::assertSame(4326, $point->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }
}
