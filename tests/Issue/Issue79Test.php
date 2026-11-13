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

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use PHPUnit\Framework\TestCase;

/** Regression coverage for incompatible spatial component exceptions.
 * @internal
 *
 * @coversNothing
 */
class Issue79Test extends TestCase
{
    /** Appending rejects an invalid first point before storing either arc point. */
    public function testArcAppendRejectsWrongPassingPoint(): void
    {
        $origin = new \LongitudeOne\SpatialTypes\Types\Dimension2\Point(0, 0, 4326);
        $curve = new \LongitudeOne\SpatialTypes\Types\Dimension2\CircularString([$origin], 4326);
        $invalid = new PolygonImmutable([]);
        $destination = new \LongitudeOne\SpatialTypes\Types\Dimension2\Point(2, 0, 2154);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('MutablePointInterface');

        try {
            $curve->appendArc($invalid, $destination);
        } finally {
            self::assertSame([$origin], $curve->getPoints());
            self::assertSame([[0, 0]], $curve->toArray());
            self::assertSame(2154, $destination->getSrid());
        }
    }

    /** Replacing rejects an invalid first point before replacing the stored pair. */
    public function testArcReplacementRejectsWrongPassingPoint(): void
    {
        $curve = new \LongitudeOne\SpatialTypes\Types\Dimension2\CircularString([[0, 0], [1, 1], [2, 0]], 4326);
        $invalid = new PolygonImmutable([]);
        $destination = new \LongitudeOne\SpatialTypes\Types\Dimension2\Point(4, 0, 2154);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('MutablePointInterface');

        try {
            $curve->replaceArc(0, $invalid, $destination);
        } finally {
            self::assertSame([[0, 0], [1, 1], [2, 0]], $curve->toArray());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame([4, 0], $destination->toArray());
        }
    }

    /** Compatible components, coordinate arrays and EMPTY members remain supported. */
    public function testCompatibleImmutablePolygonsRemainAccepted(): void
    {
        $polygon = new PolygonImmutable([[[1, 2]]], 4326);
        $empty = new PolygonImmutable([], 2154);
        $collection = new MultiPolygonImmutable([$polygon, $empty, [[[3, 4]]]], 4326);

        self::assertSame([$polygon, $empty, $collection->getPolygon(2)], $collection->getPolygons());
        self::assertSame([[[[1, 2]]], [], [[[3, 4]]]], $collection->toArray());
        self::assertSame(4326, $collection->getSrid());
        self::assertSame(2154, $collection->getPolygon(1)->getSrid());
        self::assertInstanceOf(PolygonImmutable::class, $collection->getPolygon(2));
        self::assertSame([[[1, 2]]], $polygon->toArray());
    }

    /** Immutable replacement rejects mutable spatial values before copying members. */
    public function testImmutableCollectionRejectsMutableReplacement(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(1, 2, 4326);
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable(4326, [$point]);
        $replacement = new \LongitudeOne\SpatialTypes\Types\Dimension2\Point(3, 4, 2154);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('ImmutableSpatialInterface');

        try {
            $source->withElement(0, $replacement);
        } finally {
            self::assertSame([$point], $source->getElements());
            self::assertSame([[1, 2]], $source->toArray());
            self::assertSame([3, 4], $replacement->toArray());
            self::assertSame(2154, $replacement->getSrid());
        }
    }

    /** The original EMPTY XY polygon reproduction is catchable as a spatial failure. */
    public function testImmutableMultiPolygonRejectsMutablePolygon(): void
    {
        $polygon = new Polygon([]);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('ImmutablePolygonInterface');

        new \ReflectionClass(MultiPolygonImmutable::class)->newInstance([$polygon]);
    }

    /** The wrong immutable spatial kind also gets a library exception. */
    public function testImmutableMultiPolygonRejectsWrongSpatialKind(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(1, 2);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessageIsOrContains('ImmutablePolygonInterface');

        new \ReflectionClass(MultiPolygonImmutable::class)->newInstance([$point]);
    }

