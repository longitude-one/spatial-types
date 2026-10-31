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
        self::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XY Geography mutable operations. */
    public function testXyGeographyMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XY)->family(SpatialModelEnum::GEOGRAPHY)->mutable();
        $point = $builder->point(0, null);
        self::assertInstanceOf(MutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XY Geometry immutable operations. */
    public function testXyGeometryImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XY)->family(SpatialModelEnum::GEOMETRY);
        $point = $builder->point(0, null);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XY Geometry mutable operations. */
    public function testXyGeometryMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XY)->family(SpatialModelEnum::GEOMETRY)->mutable();
        $point = $builder->point(0, null);
        self::assertInstanceOf(MutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geography immutable operations. */
    public function testXymGeographyImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOGRAPHY);
        $point = $builder->point(0, null);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geography mutable operations. */
    public function testXymGeographyMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOGRAPHY)->mutable();
        $point = $builder->point(0, null);
        self::assertInstanceOf(MutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geometry immutable operations. */
    public function testXymGeometryImmutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOMETRY);
        $point = $builder->point(0, null);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(ImmutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(ImmutableSpatialInterface::class, $progressiveCollection);
    }

    /** Verify all supported XYM Geometry mutable operations. */
    public function testXymGeometryMutableOperationMatrix(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM)->family(SpatialModelEnum::GEOMETRY)->mutable();
        $point = $builder->point(0, null);
        self::assertInstanceOf(MutableSpatialInterface::class, $point);
        self::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $directLineString = $builder->createLineString([]);
        $progressiveLineString = $builder->lineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveLineString);
        $directCircularString = $builder->createCircularString([]);
        $progressiveCircularString = $builder->circularString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCircularString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCircularString);
        $directMultiPoint = $builder->createMultiPoint([]);
        $progressiveMultiPoint = $builder->multiPoint()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPoint);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPoint);
        $directCompoundCurve = $builder->createCompoundCurve([]);
        $progressiveCompoundCurve = $builder->compoundCurve()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCompoundCurve);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCompoundCurve);
        $directPolygon = $builder->createPolygon([]);
        $progressivePolygon = $builder->polygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressivePolygon);
        $directTriangle = $builder->createTriangle([]);
        $progressiveTriangle = $builder->triangle()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directTriangle);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveTriangle);
        $directMultiLineString = $builder->createMultiLineString([]);
        $progressiveMultiLineString = $builder->multiLineString()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiLineString);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiLineString);
        $directMultiPolygon = $builder->createMultiPolygon([]);
        $progressiveMultiPolygon = $builder->multiPolygon()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directMultiPolygon);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveMultiPolygon);
        $directCollection = $builder->createCollection([]);
        $progressiveCollection = $builder->collection()->build();
        self::assertInstanceOf(MutableSpatialInterface::class, $directCollection);
        self::assertInstanceOf(MutableSpatialInterface::class, $progressiveCollection);
    }
}
