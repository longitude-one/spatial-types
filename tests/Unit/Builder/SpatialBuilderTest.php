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
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Interfaces\PolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollectionImmutable;
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
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable as GeographyPointM;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable as GeometryPointZ;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurfaceImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Point as MutablePointZM;
use PHPUnit\Framework\TestCase;

/**
 * @internal @coversNothing
 *
 * @coversNothing
 */
final class SpatialBuilderTest extends TestCase
{
    /**
     * Restore the shared factory mode.
     */
    protected function tearDown(): void
    {
        SpatialFactory::immutable();
        parent::tearDown();
    }

    /** Verify a configured context cannot change an already selected aggregate. */
    public function testAggregateRetainsItsOriginalContext(): void
    {
        $source = SpatialBuilder::create();
        $line = $source->lineString();
        $other = $source->mutable()->family(SpatialModelEnum::GEOGRAPHY)->srid(4326);
        $line->addPoint(0, null);

        static::assertInstanceOf(LineStringImmutable::class, $line->build());
        static::assertSame(0, $line->build()->getSrid());
        static::assertSame(SpatialModelEnum::GEOMETRY, $line->build()->getFamily());
        static::assertInstanceOf(Point::class, $other->point(0, null));
    }

    /**
     * Verify collections select family and representation.
     */
    public function testCollectionsSelectFamilyAndRepresentation(): void
    {
        $immutable = SpatialBuilder::create()->family(SpatialModelEnum::GEOGRAPHY)->collection()->build();
        $mutable = SpatialBuilder::create()->mutable()->family(SpatialModelEnum::GEOGRAPHY)->collection()->build();

        static::assertInstanceOf(GeographyCollectionImmutable::class, $immutable);
        static::assertInstanceOf(SpatialCollection::class, $mutable);
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
    }

