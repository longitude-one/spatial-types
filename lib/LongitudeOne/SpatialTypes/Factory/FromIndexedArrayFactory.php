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
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Geometry\Point as GeometricPoint;

/**
 * This factory creates spatial types from indexed arrays.
 */
class FromIndexedArrayFactory
{
    /**
     * Create a two-dimensional geographic point from an array of coordinates.
     *
     * @param array{0: float|int|string, 1: float|int|string} $coordinates array of coordinates
     * @param ?int                                            $srid        SRID
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point
     */
    public static function createGeographicPoint2D(array $coordinates, ?int $srid = null): PointInterface
    {
        return static::createPoint2D($coordinates, $srid, FamilyEnum::GEOGRAPHY);
    }

    /**
     * Create a two-dimensional geometric point from an array of coordinates.
     *
     * @param array{0: float|int|string, 1: float|int|string} $coordinates array of coordinates
     * @param ?int                                            $srid        SRID
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point
     */
    public static function createGeometricPoint2D(array $coordinates, ?int $srid = null): PointInterface
    {
        return static::createPoint2D($coordinates, $srid, FamilyEnum::GEOMETRY);
    }

    /**
     * Create a linestring from an indexed array.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}|PointInterface)[] $indexedArray indexed array
     * @param ?int                                                                                                                            $srid         SRID
     * @param FamilyEnum                                                                                                                      $family       family
     * @param DimensionEnum                                                                                                                   $dimension    dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point or the linestring
     */
    public static function createLineString(array $indexedArray, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): LineStringInterface
    {
        $points = [];

        foreach ($indexedArray as $element) {
            if (!is_array($element) && !$element instanceof PointInterface) {
                throw new InvalidValueException('The array must contain only objects implementing PointInterface or array of coordinates.');
            }

            if ($element instanceof PointInterface) {
                $points[] = $element;

                continue;
            }

            $points[] = static::createPoint($element, $srid, $family, $dimension);
        }

        return FromPointsFactory::createLineString($points, $srid, $family, $dimension);
    }

    /**
     * Create a point from an array of coordinates.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: float|int, 3 ?: \DateTimeInterface|float|int} $coordinates   array of coordinates
     * @param ?int                                                                                               $srid          SRID
     * @param FamilyEnum                                                                                         $family        family
     * @param DimensionEnum                                                                                      $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point
     */
    public static function createPoint(array $coordinates, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): PointInterface
    {
        if (DimensionEnum::X_Y !== $dimensionEnum) {
            throw new InvalidDimensionException('Only the two-dimensions points are yet supported.');
        }

        return static::createPoint2D($coordinates, $srid, $family);
    }

    /**
     * Create a two-dimensional point from an array of coordinates.
     *
     * @param array{0: float|int|string, 1: float|int|string} $coordinates array of coordinates
     * @param ?int                                            $srid        SRID
     * @param FamilyEnum                                      $family      family
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point
     */
    public static function createPoint2D(array $coordinates, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY): PointInterface
    {
        if (2 !== count($coordinates)) {
            throw new InvalidDimensionException('To create a two-dimensional point, your array shall contains exactly two elements.');
        }

        $x = array_first($coordinates);
        $y = array_last($coordinates);

        return match ($family) {
            FamilyEnum::GEOGRAPHY => new GeographicPoint($x, $y, $srid),
            FamilyEnum::GEOMETRY => new GeometricPoint($x, $y, $srid),
        };
    }
}