    /** XYM components are rejected without confusing their measure with Z. */
    public function testImmutableXymMultiLineStringRejectsMutableLineString(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[1, 2, 9]], 4326);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('ImmutableLineStringInterface');

        new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineStringImmutable::class)->newInstance([$line], 4326);
    }

    /** XYZM patches retain their coordinates and reference after rejection. */
    public function testImmutableXyzmSurfaceRejectsMutablePatch(): void
    {
        $polygon = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([[[1, 2, 3, 4]]], 4326);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('ImmutablePolygonInterface');

        try {
            new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurfaceImmutable::class)->newInstance([$polygon], 2154);
        } finally {
            self::assertSame([[[1, 2, 3, 4]]], $polygon->toArray());
            self::assertSame(4326, $polygon->getSrid());
        }
    }

    /** XYZ Geography follows the same structural contract. */
    public function testImmutableXyzMultiPolygonRejectsMutablePolygon(): void
    {
        $polygon = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([[[1, 2, 3]]], 4326);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('ImmutablePolygonInterface');

        new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygonImmutable::class)->newInstance([$polygon], 4326);
    }

    /** Complete membership stays unchanged when replacing with an immutable member. */
    public function testMutableCollectionRejectsImmutableReplacement(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension2\Point(1, 2, 4326);
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection([$point], 4326);
        $immutable = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(3, 4, 2154);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('MutableSpatialInterface');

        try {
            $source->setElement(0, $immutable);
        } finally {
            self::assertSame([$point], $source->getElements());
            self::assertSame([[1, 2]], $source->toArray());
            self::assertSame([3, 4], $immutable->toArray());
        }
    }

    /** XYM mutable multi-point addition rejects immutable members atomically. */
    public function testMutableMultiPointRejectsImmutableAddition(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1, 2, 9, 4326);
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$point], 4326);
        $replacement = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(3, 4, 8, 2154);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('MutableSpatialInterface');

        try {
            $source->addElement($replacement);
        } finally {
            self::assertSame([$point], $source->getElements());
            self::assertSame([[1, 2, 9]], $source->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([3, 4, 8], $replacement->toArray());
        }
    }

    /** A wrong mutable component kind is still rejected by the member-specific check. */
    public function testMutableMultiPolygonRejectsWrongMemberReplacement(): void
    {
        $polygon = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([[[1, 2, 3, 4]]], 4326);
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([$polygon], 4326);
        $replacement = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(5, 6, 7, 8, 2154);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('mutable Polygon');

        try {
            $source->setElement(0, $replacement);
        } finally {
            self::assertSame([$polygon], $source->getElements());
            self::assertSame([[[[1, 2, 3, 4]]]], $source->toArray());
            self::assertSame([5, 6, 7, 8], $replacement->toArray());
        }
    }

    /** Surface delegation checks the component before invoking the patch API. */
    public function testMutableSurfaceRejectsImmutableRingAddition(): void
    {
        $patch = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([[[1, 2, 3]]], 4326);
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension3z\PolyhedralSurface([$patch], 4326);
        $ring = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[4, 5, 6]], 2154);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('MutableLineStringInterface');

        try {
            $surface->addRing(0, $ring);
        } finally {
            self::assertSame([$patch], $surface->getPatches());
            self::assertSame([[[[1, 2, 3]]]], $surface->toArray());
            self::assertSame([[4, 5, 6]], $ring->toArray());
        }
    }

    /** Surface ring insertion preserves shared mutable descendants after rejection. */
    public function testMutableSurfaceRejectsWrongRingInsertion(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(1, 2, 3, 4326);
        $ring = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([$point], 4326);
        $patch = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([$ring], 4326);
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension3z\PolyhedralSurface([$patch], 4326);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessageIsOrContains('MutableLineStringInterface');

        try {
            $surface->insertRing(0, 1, $point);
        } finally {
            self::assertSame([$ring], $patch->getRings());
            self::assertSame([$point], $ring->getPoints());
            self::assertSame([[[[1, 2, 3]]]], $surface->toArray());
        }
    }

    /** Single-component operations still reject non-spatial programming errors natively. */
    public function testNonSpatialMutationArgumentRetainsTypeError(): void
    {
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\LineString([[1, 2]], 4326);
        $this->expectException(\TypeError::class);

        try {
            new \ReflectionMethod($source, 'addPoint')->invoke($source, new \stdClass());
        } finally {
            self::assertSame([[1, 2]], $source->toArray());
        }
    }

    /** Scalars remain programming errors outside the spatial component contract. */
    public function testScalarMutationArgumentRetainsTypeError(): void
    {
        $source = new \LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint([], 4326);
        $this->expectException(\TypeError::class);

        try {
            new \ReflectionMethod($source, 'addElement')->invoke($source, 7);
        } finally {
            self::assertSame([], $source->getElements());
        }
    }
}