    /**
     * Verify configuration copies preserve their sources.
     */
    public function testConfigurationCopiesPreserveTheirSources(): void
    {
        $source = SpatialBuilder::create();
        $mutable = $source->mutable();
        $geography = $source->family(SpatialModelEnum::GEOGRAPHY);
        $measured = $geography->dimension(CoordinateDimensionEnum::XYM);
        $referenced = $measured->srid(SpatialReference::epsg(4326));
        $immutable = $mutable->immutable();

        static::assertNotSame($source, $mutable);
        static::assertInstanceOf(PointImmutable::class, $source->point(1, 2));
        static::assertInstanceOf(Point::class, $mutable->point(1, 2));
        static::assertInstanceOf(PointImmutable::class, $immutable->point(1, 2));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $geography->point(1, 2)->getFamily());
        static::assertInstanceOf(GeographyPointM::class, $measured->point(1, 2, m: 3));
        static::assertSame(0, $measured->point(1, 2, m: 3)->getSrid());
        static::assertSame(SpatialReference::epsg(4326)->authority, $referenced->point(1, 2, m: 3)->getSpatialReference()->authority);
    }

    /**
     * Verify default context creates immutable xy geometry.
     */
    public function testDefaultContextCreatesImmutableXyGeometry(): void
    {
        $builder = SpatialBuilder::create();
        $point = $builder->point(0, null);
        static::assertInstanceOf(PointImmutable::class, $point);
        static::assertSame([0, null], $point->toArray());
        static::assertSame(0, $point->getSrid());
        static::assertInstanceOf(PointImmutable::class, $builder->point(1, 2));
    }

    /**
     * Verify direct aggregate matrix in xy geometry.
     */
    public function testDirectAggregateMatrixInXyGeometry(): void
    {
        $builder = SpatialBuilder::create();
        $point = $builder->point(0, null);
        $line = $builder->createLineString([$point]);
        $circular = $builder->createCircularString([[$point->getX(), $point->getY()]]);
        $polygon = $builder->createPolygon([$line]);

        static::assertInstanceOf(LineStringImmutable::class, $line);
        static::assertInstanceOf(CircularStringImmutable::class, $circular);
        static::assertInstanceOf(MultiPointImmutable::class, $builder->createMultiPoint([$point]));
        static::assertInstanceOf(CompoundCurveImmutable::class, $builder->createCompoundCurve([$line, $circular]));
        static::assertInstanceOf(PolygonImmutable::class, $polygon);
        static::assertInstanceOf(TriangleImmutable::class, $builder->createTriangle([$line]));
        static::assertInstanceOf(MultiLineStringImmutable::class, $builder->createMultiLineString([$line]));
        static::assertInstanceOf(MultiPolygonImmutable::class, $builder->createMultiPolygon([$polygon]));
        static::assertInstanceOf(GeometryCollectionImmutable::class, $builder->createCollection([$point, $line]));
        static::assertSame([[0, null]], $line->toArray());
    }

    /** Verify opposite-representation components are rejected by direct creation. */
    public function testDirectCreationRejectsOppositeRepresentation(): void
    {
        $builder = SpatialBuilder::create()->mutable();
        $point = new PointImmutable(1, 2);

        $this->expectException(InvalidValueException::class);
        $builder->createLineString([$point]);
    }

    /**
     * Verify failed creation does not change global mode.
     */
    public function testFailedCreationDoesNotChangeGlobalMode(): void
    {
        SpatialFactory::mutable();

        try {
            SpatialBuilder::create()->point(1, 2, z: 3);
            static::fail('Unavailable Z must be rejected.');
        } catch (InvalidDimensionException) {
            static::assertTrue(SpatialFactory::isMutable());
        }
    }

    /** Verify XYZM Geography supports direct and progressive surface creation. */
    public function testFourOrdinateGeographySupportsSurfaces(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZM)
            ->family(SpatialModelEnum::GEOGRAPHY)
        ;
        $patch = $builder->createPolygon([[[0, 0, 1, 2]]]);
        $direct = $builder->createPolyhedralSurface([$patch]);
        $progressive = $builder->polyhedralSurface()->addPatch($patch)->build();

        static::assertInstanceOf(PolyhedralSurfaceInterface::class, $progressive);
        static::assertSame(CoordinateDimensionEnum::XYZM, $direct->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $direct->getFamily());
        static::assertSame($patch, $direct->getElements()[0]);
        static::assertSame($patch, $progressive->getElements()[0]);
    }

    /**
     * Verify global factory mode does not affect builder mode in either direction.
     */
    public function testGlobalFactoryModeDoesNotAffectBuilderModeInEitherDirection(): void
    {
        SpatialFactory::mutable();
        $immutable = SpatialBuilder::create()->point(1, 2);
        $mutableBuilder = SpatialBuilder::create()->mutable();
        SpatialFactory::immutable();
        $mutable = $mutableBuilder->point(1, 2);

        static::assertInstanceOf(PointImmutable::class, $immutable);
        static::assertInstanceOf(Point::class, $mutable);
        static::assertFalse(SpatialFactory::isMutable());
    }

    /** Verify nested coordinate input uses the local mutable context. */
    public function testMutableNestedCoordinateInputUsesSelectedContext(): void
    {
        $reference = SpatialReference::epsg(4326);
        $builder = SpatialBuilder::create()->mutable()->dimension(CoordinateDimensionEnum::XYM)
            ->family(SpatialModelEnum::GEOGRAPHY)->srid($reference)
        ;
        $polygon = $builder->createPolygon([[[0, null, 7], [1, 2, 8]]]);
        $ring = $polygon->getRings()[0];
        $point = $ring->getPoints()[0];

        static::assertSame(CoordinateDimensionEnum::XYM, $polygon->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygon->getFamily());
        static::assertSame($reference, $polygon->getSpatialReference());
        static::assertSame($reference, $ring->getSpatialReference());
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame([0, null, 7], $point->toArray());
    }

    /**
     * Verify named m and unavailable axes.
     */
    public function testNamedMeasureAndUnavailableAxes(): void
    {
        $measured = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM);
        static::assertSame([1, null, 3], $measured->point(1, null, m: 3)->toArray());
        static::assertSame([], $measured->point()->toArray());
        $elevated = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZ);
        static::assertInstanceOf(GeometryPointZ::class, $elevated->point(1, 2, z: 3));
        $four = SpatialBuilder::create()->mutable()->dimension(CoordinateDimensionEnum::XYZM);
        static::assertInstanceOf(MutablePointZM::class, $four->point(1, 2, 3, 4));

        $this->expectException(InvalidDimensionException::class);
        $measured->point(1, 2, z: 3);
    }

    /**
     * Verify polyhedral surface direct and progressive creation.
     */
    public function testPolyhedralSurfaceDirectAndProgressiveCreation(): void
    {
        $builder = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZ);
        $ring = $builder->createLineString([[0, 0, 0]]);
        $patch = $builder->createPolygon([$ring]);
        $direct = $builder->createPolyhedralSurface([$patch]);
        $progressive = $builder->polyhedralSurface()->addPatch($patch)->build();

        static::assertInstanceOf(PolyhedralSurfaceImmutable::class, $direct);
        static::assertInstanceOf(PolyhedralSurfaceImmutable::class, $progressive);
        static::assertSame($patch, $direct->getElements()[0]);
        static::assertSame($patch, $progressive->getElements()[0]);
    }

    /**
     * Verify polyhedral surface requires z layout.
     */
    public function testPolyhedralSurfaceRequiresElevationLayout(): void
    {
        $this->expectException(InvalidDimensionException::class);
        SpatialBuilder::create()->polyhedralSurface();
    }

    /**
     * Verify progressive aggregate matrix in xy geometry.
     */
    public function testProgressiveAggregateMatrixInXyGeometry(): void
    {
        $builder = SpatialBuilder::create();
        $point = $builder->point(0, null);
        $line = $builder->lineString()->addPointComponent($point)->build();
        $circular = $builder->circularString()->addPoint(0, null)->build();
        static::assertInstanceOf(LineStringImmutable::class, $line);
        static::assertInstanceOf(CircularStringImmutable::class, $circular);
        $polygon = $builder->polygon()->addRing($line)->build();

        static::assertInstanceOf(MultiPointImmutable::class, $builder->multiPoint()->addPoint(0, null)->build());
        static::assertInstanceOf(CompoundCurveImmutable::class, $builder->compoundCurve()->addCurve($line)->addCurve($circular)->build());
        static::assertInstanceOf(PolygonImmutable::class, $polygon);
        static::assertInstanceOf(TriangleImmutable::class, $builder->triangle()->addRing($line)->build());
        static::assertInstanceOf(MultiLineStringImmutable::class, $builder->multiLineString()->addLineString($line)->build());
        static::assertInstanceOf(MultiPolygonImmutable::class, $builder->multiPolygon()->addPolygon($polygon)->build());
        static::assertInstanceOf(GeometryCollectionImmutable::class, $builder->collection()->addElement($point)->build());
    }

    /**
     * Verify rejected addition leaves accumulated state untouched.
     */
    public function testRejectedAdditionLeavesAccumulatedStateUntouched(): void
    {
        $line = SpatialBuilder::create()->lineString()->addPoint(1, 2);

        try {
            $line->addPointComponent(new Point(3, 4));
            static::fail('A mutable point must not enter an immutable line.');
        } catch (InvalidValueException) {
            static::assertSame([[1, 2]], $line->build()->toArray());
        }
    }

    /**
     * Verify selection and repeated build retain accumulation.
     */
    public function testSelectionAndRepeatedBuildRetainAccumulation(): void
    {
        $source = SpatialBuilder::create();
        $line = $source->lineString();
        static::assertNotSame($source, $line);
        static::assertSame($line, $line->addPoint(1, 2));
        $first = $line->build();
        $second = $line->build();
        $line->addPoint(3, 4);
        $third = $line->build();

        static::assertInstanceOf(LineStringImmutable::class, $first);
        static::assertNotSame($first, $second);
        static::assertSame([[1, 2]], $first->toArray());
        static::assertSame([[1, 2], [3, 4]], $third->toArray());
    }

    /**
     * Verify supplied mutable point is shared across builds.
     */
    public function testSuppliedMutablePointIsSharedAcrossBuilds(): void
    {
        $point = new Point(1, 2);
        $line = SpatialBuilder::create()->mutable()->lineString();
        $line->addPointComponent($point);
        $first = $line->build();
        $second = $line->build();
        $point->setX(5);

        static::assertInstanceOf(LineString::class, $first);
        static::assertInstanceOf(LineString::class, $second);
        static::assertSame($point, $first->getPoints()[0]);
        static::assertSame($point, $second->getPoints()[0]);
        static::assertSame([[5, 2]], $first->toArray());
        static::assertNotSame($first, $second);
    }

    /**
     * Verify wrong aggregate addition is rejected.
     */
    public function testWrongAggregateAdditionIsRejected(): void
    {
        $builder = SpatialBuilder::create()->polygon();
        $this->expectException(InvalidValueException::class);
        $builder->addPoint(1, 2);
    }
}
