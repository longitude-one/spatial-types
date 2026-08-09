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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as GeographyPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as GeometryPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point as GeographyPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as GeometryPoint3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point as GeographyPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point as GeometryPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Point as GeographyPoint4Dzm;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Point as GeometryPoint4Dzm;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 */
class PointWithCoordinatesTest extends TestCase
{
    /**
     * Verify that withCoordinates returns a distinct point with the same SRID and type.
     *
     * @param Coordinates    $coordinates replacement coordinates
     * @param PointInterface $point       point to copy
     */
    #[DataProvider('providePointsAndCoordinates')]
    public function testWithCoordinates(Coordinates $coordinates, PointInterface $point): void
    {
        $originalCoordinates = $point->getCoordinates();
        $pointWithCoordinates = $point->withCoordinates($coordinates);

        static::assertNotSame($point, $pointWithCoordinates);
        static::assertSame($point::class, $pointWithCoordinates::class);
        static::assertSame(4326, $point->getSrid());
        static::assertSame(4326, $pointWithCoordinates->getSrid());
        static::assertEquals($originalCoordinates, $point->getCoordinates());
        static::assertEquals($coordinates, $pointWithCoordinates->getCoordinates());
    }

    /**
     * @return \Generator<string, array{0: Coordinates, 1: PointInterface}, null, void>
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
     * Verify that withCoordinates keeps geography range validation.
     */
    public function testWithCoordinatesValidatesGeographicLatitude(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Out of range latitude value');

        (new GeographyPoint2D(1, 2, 4326))->withCoordinates(Coordinates::xy(3, 92));
    }

    /**
     * Verify that withCoordinates keeps geography range validation.
     */
    public function testWithCoordinatesValidatesGeographicLongitude(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Out of range longitude value');

        (new GeographyPoint2D(1, 2, 4326))->withCoordinates(Coordinates::xy(181, 2));
    }
}
