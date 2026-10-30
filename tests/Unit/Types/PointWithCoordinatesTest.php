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

use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable as GeographyPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable as GeometryPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable as GeographyPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable as GeometryPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable as GeographyPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable as GeometryPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable as GeographyPoint4Dzm;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable as GeometryPoint4Dzm;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable
 */
class PointWithCoordinatesTest extends TestCase
{
    /**
     * Point equality includes every ordinate that its dimension exposes.
     */
    public function testEqualsToRejectsDifferentMeasureOrElevation(): void
    {
        static::assertFalse(
            (new GeometryPoint3Dm(1, 2, 3))->equalsTo(new GeometryPoint3Dm(1, 2, 4))
        );
        static::assertFalse(
            (new GeometryPoint3Dz(1, 2, 3))->equalsTo(new GeometryPoint3Dz(1, 2, 4))
        );
        static::assertFalse(
            (new GeometryPoint4Dzm(1, 2, 3, 4))->equalsTo(new GeometryPoint4Dzm(1, 2, 3, 5))
        );
    }

    /**
     * Verify that withCoordinates returns a distinct point with the same SRID and type.
     *
     * @param Coordinates             $coordinates replacement coordinates
     * @param ImmutablePointInterface $point       point to copy
     */
    #[DataProvider('providePointsAndCoordinates')]
    public function testWithCoordinates(Coordinates $coordinates, ImmutablePointInterface $point): void
    {
        $originalCoordinates = $point->getCoordinates();
        $pointWithCoordinates = $point->withCoordinates($coordinates);

        static::assertNotSame($point, $pointWithCoordinates);
        static::assertSame($point::class, (new \ReflectionClass($pointWithCoordinates))->getName());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(4326, $pointWithCoordinates->getSrid());
        static::assertEquals($originalCoordinates, $point->getCoordinates());
        static::assertEquals($coordinates, $pointWithCoordinates->getCoordinates());
    }

    /**
     * @return \Generator<string, array{0: Coordinates, 1: ImmutablePointInterface}, null, void>
     */
    public static function providePointsAndCoordinates(): \Generator
    {
        yield '2D geometry' => [Coordinates::xy(3, 4), new GeometryPoint2D(1, 2, 4326)];

        yield '2D geography' => [Coordinates::xy(3, 4), new GeographyPoint2D(1, 2, 4326)];

        yield '3DM geometry' => [Coordinates::xym(3, 4, 5), new GeometryPoint3Dm(1, 2, 3, 4326)];

        yield '3DM geography' => [Coordinates::xym(3, 4, 5), new GeographyPoint3Dm(1, 2, 3, 4326)];

        yield '3DZ geometry' => [Coordinates::xyz(3, 4, 5), new GeometryPoint3Dz(1, 2, 3, 4326)];

        yield '3DZ geography' => [Coordinates::xyz(3, 4, 5), new GeographyPoint3Dz(1, 2, 3, 4326)];

        yield '4D geometry' => [Coordinates::xyzm(3, 4, 5, 6), new GeometryPoint4Dzm(1, 2, 3, 4, 4326)];

        yield '4D geography' => [Coordinates::xyzm(3, 4, 5, 6), new GeographyPoint4Dzm(1, 2, 3, 4, 4326)];
    }

    /**
     * Verify that withCoordinates rejects a value from another dimension.
     */
    public function testWithCoordinatesRejectsDifferentDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('incompatible with the XY point dimension');

        (new GeometryPoint2D(1, 2, 4326))->withCoordinates(Coordinates::xyz(1, 2, 3));
    }

    /**
     * An XYM geographic point cannot expose an elevation ordinate.
     */
    public function testXymGeographicPointDoesNotExposeZ(): void
    {
        self::expectException(BadMethodCallException::class);

        (new GeographyPoint3Dm(1, 2, 3))->getZ();
    }
}
