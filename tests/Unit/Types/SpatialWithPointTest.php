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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Internal\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractLineStringImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPolygonImmutable
 */
class SpatialWithPointTest extends TestCase
{
    /**
     * Verify that a line string rejects coordinates from another dimension.
     */
    public function testLineStringWithPointRejectsAnotherDimension(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([[1, 2]], 2154);

        self::expectException(InvalidDimensionException::class);

        $lineString->withPoint(0, Coordinates::xyz(3, 4, 5));
    }

    /**
     * Verify that a line string replaces one point in a deep immutable copy.
     */
    public function testLineStringWithPointReturnsAnIndependentCopy(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([[1, 2, 3], [4, 5, 6]], 2154, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $replacement = $lineString->withPoint(1, Coordinates::xym(7, 8, 9));

        self::assertNotSame($lineString, $replacement);
        self::assertSame($lineString::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame([[1, 2, 3], [4, 5, 6]], $lineString->toArray());
        self::assertSame([[1, 2, 3], [7, 8, 9]], $replacement->toArray());
        self::assertNotSame($lineString->getPoint(0), $replacement->getPoint(0));
        self::assertNotSame($lineString->getPoint(1), $replacement->getPoint(1));
    }

    /**
     * Verify that replacing the first exterior-ring point also replaces its closing point.
     */
    public function testPolygonWithPointPreservesExteriorRingClosure(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([
            [[0, 0], [10, 0], [0, 10], [0, 0]],
            [[1, 1], [2, 1], [1, 2], [1, 1]],
        ], 2154);
        $replacement = $polygon->withPoint(0, 0, Coordinates::xy(-1, -1));

        self::assertNotSame($polygon, $replacement);
        self::assertSame($polygon::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $polygon->toArray());
        self::assertSame([[[-1, -1], [10, 0], [0, 10], [-1, -1]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $replacement->toArray());
        self::assertNotSame($polygon->getRing(0), $replacement->getRing(0));
        self::assertNotSame($polygon->getRing(1), $replacement->getRing(1));
    }

    /**
     * Verify that replacing the final point of an inner ring also replaces its first point.
     */
    public function testPolygonWithPointPreservesInnerRingClosure(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([
            [[0, 0], [10, 0], [0, 10], [0, 0]],
            [[1, 1], [2, 1], [1, 2], [1, 1]],
        ], 2154);
        $replacement = $polygon->withPoint(-1, -1, Coordinates::xy(0.5, 0.5));

        self::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]], [[0.5, 0.5], [2, 1], [1, 2], [0.5, 0.5]]], $replacement->toArray());
        self::assertTrue($replacement->getRing(-1)->getPoint(0)->equalsTo($replacement->getRing(-1)->getPoint(-1)));
    }
}
