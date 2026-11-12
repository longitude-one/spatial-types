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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Builder;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Builder\SpatialBuilder;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal @coversNothing
 *
 * @coversNothing
 */
final class SpatialBuilderLayoutMatrixTest extends TestCase
{
    /** Verify all supported XY Geography immutable operations. */
    public function testXyGeographyImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XY)->family(SpatialModelEnum::GEOGRAPHY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XY Geography mutable operations. */
    public function testXyGeographyMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XY)->family(SpatialModelEnum::GEOGRAPHY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XY Geometry immutable operations. */
    public function testXyGeometryImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XY)->family(SpatialModelEnum::GEOMETRY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XY Geometry mutable operations. */
    public function testXyGeometryMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XY)->family(SpatialModelEnum::GEOMETRY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geography immutable operations. */
    public function testXymGeographyImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOGRAPHY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geography mutable operations. */
    public function testXymGeographyMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOGRAPHY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geometry immutable operations. */
    public function testXymGeometryImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOMETRY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geometry mutable operations. */
    public function testXymGeometryMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOMETRY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }
}
