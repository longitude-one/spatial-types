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
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver;

/**
 * This factory creates spatial types from indexed arrays.
 */
class FromIndexedArrayFactory
{
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
        if (DimensionEnum::X_Y !== $dimension && DimensionEnum::X_Y_Z !== $dimension) {
            throw new InvalidDimensionException('Only the two-dimensions line-strings and the three-dimensions elevation line-strings are yet supported.');
        }

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

        return SpatialFamilyFactoryResolver::resolve($family)->createLineString($points, $srid, $dimension);
    }

    /**
     * Create a point from an array of coordinates.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int} $coordinates   array of coordinates
     * @param ?int                                                                                                         $srid          SRID
     * @param FamilyEnum                                                                                                   $family        family
     * @param DimensionEnum                                                                                                $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point
     */
    public static function createPoint(array $coordinates, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): PointInterface
    {
        if (DimensionEnum::X_Y === $dimensionEnum) {
            return static::createPoint2D($coordinates, $srid, $family);
        }

        if (DimensionEnum::X_Y_Z === $dimensionEnum) {
            return static::createPoint3Dz($coordinates, $srid, $family);
        }

        throw new InvalidDimensionException('Only the two-dimensions points and elevation point (3dZ) are yet supported.');
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

        if (!isset($coordinates[0])) {
            throw new MissingValueException('The first coordinate must be stored at array index 0. Index 0 is missing.');
        }

        if (!isset($coordinates[1])) {
            throw new MissingValueException('The second coordinate must be stored at array index 1. Index 1 is missing.');
        }

        $x = $coordinates[0];
        $y = $coordinates[1];

        return SpatialFamilyFactoryResolver::resolve($family)->createPoint($x, $y, null, null, $srid, DimensionEnum::X_Y);
    }

    /**
     * Create a point from an array of coordinates.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int} $coordinates array of coordinates
     * @param ?int                                                                 $srid        SRID
     * @param FamilyEnum                                                           $family      family
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point
     */
    public static function createPoint3Dz(array $coordinates, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY): PointInterface
    {
        if (3 !== count($coordinates)) {
            throw new InvalidDimensionException('To create a three-dimensional elevation point, your array shall contains exactly three elements.');
        }

        // @phpstan-ignore-next-line
        if (!isset($coordinates[0])) {
            throw new MissingValueException('The first coordinate must be stored at array index 0. Index 0 is missing.');
        }

        // @phpstan-ignore-next-line
        if (!isset($coordinates[1])) {
            throw new MissingValueException('The second coordinate must be stored at array index 1. Index 1 is missing.');
        }

        if (!isset($coordinates[2])) {
            throw new MissingValueException('The third coordinate must be stored at array index 2. Index 2 is missing.');
        }

        $x = $coordinates[0];
        $y = $coordinates[1];
        $z = $coordinates[2];

        return SpatialFamilyFactoryResolver::resolve($family)->createPoint($x, $y, $z, null, $srid, DimensionEnum::X_Y_Z);
    }

    /**
     * Create a polygon from an indexed array of closed line strings.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[]|LineStringInterface)[] $indexedArray indexed array of rings
     * @param ?int                                                                                                                                   $srid         SRID
     * @param FamilyEnum                                                                                                                             $family       family
     * @param DimensionEnum                                                                                                                          $dimension    dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of a line string or the polygon
     */
    public static function createPolygon(array $indexedArray, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): PolygonInterface
    {
        if (DimensionEnum::X_Y !== $dimension) {
            throw new InvalidDimensionException('Only the two-dimensions polygons are yet supported.');
        }

        $lineStrings = [];
        foreach ($indexedArray as $element) {
            if (!is_array($element) && !$element instanceof LineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing LineStringInterface or array of coordinates.');
            }

            if ($element instanceof LineStringInterface) {
                $lineStrings[] = $element;

                continue;
            }

            $lineStrings[] = static::createLineString($element, $srid, $family, $dimension);
        }

        return SpatialFamilyFactoryResolver::resolve($family)->createPolygon($lineStrings, $srid);
    }
}
