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

namespace LongitudeOne\SpatialTypes\Factory\Internal;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Factory\RequiredCoordinateTrait;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString as LineString2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as Point2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString as LineString3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as Point3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Polygon as Polygon3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString as LineString3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point as Point3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon as Polygon3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString as LineString4Dzm;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Point as Point4Dzm;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Polygon as Polygon4Dzm;

/**
 * Creates geometric spatial types.
 *
 * @internal
 */
final class GeometricSpatialFamilyFactory implements SpatialFamilyFactoryInterface
{
    use RequiredCoordinateTrait;

    /**
     * Creates a geometric line string instance.
     *
     * @param PointInterface[] $points    the points that compose the line string
     * @param null|int         $srid      the spatial reference identifier
     * @param DimensionEnum    $dimension the dimension of the line string
     *
     * @return LineStringInterface the created geometric line string
     *
     * @throws InvalidDimensionException when the dimension is not supported
     */
    public function createLineString(array $points, ?int $srid, DimensionEnum $dimension): LineStringInterface
    {
        return match ($dimension) {
            DimensionEnum::X_Y => new LineString2D($points, $srid),
            DimensionEnum::X_Y_M => new LineString3Dm($points, $srid),
            DimensionEnum::X_Y_Z => new LineString3Dz($points, $srid),
            DimensionEnum::X_Y_Z_M => new LineString4Dzm($points, $srid),
        };
    }

    /**
     * Creates a geometric point instance of the specified dimension.
     *
     * @param float|int|string $x         the X coordinate of the point, the longitude
     * @param float|int|string $y         the Y coordinate of the point, the latitude
     * @param null|float|int   $z         the Z coordinate of the point, the elevation
     * @param null|float|int   $m         the M coordinate of the point
     * @param null|int         $srid      the spatial reference identifier
     * @param DimensionEnum    $dimension the dimension of the point to create
     *
     * @return PointInterface the created geometric point
     *
     * @throws InvalidDimensionException when the dimension is not supported
     * @throws MissingValueException     when a required Z or M coordinate is missing
     */
    public function createPoint(float|int|string $x, float|int|string $y, float|int|null $z, float|int|null $m, ?int $srid, DimensionEnum $dimension): PointInterface
    {
        return match ($dimension) {
            DimensionEnum::X_Y => new Point2D($x, $y, $srid),
            DimensionEnum::X_Y_M => new Point3Dm($x, $y, self::requiredCoordinate($m, 'third'), $srid),
            DimensionEnum::X_Y_Z => new Point3Dz($x, $y, self::requiredCoordinate($z, 'third'), $srid),
            DimensionEnum::X_Y_Z_M => new Point4Dzm($x, $y, self::requiredCoordinate($z, 'third'), self::requiredCoordinate($m, 'fourth'), $srid),
        };
    }

    /**
     * Creates a geometric polygon instance.
     *
     * @param LineStringInterface[] $rings     the rings that compose the polygon
     * @param null|int              $srid      the spatial reference identifier
     * @param DimensionEnum         $dimension the dimension of the polygon
     *
     * @return PolygonInterface the created geometric polygon
     *
     * @throws InvalidDimensionException when the dimension is not supported
     */
    public function createPolygon(array $rings, ?int $srid, DimensionEnum $dimension): PolygonInterface
    {
        return match ($dimension) {
            DimensionEnum::X_Y => new Polygon($rings, $srid),
            DimensionEnum::X_Y_M => new Polygon3Dm($rings, $srid),
            DimensionEnum::X_Y_Z => new Polygon3Dz($rings, $srid),
            DimensionEnum::X_Y_Z_M => new Polygon4Dzm($rings, $srid),
        };
    }
}
