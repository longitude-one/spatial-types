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
use LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver;

/**
 * Factory Point class.
 *
 * @internal This class is internal. It is used to create a point from an array of coordinates.
 *
 * Developer can use it, but be aware that there is no backward compatibility pledge.
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
     * @param null|int         $srid      The Spatial Reference Identifier
     * @param FamilyEnum       $family    The family of the point
     * @param DimensionEnum    $dimension The dimension of the point
     *
     * @return PointInterface The point matching the requested family and dimension
     *
     * @throws InvalidDimensionException when a supplied coordinate is absent from the requested dimension
     * @throws InvalidValueException     when one of the coordinates is invalid
     * @throws MissingValueException     when the requested Z coordinate is missing
     */
    public static function fromCoordinates(float|int|string $x, float|int|string $y, float|int|null $z = null, float|int|null $m = null, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): PointInterface
    {
        if ((!$dimension->hasZ() && null !== $z) || (!$dimension->hasM() && null !== $m)) {
            throw new InvalidDimensionException('The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter DimensionEnum?');
        }

        return SpatialFamilyFactoryResolver::resolve($family)->createPoint($x, $y, $z, $m, $srid, $dimension);
    }

    /**
     * Create a point from an array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $point     The point as an array
     * @param null|int                                                                                  $srid      The Spatial Reference Identifier
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
        ?int $srid = null,
        FamilyEnum $family = FamilyEnum::GEOMETRY,
        DimensionEnum $dimension = DimensionEnum::X_Y
    ): PointInterface {
        $coordinateCount = $dimension->coordinateCount();
        if (count($point) > $coordinateCount) {
            throw new InvalidDimensionException(sprintf('The array must contain exactly %d coordinates to create a %s point.', $coordinateCount, $dimension->value));
        }

        foreach (range(0, $coordinateCount - 1) as $index) {
            if (!array_key_exists($index, $point) || null === $point[$index]) {
                throw new MissingValueException(sprintf('The %s coordinate of array is missing.', match ($index) {
                    0 => 'first',
                    1 => 'second',
                    2 => 'third',
                    3 => 'fourth',
                    default => 'unknown',
                }));
            }
        }

        $x = $point[0];
        $y = $point[1];
        $zIndex = $dimension->zIndex();
        $mIndex = $dimension->mIndex();
        $z = null === $zIndex ? null : self::numericCoordinate($point[$zIndex], 'Z');
        $m = null === $mIndex ? null : self::numericCoordinate($point[$mIndex], 'M');

        return self::fromCoordinates($x, $y, $z, $m, $srid, $family, $dimension);
    }

    /**
     * Ensure a Z or M coordinate is numeric.
     *
     * @param mixed  $coordinate The coordinate to validate
     * @param string $name       The coordinate name used in the error message
     *
     * @return float|int The validated coordinate
     *
     * @throws InvalidValueException when the coordinate is not numeric
     */
    private static function numericCoordinate(mixed $coordinate, string $name): float|int
    {
        if (!is_float($coordinate) && !is_int($coordinate)) {
            throw new InvalidValueException(sprintf('The %s coordinate must be a number.', $name));
        }

        return $coordinate;
    }
}
