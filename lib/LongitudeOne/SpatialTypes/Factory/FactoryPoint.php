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

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * Factory Point class.
 *
 * @internal
 *
 * @deprecated use SpatialFactory injected with a SpatialFactoryRegistry instead
 */
class FactoryPoint
{
    /**
     * Create a point from coordinates.
     *
     * @param float|int|string $x         The x or longitude of the point
     * @param float|int|string $y         The y or latitude of the point
     * @param null|float|int   $z         The elevation of the point
     * @param null|float|int   $m         The measure of the point
     * @param int              $srid      The Spatial Reference Identifier
     * @param FamilyEnum       $family    The family of the point
     * @param DimensionEnum    $dimension The dimension of the point
     *
     * @return PointInterface The point matching the requested family and dimension
     *
     * @throws InvalidDimensionException when a supplied coordinate is absent from the requested dimension
     * @throws InvalidValueException     when one of the coordinates is invalid
     * @throws MissingValueException     when the requested Z coordinate is missing
     */
    public static function fromCoordinates(float|int|string $x, float|int|string $y, float|int|null $z = null, float|int|null $m = null, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): PointInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPoint(new Coordinates($x, $y, $z, $m), new SpatialContext($srid, $family, $dimension));
    }

    /**
     * Create a point from an array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $point     The point as an array
     * @param int                                                                                       $srid      The Spatial Reference Identifier
     * @param FamilyEnum                                                                                $family    The family of the point
     * @param DimensionEnum                                                                             $dimension The dimension of the point
     *
     * @return PointInterface The point matching the requested family and dimension
     *
     * @throws InvalidDimensionException when the array contains too many coordinates or the dimension is unsupported
     * @throws InvalidValueException     when a Z or M coordinate is not numeric
     * @throws MissingValueException     when a coordinate required by the dimension is missing
     */
    public static function fromIndexedArray(
        array $point,
        int $srid = SpatialInterface::DEFAULT_SRID,
        FamilyEnum $family = FamilyEnum::GEOMETRY,
        DimensionEnum $dimension = DimensionEnum::X_Y
    ): PointInterface {
        return DefaultSpatialFactoryFactory::create()->createPointFromIndexedArray($point, new SpatialContext($srid, $family, $dimension));
    }
}
