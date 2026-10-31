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

        self::assertInstanceOf(LineStringImmutable::class, $line->build());
        self::assertSame(0, $line->build()->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $line->build()->getFamily());
        self::assertInstanceOf(Point::class, $other->point(0, null));
    }

    /**
     * Verify collections select family and representation.
     */
    public function testCollectionsSelectFamilyAndRepresentation(): void
    {
        $immutable = SpatialBuilder::create()->family(SpatialModelEnum::GEOGRAPHY)->collection()->build();
        $mutable = SpatialBuilder::create()->mutable()->family(SpatialModelEnum::GEOGRAPHY)->collection()->build();

        self::assertInstanceOf(GeographyCollectionImmutable::class, $immutable);
        self::assertInstanceOf(SpatialCollection::class, $mutable);
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
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

        self::assertNotSame($source, $mutable);
        self::assertInstanceOf(PointImmutable::class, $source->point(1, 2));
        self::assertInstanceOf(Point::class, $mutable->point(1, 2));
        self::assertInstanceOf(PointImmutable::class, $immutable->point(1, 2));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $geography->point(1, 2)->getFamily());
        self::assertInstanceOf(GeographyPointM::class, $measured->point(1, 2, m: 3));
        self::assertSame(0, $measured->point(1, 2, m: 3)->getSrid());
        self::assertSame(SpatialReference::epsg(4326)->authority, $referenced->point(1, 2, m: 3)->getSpatialReference()->authority);
    }

    /**
     * Verify default context creates immutable xy geometry.
     */
    public function testDefaultContextCreatesImmutableXyGeometry(): void
    {
        $builder = SpatialBuilder::create();
        $point = $builder->point(0, null);
        self::assertInstanceOf(PointImmutable::class, $point);
        self::assertSame([0, null], $point->toArray());
        self::assertSame(0, $point->getSrid());
        self::assertInstanceOf(PointImmutable::class, $builder->point(1, 2));
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

        self::assertInstanceOf(LineStringImmutable::class, $line);
        self::assertInstanceOf(CircularStringImmutable::class, $circular);
        self::assertInstanceOf(MultiPointImmutable::class, $builder->createMultiPoint([$point]));
        self::assertInstanceOf(CompoundCurveImmutable::class, $builder->createCompoundCurve([$line, $circular]));
        self::assertInstanceOf(PolygonImmutable::class, $polygon);
        self::assertInstanceOf(TriangleImmutable::class, $builder->createTriangle([$line]));
        self::assertInstanceOf(MultiLineStringImmutable::class, $builder->createMultiLineString([$line]));
        self::assertInstanceOf(MultiPolygonImmutable::class, $builder->createMultiPolygon([$polygon]));
        self::assertInstanceOf(GeometryCollectionImmutable::class, $builder->createCollection([$point, $line]));
        self::assertSame([[0, null]], $line->toArray());
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
            self::fail('Unavailable Z must be rejected.');
        } catch (InvalidDimensionException) {
            self::assertTrue(SpatialFactory::isMutable());
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

        self::assertInstanceOf(PolyhedralSurfaceInterface::class, $progressive);
        self::assertSame(CoordinateDimensionEnum::XYZM, $direct->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $direct->getFamily());
        self::assertSame($patch, $direct->getElements()[0]);
        self::assertSame($patch, $progressive->getElements()[0]);
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

        self::assertInstanceOf(PointImmutable::class, $immutable);
        self::assertInstanceOf(Point::class, $mutable);
        self::assertFalse(SpatialFactory::isMutable());
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

        self::assertSame(CoordinateDimensionEnum::XYM, $polygon->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $polygon->getFamily());
        self::assertSame($reference, $polygon->getSpatialReference());
        self::assertSame($reference, $ring->getSpatialReference());
        self::assertSame($reference, $point->getSpatialReference());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame([0, null, 7], $point->toArray());
    }

    /**
     * Verify named m and unavailable axes.
     */
    public function testNamedMeasureAndUnavailableAxes(): void
    {
        $measured = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYM);
        self::assertSame([1, null, 3], $measured->point(1, null, m: 3)->toArray());
        self::assertSame([], $measured->point()->toArray());
        $elevated = SpatialBuilder::create()->dimension(CoordinateDimensionEnum::XYZ);
        self::assertInstanceOf(GeometryPointZ::class, $elevated->point(1, 2, z: 3));
        $four = SpatialBuilder::create()->mutable()->dimension(CoordinateDimensionEnum::XYZM);
        self::assertInstanceOf(MutablePointZM::class, $four->point(1, 2, 3, 4));

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

        self::assertInstanceOf(PolyhedralSurfaceImmutable::class, $direct);
        self::assertInstanceOf(PolyhedralSurfaceImmutable::class, $progressive);
        self::assertSame($patch, $direct->getElements()[0]);
        self::assertSame($patch, $progressive->getElements()[0]);
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
        self::assertInstanceOf(LineStringImmutable::class, $line);
        self::assertInstanceOf(CircularStringImmutable::class, $circular);
        $polygon = $builder->polygon()->addRing($line)->build();

        self::assertInstanceOf(MultiPointImmutable::class, $builder->multiPoint()->addPoint(0, null)->build());
        self::assertInstanceOf(CompoundCurveImmutable::class, $builder->compoundCurve()->addCurve($line)->addCurve($circular)->build());
        self::assertInstanceOf(PolygonImmutable::class, $polygon);
        self::assertInstanceOf(TriangleImmutable::class, $builder->triangle()->addRing($line)->build());
        self::assertInstanceOf(MultiLineStringImmutable::class, $builder->multiLineString()->addLineString($line)->build());
        self::assertInstanceOf(MultiPolygonImmutable::class, $builder->multiPolygon()->addPolygon($polygon)->build());
        self::assertInstanceOf(GeometryCollectionImmutable::class, $builder->collection()->addElement($point)->build());
    }

    /**
     * Verify rejected addition leaves accumulated state untouched.
     */
    public function testRejectedAdditionLeavesAccumulatedStateUntouched(): void
    {
        $line = SpatialBuilder::create()->lineString()->addPoint(1, 2);

        try {
            $line->addPointComponent(new Point(3, 4));
            self::fail('A mutable point must not enter an immutable line.');
        } catch (InvalidValueException) {
            self::assertSame([[1, 2]], $line->build()->toArray());
        }
    }

    /**
     * Verify selection and repeated build retain accumulation.
     */
    public function testSelectionAndRepeatedBuildRetainAccumulation(): void
    {
        $source = SpatialBuilder::create();
        $line = $source->lineString();
        self::assertNotSame($source, $line);
        self::assertSame($line, $line->addPoint(1, 2));
        $first = $line->build();
        $second = $line->build();
        $line->addPoint(3, 4);
        $third = $line->build();

        self::assertInstanceOf(LineStringImmutable::class, $first);
        self::assertNotSame($first, $second);
        self::assertSame([[1, 2]], $first->toArray());
        self::assertSame([[1, 2], [3, 4]], $third->toArray());
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

        self::assertInstanceOf(LineString::class, $first);
        self::assertInstanceOf(LineString::class, $second);
        self::assertSame($point, $first->getPoints()[0]);
        self::assertSame($point, $second->getPoints()[0]);
        self::assertSame([[5, 2]], $first->toArray());
        self::assertNotSame($first, $second);
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
