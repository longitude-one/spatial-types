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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString as LineString2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as Point2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString as LineString3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point as Point3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Polygon as Polygon3Dm;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString as LineString3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point as Point3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Polygon as Polygon3Dz;

/**
 * Creates geographic spatial types.
 *
 * @internal
 */
final class GeographicSpatialFamilyFactory implements SpatialFamilyFactoryInterface
{
    use RequiredCoordinateTrait;

    /**
     * Creates a geographic line string instance.
     *
     * @param PointInterface[] $points    the points that compose the line string
     * @param null|int         $srid      the spatial reference identifier
     * @param DimensionEnum    $dimension the dimension of the line string
     *
     * @return LineStringInterface the created geographic line string
     *
     * @throws InvalidDimensionException when the dimension is not supported
     */
    public function createLineString(array $points, ?int $srid, DimensionEnum $dimension): LineStringInterface
    {
        return match ($dimension) {
            DimensionEnum::X_Y => new LineString2D($points, $srid),
            DimensionEnum::X_Y_M => new LineString3Dm($points, $srid),
            DimensionEnum::X_Y_Z => new LineString3Dz($points, $srid),
            default => throw new InvalidDimensionException('Only two-dimension line-strings, three-dimension measure line-strings, and three-dimension elevation line-strings are yet supported'),
        };
    }

    /**
     * Creates a geographic point instance of specified dimension.
     *
     * @param float|int|string $x         the X coordinate of the point, the longitude
     * @param float|int|string $y         the Y coordinate of the point, the latitude
     * @param float|int        $z         the Z coordinate of the point, the elevation
     * @param float|int        $m         the M coordinate of the point
     * @param null|int         $srid      the spatial reference identifier
     * @param DimensionEnum    $dimension the dimension of the point
     *
     * @return PointInterface the created geographic point
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
            default => throw new InvalidDimensionException('Only two-dimension points, three-dimension measure points, and three-dimension elevation points are yet supported'),
        };
    }

    /**
     * Creates a geographic polygon instance.
     *
     * @param LineStringInterface[] $rings     the rings that compose the polygon
     * @param null|int              $srid      the spatial reference identifier
     * @param DimensionEnum         $dimension the dimension of the polygon
     *
     * @return PolygonInterface the created geographic polygon
     *
     * @throws InvalidDimensionException when the dimension is not supported
     */
    public function createPolygon(array $rings, ?int $srid, DimensionEnum $dimension): PolygonInterface
    {
        return match ($dimension) {
            DimensionEnum::X_Y => new Polygon($rings, $srid),
            DimensionEnum::X_Y_M => new Polygon3Dm($rings, $srid),
            DimensionEnum::X_Y_Z => new Polygon3Dz($rings, $srid),
            default => throw new InvalidDimensionException('Only two-dimension polygons, three-dimension measure polygons, and three-dimension elevation polygons are yet supported'),
        };
    }
}
