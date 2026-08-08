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
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;

/**
 * This factory creates spatial types from indexed array of points.
 *
 * Points shall be instance of PointInterface.
 */
class FromPointFactory
{
    /**
     * Create a linestring from an array of points.
     *
     * @param PointInterface[] $points        array of points
     * @param ?int             $srid          SRID
     * @param FamilyEnum       $family        family
     * @param DimensionEnum    $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the linestring
     */
    public static function createLineString(array $points, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): LineStringInterface
    {
        return FactoryLineString::fromArrayOfPoints($points, $srid, $family, $dimensionEnum);
    }
}
