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
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver;

/**
 * This factory creates spatial types from indexed array of LineStrings.
 *
 * LineStrings shall be instance of LineStringInterface.
 */
class FromLineStringFactory
{
    /**
     * Create a PolygonInterface from an array of LineStringInterface.
     *
     * @param LineStringInterface[] $lineStrings   array of LineStrings
     * @param ?int                  $srid          SRID
     * @param FamilyEnum            $family        family
     * @param DimensionEnum         $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the linestring
     */
    public static function createPolygon(array $lineStrings, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): PolygonInterface
    {
        if (DimensionEnum::X_Y !== $dimensionEnum) {
            throw new InvalidDimensionException('Only the two-dimensions LineStrings are yet supported.');
        }

        foreach ($lineStrings as $lineString) {
            if (!$lineString instanceof LineStringInterface) {
                throw new InvalidValueException('The array must only contain objects implementing LineStringInterface.');
            }
        }

        return SpatialFamilyFactoryResolver::resolve($family)->createPolygon($lineStrings, $srid);
    }
}
