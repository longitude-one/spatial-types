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
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for the compound-curve shared path in issue #36.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractCompoundCurveImmutable
 */
class PartiallyNullCompoundCurveTest extends TestCase
{
    /** Null ordinates survive construction, access and independent reference copies. */
    public function testElevationGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new LineStringImmutable([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new CircularStringImmutable([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testElevationGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularStringImmutable([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** An EMPTY member keeps its empty tuple beside a partially supplied point. */
    public function testEmptyPointMemberRemainsEmptyDuringReferenceCopy(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, null, 3], [null, null, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurveImmutable([$line], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));

        self::assertSame([[[0, null, 3], []]], $compound->toArray());
        self::assertSame([[[0, null, 3], []]], $copy->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $copy->getEndPoint()?->getDimension());
        self::assertSame(4326, $compound->getEndPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()->getSrid());
        self::assertNotSame($compound->getEndPoint(), $copy->getEndPoint());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testFourDimensionalGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([[0, null, 3.5, null], [null, 2, null, 4]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularStringImmutable([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $compound->toArray());
        self::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $copy->toArray());
        self::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null, 3.5, null], [null, 2, null, 4]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testFourDimensionalGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([[0, null, 3.5, null], [null, 2, null, 4]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularStringImmutable([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $compound->toArray());
        self::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $copy->toArray());
        self::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null, 3.5, null], [null, 2, null, 4]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testMeasureGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularStringImmutable([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testMeasureGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularStringImmutable([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        self::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testPlanarGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([[0, null], [null, 2]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CircularStringImmutable([[null, 2], [1.5, null], [0, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $compound->toArray());
        self::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $copy->toArray());
        self::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null], [null, 2]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2], [1.5, null], [0, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testPlanarGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable([[0, null], [null, 2]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable([[null, 2], [1.5, null], [0, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        self::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $compound->toArray());
        self::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $copy->toArray());
        self::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $legacyCopy->toArray());
        self::assertSame([[0, null], [null, 2]], $copy->getCurve(0)->toArray());
        self::assertSame([[null, 2], [1.5, null], [0, null]], $copy->getCurve(1)->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $copy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        self::assertNotSame($compound, $copy);
        self::assertNotSame($line, $copy->getCurve(0));
        self::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        self::assertNotSame($circular, $copy->getCurve(1));
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable::class, $copy->getCurve(0)::class);
        self::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable::class, $copy->getCurve(1)::class);
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $line->getPoint(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }
}
