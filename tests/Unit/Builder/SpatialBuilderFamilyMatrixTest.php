<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
final class SpatialBuilderFamilyMatrixTest extends TestCase
{
    /** Verify all supported XYZ Geography immutable operations. */
    public function testXyzGeographyImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZ)->family(SpatialModelEnum::GEOGRAPHY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolyhedralSurface);
    }

    /** Verify all supported XYZ Geography mutable operations. */
    public function testXyzGeographyMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZ)->family(SpatialModelEnum::GEOGRAPHY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolyhedralSurface);
    }

    /** Verify all supported XYZ Geometry immutable operations. */
    public function testXyzGeometryImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZ)->family(SpatialModelEnum::GEOMETRY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolyhedralSurface);
    }

    /** Verify all supported XYZ Geometry mutable operations. */
    public function testXyzGeometryMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZ)->family(SpatialModelEnum::GEOMETRY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolyhedralSurface);
    }

    /** Verify all supported XYZM Geography immutable operations. */
    public function testXyzmGeographyImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZM)->family(SpatialModelEnum::GEOGRAPHY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolyhedralSurface);
    }

    /** Verify all supported XYZM Geography mutable operations. */
    public function testXyzmGeographyMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZM)->family(SpatialModelEnum::GEOGRAPHY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolyhedralSurface);
    }

    /** Verify all supported XYZM Geometry immutable operations. */
    public function testXyzmGeometryImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZM)->family(SpatialModelEnum::GEOMETRY);
        $point = $builder->point(0, null);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(ImmutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolyhedralSurface);
    }

    /** Verify all supported XYZM Geometry mutable operations. */
    public function testXyzmGeometryMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZM)->family(SpatialModelEnum::GEOMETRY)->mutable();
        $point = $builder->point(0, null);
        static::assertInstanceOf(MutableSpatialInterface::class, $point);
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
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
        $directPolyhedralSurface = $builder->createPolyhedralSurface([]);
        $progressivePolyhedralSurface = $builder->polyhedralSurface()->build();
        static::assertInstanceOf(MutableSpatialInterface::class, $directPolyhedralSurface);
        static::assertInstanceOf(MutableSpatialInterface::class, $progressivePolyhedralSurface);
    }
}
