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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/**
 * Creates spatial types from indexed arrays.
 *
 * @internal use concrete spatial-type constructors in application code
 */
class FromIndexedArrayFactory
{
    /**
     * Create a line string from an indexed array.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface)[] $indexedArray indexed array
     * @param int|SpatialReference                                                                                         $srid         SRID
     * @param SpatialModelEnum                                                                                             $family       family
     * @param CoordinateDimensionEnum                                                                                      $dimension    dimension
     *
     * @throws SpatialTypeExceptionInterface when the point or line string cannot be created
     */
    public static function createLineString(array $indexedArray, int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum $dimension = CoordinateDimensionEnum::XY): LineStringInterface
    {
        return DefaultSpatialFactoryFactory::create()->createLineStringFromIndexedArray($indexedArray, new SpatialContext($srid, $family, $dimension));
    }

    /**
     * Create a point from an array of coordinates.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|array{} $coordinates   array of coordinates; an empty array creates an empty point
     * @param int|SpatialReference                                                                              $srid          SRID
     * @param SpatialModelEnum                                                                                  $family        family
     * @param CoordinateDimensionEnum                                                                           $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point
     */
    public static function createPoint(array $coordinates, int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum $dimensionEnum = CoordinateDimensionEnum::XY): PointInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPointFromIndexedArray($coordinates, new SpatialContext($srid, $family, $dimensionEnum));
    }

    /**
     * Create a polygon from an indexed array of closed line strings.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|LineStringInterface)[] $indexedArray indexed array of rings
     * @param int|SpatialReference                                                                                                $srid         SRID
     * @param SpatialModelEnum                                                                                                    $family       family
     * @param CoordinateDimensionEnum                                                                                             $dimension    dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of a line string or the polygon
     */
    public static function createPolygon(array $indexedArray, int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum $dimension = CoordinateDimensionEnum::XY): PolygonInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPolygonFromIndexedArray($indexedArray, new SpatialContext($srid, $family, $dimension));
    }
}
