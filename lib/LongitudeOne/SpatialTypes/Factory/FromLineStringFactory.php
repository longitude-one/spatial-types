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
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * This factory creates spatial types from indexed arrays of line strings.
 *
 * Each line string must implement LineStringInterface.
 */
class FromLineStringFactory
{
    /**
     * Create a polygon from an array of line strings.
     *
     * @param LineStringInterface[] $lineStrings   array of LineStrings
     * @param int                   $srid          SRID
     * @param FamilyEnum            $family        family
     * @param DimensionEnum         $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when the polygon cannot be created
     */
    public static function createPolygon(array $lineStrings, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): PolygonInterface
    {
        return FactoryPolygon::fromArrayOfLineStrings($lineStrings, $srid, $family, $dimensionEnum);
    }
}
