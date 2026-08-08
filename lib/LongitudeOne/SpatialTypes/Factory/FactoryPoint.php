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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as GeographicPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as GeometricPoint2D;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point as GeographicPoint3Dz;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point as GeometricPoint3Dz;

/**
 * Factory Point class.
 *
 * @internal This class is internal. It is used to create a point from an array of coordinates.
 *
 * Developer can use it, but be aware that there is no backward compatibility pledge.
 */
class FactoryPoint
{
    use RequiredCoordinateTrait;

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
     * @throws InvalidValueException     when one of the coordinates is invalid
     * @throws InvalidDimensionException as long as the third and fourth dimensions are not supported
     */
    public static function fromCoordinates(float|int|string $x, float|int|string $y, float|int|null $z = null, float|int|null $m = null, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): PointInterface
    {
        if ((!$dimension->hasZ() && null !== $z) || (!$dimension->hasM() && null !== $m)) {
            throw new InvalidDimensionException('The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter DimensionEnum?');
        }

        return match ([$family, $dimension]) {
            [FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y] => new GeographicPoint2D($x, $y, $srid),
            [FamilyEnum::GEOMETRY, DimensionEnum::X_Y] => new GeometricPoint2D($x, $y, $srid),
            [FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y_Z] => new GeographicPoint3Dz($x, $y, self::requiredCoordinate($z, 'third'), $srid),
            [FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z] => new GeometricPoint3Dz($x, $y, self::requiredCoordinate($z, 'third'), $srid),
            default => throw new InvalidDimensionException(sprintf(
                'Only the two-dimensions line-strings and the three-dimensions elevation line-strings are yet supported. Point(%s %s %s %s) cannot be created.',
                $x,
                $y,
                $z,
                $m
            )),
        };
    }

    /**
     * Create a point from an array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $point     The point as an array
     * @param null|int                                                                                  $srid      The Spatial Reference Identifier
     * @param FamilyEnum                                                                                $family    The family of the point
     * @param DimensionEnum                                                                             $dimension The dimension of the point
     *
     * @throws MissingValueException     when one of the coordinates is missing
     * @throws InvalidValueException     when one of the coordinates is invalid
     * @throws InvalidDimensionException as long as the third and fourth dimensions are not supported
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
     * Ensure a coordinate is numeric.
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
