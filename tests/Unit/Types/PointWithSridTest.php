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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as GeographyPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as GeometryPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point as GeographyPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as GeometryPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point as GeographyPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point as GeometryPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Point as GeographyPoint4Dzm;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Point as GeometryPoint4Dzm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 */
class PointWithSridTest extends TestCase
{
    /**
     * Verify that withSrid returns a distinct point without changing its ordinates.
     *
     * @param PointInterface $point Point to copy
     */
    #[DataProvider('providePoints')]
    public function testWithSrid(PointInterface $point): void
    {
        $pointWithSrid = $point->withSrid(2154);

        static::assertNotSame($point, $pointWithSrid);
        static::assertSame($point::class, $pointWithSrid::class);
        static::assertSame($point->toArray(), $pointWithSrid->toArray());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(2154, $pointWithSrid->getSrid());
    }

    /**
     * @return \Generator<string, array{0: PointInterface}, null, void>
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
     * Verify that withSrid returns a new instance and do not only update the point.
     */
    public function testWithSridReturnsDistinctInstance(): void
    {
        $initialPoint = new GeometryPoint2D(1, 2, 4326);
        $clonedPoint = $initialPoint->withSrid(4326);

        static::assertEquals($clonedPoint, $initialPoint);
        static::assertNotSame($clonedPoint, $initialPoint);
    }
}
