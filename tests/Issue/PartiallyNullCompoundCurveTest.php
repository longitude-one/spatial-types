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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for the compound-curve shared path in issue #36.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCompoundCurve
 */
class PartiallyNullCompoundCurveTest extends TestCase
{
    /** Null ordinates survive construction, access and independent reference copies. */
    public function testElevationGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new LineString([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new CircularString([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZ, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(CompoundCurve::class, $copy::class);
        static::assertSame(LineString::class, $copy->getCurve(0)::class);
        static::assertSame(CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testElevationGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularString([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZ, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurve::class, $copy::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString::class, $copy->getCurve(0)::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** An EMPTY member keeps its empty tuple beside a partially supplied point. */
    public function testEmptyPointMemberRemainsEmptyDuringReferenceCopy(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString([[0, null, 3], [null, null, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurve([$line], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));

        static::assertFalse($compound->isEmpty());
        static::assertSame([[[0, null, 3], []]], $compound->toArray());
        static::assertSame([[[0, null, 3], []]], $copy->toArray());
        static::assertTrue($compound->getEndPoint()?->isEmpty());
        static::assertTrue($copy->getEndPoint()?->isEmpty());
        static::assertSame(CoordinateDimensionEnum::XYM, $copy->getEndPoint()->getDimension());
        static::assertSame(4326, $compound->getEndPoint()->getSrid());
        static::assertSame(3857, $copy->getEndPoint()->getSrid());
        static::assertNotSame($compound->getEndPoint(), $copy->getEndPoint());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testFourDimensionalGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString([[0, null, 3.5, null], [null, 2, null, 4]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularString([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $compound->toArray());
        static::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $copy->toArray());
        static::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null, 3.5, null], [null, 2, null, 4]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurve::class, $copy::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString::class, $copy->getCurve(0)::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testFourDimensionalGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString([[0, null, 3.5, null], [null, 2, null, 4]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularString([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $compound->toArray());
        static::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $copy->toArray());
        static::assertSame([[[0, null, 3.5, null], [null, 2, null, 4]], [[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null, 3.5, null], [null, 2, null, 4]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2, null, 4], [1.5, null, 3, null], [0, 5, null, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurve::class, $copy::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString::class, $copy->getCurve(0)::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testMeasureGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularString([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XYM, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurve::class, $copy::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString::class, $copy->getCurve(0)::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testMeasureGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString([[0, null, null], [null, 2, 3.5]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularString([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $compound->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $copy->toArray());
        static::assertSame([[[0, null, null], [null, 2, 3.5]], [[null, 2, 3.5], [1.5, null, 4], [0, 5, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null, null], [null, 2, 3.5]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2, 3.5], [1.5, null, 4], [0, 5, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XYM, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurve::class, $copy::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString::class, $copy->getCurve(0)::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testPlanarGeographyComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString([[0, null], [null, 2]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CircularString([[null, 2], [1.5, null], [0, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $compound->toArray());
        static::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $copy->toArray());
        static::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null], [null, 2]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2], [1.5, null], [0, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CompoundCurve::class, $copy::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString::class, $copy->getCurve(0)::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }

    /** Null ordinates survive construction, access and independent reference copies. */
    public function testPlanarGeometryComponentsPreserveNullOrdinates(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString([[0, null], [null, 2]], 4326);
        $circular = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString([[null, 2], [1.5, null], [0, null]], 4326);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurve([$line, $circular], 4326);
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));
        $legacyCopy = $compound->withSrid(2154);

        static::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $compound->toArray());
        static::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $copy->toArray());
        static::assertSame([[[0, null], [null, 2]], [[null, 2], [1.5, null], [0, null]]], $legacyCopy->toArray());
        static::assertSame([[0, null], [null, 2]], $copy->getCurve(0)->toArray());
        static::assertSame([[null, 2], [1.5, null], [0, null]], $copy->getCurve(1)->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $copy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $copy->getFamily());
        static::assertFalse($compound->isEmpty());
        static::assertNotSame($compound, $copy);
        static::assertNotSame($line, $copy->getCurve(0));
        static::assertNotSame($line->getPoint(0), $copy->getStartPoint());
        static::assertNotSame($circular, $copy->getCurve(1));
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurve::class, $copy::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString::class, $copy->getCurve(0)::class);
        static::assertSame(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString::class, $copy->getCurve(1)::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertSame(2154, $legacyCopy->getStartPoint()?->getSrid());
    }
}
