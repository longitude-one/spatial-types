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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension2\Geography;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PolygonImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the two-dimensional geographic polygon.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Boundary\PolygonBoundary
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPolygonImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PolygonImmutable
 */
class PolygonTest extends TestCase
{
    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('g');
        new PolygonImmutable([[['240W', '340S'], ['45W', '45N'], ['45W', '90N'], ['240W', '340S']]]);
    }

    /**
     * Test the getDimension method.
     */
    public function testGetDimension(): void
    {
        $polygon = new PolygonImmutable([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        self::assertFalse($polygon->hasZ());
        self::assertFalse($polygon->hasM());
        $ring = $polygon->getRing(0);
        self::assertFalse($ring->hasZ());
        self::assertFalse($ring->hasM());
        foreach ($ring->getPoints() as $point) {
            self::assertFalse($point->hasZ());
            self::assertFalse($point->hasM());
        }
    }

    /**
     * Test the getFamily method.
     */
    public function testGetFamily(): void
    {
        $expected = SpatialModelEnum::GEOGRAPHY;
        $polygon = new PolygonImmutable([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        self::assertSame($expected, $polygon->getFamily());
        $ring = $polygon->getRing(0);
        self::assertSame($expected, $ring->getFamily());
        foreach ($ring->getPoints() as $point) {
            self::assertSame($expected, $point->getFamily());
        }
    }

    /**
     * Test the getTypes method.
     */
    public function testGetType(): void
    {
        $polygon = new PolygonImmutable([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        self::assertSame(GeometryTypeEnum::POLYGON, $polygon->getType());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $polygon = new PolygonImmutable([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        self::assertSame('{"type":"Polygon","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":0}', json_encode($polygon));
        $polygon = new PolygonImmutable([[[1, 2], [3, 4], [3, 6], [1, 2]]], 4326);
        self::assertSame('{"type":"Polygon","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":4326}', json_encode($polygon));
    }
}
