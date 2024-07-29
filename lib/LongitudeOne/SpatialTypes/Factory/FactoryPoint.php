<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Helper\DimensionHelper;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Geometry\Point as GeometricPoint;

class FactoryPoint
{
    /**
     * Create a point from an array.
     *
     * @throws MissingValueException when one of the coordinates is missing
     * @throws InvalidValueException when one of the coordinates is invalid
     * @throws \LogicException       as long as the third and fourth dimensions are not supported
     */
    public static function fromArray(
        array $point,
        ?int $srid = null,
        FamilyEnum $family = FamilyEnum::GEOMETRY,
        DimensionEnum $dimension = DimensionEnum::X_Y
    ): PointInterface {
        $dimensionHelper = new DimensionHelper($dimension);

        if (!isset($point['x']) && !isset($point[0])) {
            throw new MissingValueException('The first coordinate of array is missing.');
        }

        if (!isset($point['y']) && !isset($point[1])) {
            throw new MissingValueException('The second coordinate of array is missing.');
        }

        if ($dimensionHelper->hasZ() && !isset($point['z']) && !isset($point[2])) {
            throw new MissingValueException('The third coordinate of array is missing.');
        }

        if ($dimensionHelper->hasM() && !isset($point['m']) && !isset($point[3])) {
            throw new MissingValueException('The fourth coordinate of array is missing.');
        }

        $x = $point['x'] ?? $point[0] ?? null;
        $y = $point['y'] ?? $point[1] ?? null;
        $z = $point['z'] ?? $point[2] ?? null;
        $m = $point['m'] ?? $point[3] ?? null;

        return self::fromCoordinates($x, $y, $z, $m, $srid, $family, $dimension);
    }

    /**
     * Create a point from coordinates.
     *
     * @param float|int|string                  $x         The x or longitude of the point
     * @param float|int|string                  $y         The y or latitude of the point
     * @param null|float|int                    $z         The elevation of the point
     * @param null|\DateTimeInterface|float|int $m         The measure of the point
     * @param null|int                          $srid      The Spatial Reference Identifier
     * @param FamilyEnum                        $family    The family of the point
     * @param DimensionEnum                     $dimension The dimension of the point
     *
     * @throws InvalidValueException when one of the coordinates is invalid
     * @throws \LogicException       as long as the third and fourth dimensions are not supported
     */
    public static function fromCoordinates(float|int|string $x, float|int|string $y, null|float|int $z = null, null|\DateTimeInterface|float|int $m = null, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): PointInterface
    {
        if (DimensionEnum::X_Y === $dimension) {
            if (FamilyEnum::GEOGRAPHY === $family) {
                return new GeographicPoint($x, $y, $srid);
            }

            return new GeometricPoint($x, $y, $srid);
        }

        throw new \LogicException('Only the two-dimensions points are yet supported.');
    }
}
