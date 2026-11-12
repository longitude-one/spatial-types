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

use LongitudeOne\SpatialTypes\Builder\SpatialBuilder;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use PHPUnit\Framework\TestCase;

/** Builder component mutations follow the geometry exception contract.
 * @internal
 *
 * @coversNothing
 */
class Issue79BuilderTest extends TestCase
{
    /** Correct spatial kinds still enforce the selected representation without conversion. */
    public function testMutablePolygonDoesNotEnterImmutableBuilder(): void
    {
        $builder = SpatialBuilder::create()->multiPolygon();
        $polygon = new Polygon([[[1, 2]]], 4326);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessage('exclusively immutable spatial components');

        try {
            $builder->addPolygon($polygon);
        } finally {
            self::assertSame([], $builder->build()->toArray());
            self::assertSame([[[1, 2]]], $polygon->toArray());
            self::assertSame(4326, $polygon->getSrid());
        }
    }

    /** A nested compound curve does not become an accepted builder component. */
    public function testNestedCurveAdditionIsRejected(): void
    {
        $builder = SpatialBuilder::create()->compoundCurve();
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessage('CircularStringInterface or LineStringInterface');

        try {
            $builder->addCurve(new CompoundCurveImmutable([]));
        } finally {
            self::assertSame([], $builder->build()->toArray());
        }
    }

    /** Non-spatial additions remain PHP programming errors. */
    public function testNonSpatialBuilderArgumentRetainsTypeError(): void
    {
        $builder = SpatialBuilder::create()->polygon();
        $this->expectException(\TypeError::class);

        try {
            new \ReflectionMethod($builder, 'addRing')->invoke($builder, new \stdClass());
        } finally {
            self::assertSame([], $builder->build()->toArray());
        }
    }

    /** Multi-line-string composition rejects point components. */
    public function testWrongLineStringAdditionIsRejected(): void
    {
        $builder = SpatialBuilder::create()->multiLineString();
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessage('LineStringInterface');

        try {
            $builder->addLineString(new PointImmutable(1, 2));
        } finally {
            self::assertSame([], $builder->build()->toArray());
        }
    }

    /** Surface composition rejects point components. */
    public function testWrongPatchAdditionIsRejected(): void
    {
        $builder = SpatialBuilder::create()->dimension(\LongitudeOne\Core\Enum\CoordinateDimensionEnum::XYZ)->polyhedralSurface();
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessage('PolygonInterface');

        try {
            $builder->addPatch(new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, 3));
        } finally {
            self::assertSame([], $builder->build()->toArray());
        }
    }

    /** Point composition rejects polygon components before changing the builder. */
    public function testWrongPointAdditionIsRejected(): void
    {
        $builder = SpatialBuilder::create()->lineString()->addPoint(1, 2);
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessage('PointInterface');

        try {
            $builder->addPointComponent(new PolygonImmutable([]));
        } finally {
            self::assertSame([[1, 2]], $builder->build()->toArray());
        }
    }

    /** Polygon composition rejects point components through the spatial contract. */
    public function testWrongPolygonAdditionIsRejected(): void
    {
        $builder = SpatialBuilder::create()->multiPolygon();
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessage('PolygonInterface');

        try {
            $builder->addPolygon(new PointImmutable(1, 2));
        } finally {
            self::assertSame([], $builder->build()->toArray());
        }
    }

    /** Ring composition rejects point components through the spatial contract. */
    public function testWrongRingAdditionIsRejected(): void
    {
        $builder = SpatialBuilder::create()->polygon();
        $this->expectException(SpatialTypeExceptionInterface::class);
        $this->expectExceptionMessage('LineStringInterface');

        try {
            $builder->addRing(new PointImmutable(1, 2));
        } finally {
            self::assertSame([], $builder->build()->toArray());
        }
    }
}
