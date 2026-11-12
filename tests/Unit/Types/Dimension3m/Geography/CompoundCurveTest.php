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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3m\Geography;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\CompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Public compound curves in XYM Geography.
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
        $compound = new CompoundCurveImmutable([], 4326);
        $line = new LineStringImmutable([], 4326);
        $circular = new CircularStringImmutable([], 4326);

        self::assertNull($compound->getStartPoint());
        self::assertNull($compound->getEndPoint());
        self::assertSame([], $compound->getCurves());
        self::assertSame([], $compound->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $compound->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame(4326, $compound->getSrid());
        self::assertNull($line->getStartPoint());
        self::assertNull($line->getEndPoint());
        self::assertNull($circular->getStartPoint());
        self::assertNull($circular->getEndPoint());
    }

    /** Mixed interpolation, order and closure survive through public contracts. */
    public function testMixedClosedCurve(): void
    {
        $circular = new CircularStringImmutable([[0, 0, 6], [1, 1, 7], [2, 0, 8]], 4326);
        $line = new LineStringImmutable([[2, 0, 8], [0, 0, 6]], 4326);
        $compound = new CompoundCurveImmutable([$circular, $line], 4326);

        self::assertContains(CompoundCurveInterface::class, class_implements($compound));
        self::assertContains(CurveInterface::class, class_implements($compound));
        self::assertSame([$circular, $line], $compound->getCurves());
        self::assertSame([$circular, $line], $compound->getElements());
        self::assertSame($circular, $compound->getCurve(0));
        self::assertSame($line, $compound->getCurve(-1));
        self::assertSame($circular, $compound->getCurve(2));
        self::assertSame($circular->getStartPoint(), $compound->getStartPoint());
        self::assertSame($line->getEndPoint(), $compound->getEndPoint());
        self::assertSame([0, 0, 6], $compound->getStartPoint()?->toArray());
        self::assertSame([2, 0, 8], $circular->getEndPoint()?->toArray());
        self::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        self::assertSame(CoordinateDimensionEnum::XYM, $compound->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame([[[0, 0, 6], [1, 1, 7], [2, 0, 8]], [[2, 0, 8], [0, 0, 6]]], $compound->toArray());
    }

    /** Reference changes deeply copy components and preserve defining ordinates. */
    public function testReferenceReplacement(): void
    {
        $compound = new CompoundCurveImmutable([
            new CircularStringImmutable([[0, 0, 6], [1, 1, 7], [2, 0, 8]], SpatialReference::epsg(4326)),
            new LineStringImmutable([[2, 0, 8], [0, 0, 6]], SpatialReference::epsg(4326)),
        ], SpatialReference::epsg(4326));
        $copy = $compound->withSpatialReference(SpatialReference::epsg(3857));

        self::assertSame(CompoundCurveImmutable::class, new \ReflectionClass($copy)->getName());
        self::assertSame(4326, $compound->getSrid());
        self::assertSame(4326, $compound->getCurve(0)->getSrid());
        self::assertSame(3857, $copy->getSrid());
        self::assertSame('EPSG', $copy->getCurve(0)->getSpatialReference()->authority);
        self::assertSame(3857, $copy->getStartPoint()?->getSrid());
        self::assertSame(3857, $copy->getEndPoint()?->getSrid());
        self::assertNotSame($compound->getCurve(0), $copy->getCurve(0));
        self::assertNotSame($compound->getStartPoint(), $copy->getStartPoint());
        self::assertSame([[[0, 0, 6], [1, 1, 7], [2, 0, 8]], [[2, 0, 8], [0, 0, 6]]], $compound->toArray());
        self::assertSame([[[0, 0, 6], [1, 1, 7], [2, 0, 8]], [[2, 0, 8], [0, 0, 6]]], $copy->toArray());
        self::assertInstanceOf(CircularStringImmutable::class, $copy->getCurve(0));
        self::assertInstanceOf(LineStringImmutable::class, $copy->getCurve(1));
    }
}
