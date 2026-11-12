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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3m;

use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable as GeometricPoint;
use PHPUnit\Framework\TestCase;

/**
 * Contract tests for XYM spatial objects.
 *
 * @internal
 *
 * @coversNothing
 */
class XymContractTest extends TestCase
{
    /**
     * Verify that XYM points preserve their coordinate order and serialize it unchanged.
     */
    public function testCoordinatesUseXymOrder(): void
    {
        $point = new GeometricPoint(1, 2, 3, 2154);

        self::assertSame(1, $point->getX());
        self::assertSame(2, $point->getY());
        self::assertSame(3, $point->getM());
        self::assertSame([1, 2, 3], $point->toArray());
        self::assertSame('{"type":"Point","coordinates":[1,2,3],"srid":2154}', json_encode($point));
    }

    /**
     * Verify that geographic XYM points preserve their coordinate order.
     */
    public function testGeographicCoordinatesUseXymOrder(): void
    {
        $point = new GeographicPoint('1W', '2N', 3, 4326);

        self::assertSame(-1, $point->getX());
        self::assertSame(2, $point->getY());
        self::assertSame(3, $point->getM());
        self::assertSame([-1, 2, 3], $point->toArray());
        self::assertSame('{"type":"Point","coordinates":[-1,2,3],"srid":4326}', json_encode($point));
    }
}
