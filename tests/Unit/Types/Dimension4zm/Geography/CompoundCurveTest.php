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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension4zm\Geography;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\CompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString;
use PHPUnit\Framework\TestCase;

/**
 * Public compound curves in XYZM Geography.
 *
 * @internal
 *
 * @coversNothing
 */
class CompoundCurveTest extends TestCase
{
    /** Empty compounds and component curves expose the common empty contract. */
    public function testEmptyCurves(): void
    {
        $compound = new CompoundCurve([], 4326);
        $line = new LineString([], 4326);
        $circular = new CircularString([], 4326);

        static::assertTrue($compound->isEmpty());
        static::assertNull($compound->getStartPoint());
        static::assertNull($compound->getEndPoint());
        static::assertFalse($compound->isClosed());
        static::assertSame([], $compound->getCurves());
        static::assertSame([], $compound->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $compound->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame(4326, $compound->getSrid());
        static::assertNull($line->getStartPoint());
        static::assertNull($line->getEndPoint());
        static::assertFalse($line->isClosed());
        static::assertNull($circular->getStartPoint());
        static::assertNull($circular->getEndPoint());
        static::assertFalse($circular->isClosed());
    }

    /** Mixed interpolation, order and closure survive through public contracts. */
    public function testMixedClosedCurve(): void
    {
        $circular = new CircularString([[0, 0, 3, 6], [1, 1, 4, 7], [2, 0, 5, 8]], 4326);
        $line = new LineString([[2, 0, 5, 8], [0, 0, 3, 6]], 4326);
        $compound = new CompoundCurve([$circular, $line], 4326);

        static::assertContains(CompoundCurveInterface::class, class_implements($compound));
        static::assertContains(CurveInterface::class, class_implements($compound));
        static::assertSame([$circular, $line], $compound->getCurves());
        static::assertSame([$circular, $line], $compound->getElements());
        static::assertSame($circular, $compound->getCurve(0));
        static::assertSame($line, $compound->getCurve(-1));
        static::assertSame($circular, $compound->getCurve(2));
        static::assertSame($circular->getStartPoint(), $compound->getStartPoint());
        static::assertSame($line->getEndPoint(), $compound->getEndPoint());
        static::assertSame([0, 0, 3, 6], $compound->getStartPoint()?->toArray());
        static::assertTrue($compound->isClosed());
        static::assertFalse($compound->isEmpty());
        static::assertFalse($line->isClosed());
        static::assertFalse($circular->isClosed());
        static::assertSame([2, 0, 5, 8], $circular->getEndPoint()?->toArray());
        static::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        static::assertSame(CoordinateDimensionEnum::XYZM, $compound->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame([[[0, 0, 3, 6], [1, 1, 4, 7], [2, 0, 5, 8]], [[2, 0, 5, 8], [0, 0, 3, 6]]], $compound->toArray());
    }

    /** Reference changes deeply copy components and preserve defining ordinates. */
    public function testReferenceReplacement(): void
    {
        $compound = new CompoundCurve([
            new CircularString([[0, 0, 3, 6], [1, 1, 4, 7], [2, 0, 5, 8]], SpatialReference::epsg(4326)),
            new LineString([[2, 0, 5, 8], [0, 0, 3, 6]], SpatialReference::epsg(4326)),
        ], SpatialReference::epsg(4326));
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));

        static::assertSame(CompoundCurve::class, $copy::class);
        static::assertSame(4326, $compound->getSrid());
        static::assertSame(4326, $compound->getCurve(0)->getSrid());
        static::assertSame(3857, $copy->getSrid());
        static::assertSame('EPSG', $copy->getCurve(0)->getSpatialReference()->authority);
        static::assertSame(3857, $copy->getStartPoint()?->getSrid());
        static::assertSame(3857, $copy->getEndPoint()?->getSrid());
        static::assertNotSame($compound->getCurve(0), $copy->getCurve(0));
        static::assertNotSame($compound->getStartPoint(), $copy->getStartPoint());
        static::assertSame([[[0, 0, 3, 6], [1, 1, 4, 7], [2, 0, 5, 8]], [[2, 0, 5, 8], [0, 0, 3, 6]]], $compound->toArray());
        static::assertSame([[[0, 0, 3, 6], [1, 1, 4, 7], [2, 0, 5, 8]], [[2, 0, 5, 8], [0, 0, 3, 6]]], $copy->toArray());
        static::assertInstanceOf(CircularString::class, $copy->getCurve(0));
        static::assertInstanceOf(LineString::class, $copy->getCurve(1));
        static::assertTrue($copy->isClosed());
    }
}
