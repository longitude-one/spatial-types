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

use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable as GeographyPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable as GeometryPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable as GeographyPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable as GeometryPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable as GeographyPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable as GeometryPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable as GeographyPoint4Dzm;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable as GeometryPoint4Dzm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable
 */
class PointWithSridTest extends TestCase
{
    /**
     * @return \Generator<string, array{0: ImmutablePointInterface}, null, void>
     */
    public static function providePoints(): \Generator
    {
        yield '2D geometry' => [new GeometryPoint2D(1, 2, 4326)];

        yield '2D geography' => [new GeographyPoint2D(1, 2, 4326)];

        yield '3DM geometry' => [new GeometryPoint3Dm(1, 2, 3, 4326)];

        yield '3DM geography' => [new GeographyPoint3Dm(1, 2, 3, 4326)];

        yield '3DZ geometry' => [new GeometryPoint3Dz(1, 2, 3, 4326)];

        yield '3DZ geography' => [new GeographyPoint3Dz(1, 2, 3, 4326)];

        yield '4D geometry' => [new GeometryPoint4Dzm(1, 2, 3, 4, 4326)];

        yield '4D geography' => [new GeographyPoint4Dzm(1, 2, 3, 4, 4326)];
    }

    /**
     * Verify that withSrid returns a distinct point without changing its ordinates.
     *
     * @param ImmutablePointInterface $point Point to copy
     */
    #[DataProvider('providePoints')]
    public function testWithSrid(ImmutablePointInterface $point): void
    {
        $pointWithSrid = $point->withSrid(2154);

        self::assertNotSame($point, $pointWithSrid);
        self::assertSame($point::class, new \ReflectionClass($pointWithSrid)->getName());
        self::assertSame($point->toArray(), $pointWithSrid->toArray());
        self::assertSame(4326, $point->getSrid());
        self::assertSame(2154, $pointWithSrid->getSrid());
    }

    /**
     * Verify that withSrid returns a new instance and do not only update the point.
     */
    public function testWithSridReturnsDistinctInstance(): void
    {
        $initialPoint = new GeometryPoint2D(1, 2, 4326);
        $clonedPoint = $initialPoint->withSrid(4326);

        self::assertEquals($clonedPoint, $initialPoint);
        self::assertNotSame($clonedPoint, $initialPoint);
    }
}
