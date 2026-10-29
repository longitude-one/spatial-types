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
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\TriangleImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurfaceImmutable;
use PHPUnit\Framework\TestCase;

/** Mutable components never become part of an immutable aggregate.
 * @internal
 *
 * @coversNothing
 */
class ImmutableComponentContractTest extends TestCase
{
    /** CircularString rejects mutable input without conversion. */
    public function testCircularStringRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutablePointInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(CircularStringImmutable::class))->newInstance([$mutable]);
    }

    /** Collection rejects mutable input without conversion. */
    public function testCollectionRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutablePointInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(GeometryCollectionImmutable::class))->newInstance(0, [$mutable]);
    }

    /** CompoundCurve rejects mutable input without conversion. */
    public function testCompoundCurveRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutableCurveInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(CompoundCurveImmutable::class))->newInstance([$mutable]);
    }

    /** XY tuples produce immutable points while preserving partial nulls. */
    public function testCoordinateTuplesCreateImmutableDescendants(): void
    {
        $source = new MultiPolygonImmutable([[[[0, null], [2, 3]]]], 4326);
        $copy = $source->withSpatialReference(SpatialReference::epsg(2154));

        static::assertTrue(is_a((new \ReflectionClass($copy->getPolygon(0)))->getName(), ImmutableSpatialInterface::class, true));
        static::assertTrue(is_a((new \ReflectionClass($copy->getPolygon(0)->getRing(0)))->getName(), ImmutableSpatialInterface::class, true));
        static::assertTrue(is_a((new \ReflectionClass($copy->getPolygon(0)->getRing(0)->getPoint(0)))->getName(), ImmutableSpatialInterface::class, true));
        static::assertSame([[[[0, null], [2, 3]]]], $copy->toArray());
        static::assertSame(2154, $copy->getPolygon(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame(4326, $source->getPolygon(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertNotSame($source->getPolygon(0), $copy->getPolygon(0));
        static::assertNotSame($source->getPolygon(0)->getRing(0), $copy->getPolygon(0)->getRing(0));
        static::assertNotSame($source->getPolygon(0)->getRing(0)->getPoint(0), $copy->getPolygon(0)->getRing(0)->getPoint(0));
    }

    /** LineString rejects mutable input without conversion. */
    public function testLineStringRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutablePointInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(LineStringImmutable::class))->newInstance([$mutable]);
    }

    /** MultiLineString rejects mutable input without conversion. */
    public function testMultiLineStringRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutableLineStringInterface::class);
        $this->expectException(\TypeError::class);

        (new \ReflectionClass(MultiLineStringImmutable::class))->newInstance([$mutable]);
    }

    /** MultiPoint rejects mutable input without conversion. */
    public function testMultiPointRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutablePointInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(MultiPointImmutable::class))->newInstance([$mutable]);
    }

    /** MultiPolygon rejects mutable input without conversion. */
    public function testMultiPolygonRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutablePolygonInterface::class);
        $this->expectException(\TypeError::class);

        (new \ReflectionClass(MultiPolygonImmutable::class))->newInstance([$mutable]);
    }

    /** A marker cannot hide mutable descendants of a custom collection. */
    public function testNestedMutableComponentIsRejected(): void
    {
        $mutable = static::createStub(MutablePointInterface::class);
        $nested = static::createStub(ImmutableCollectionInterface::class);
        $nested->method('getElements')->willReturn([$mutable]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Immutable aggregates require exclusively immutable spatial components.');

        new GeometryCollectionImmutable(0, [$nested]);
    }

    /** NestedPointArray rejects mutable input without conversion. */
    public function testNestedPointArrayRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutablePointInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(PolygonImmutable::class))->newInstance([[$mutable]]);
    }

    /** Polygon rejects mutable input without conversion. */
    public function testPolygonRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutableLineStringInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(PolygonImmutable::class))->newInstance([$mutable]);
    }

    /** PolyhedralSurface rejects mutable input without conversion. */
    public function testPolyhedralSurfaceRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutablePolygonInterface::class);
        $this->expectException(\TypeError::class);

        (new \ReflectionClass(PolyhedralSurfaceImmutable::class))->newInstance([$mutable]);
    }

    /** A rejected replacement preserves source membership and supplied objects. */
    public function testRejectedReplacementDoesNotChangeTheSource(): void
    {
        $point = new PointImmutable(0, null, 4326);
        $source = new GeometryCollectionImmutable(4326, [$point]);
        $mutable = static::createStub(MutablePointInterface::class);
        $mutable->method('getX')->willReturn(7);
        $nested = static::createStub(ImmutableCollectionInterface::class);
        $nested->method('getElements')->willReturn([$mutable]);
        $this->expectException(InvalidValueException::class);

        try {
            $source->withElement(0, $nested);
        } finally {
            static::assertSame([$point], $source->getElements());
            static::assertSame([[0, null]], $source->toArray());
            static::assertSame(4326, $source->getSrid());
            static::assertSame(7, $mutable->getX());
        }
    }

    /** Triangle rejects mutable input without conversion. */
    public function testTriangleRejectsMutableComponents(): void
    {
        $mutable = static::createStub(MutableLineStringInterface::class);
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(TriangleImmutable::class))->newInstance([$mutable]);
    }
}
