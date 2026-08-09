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
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Factory\Hydrator\SpatialArrayHydrator;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver;

/**
 * Factory LineString class.
 *
 * @internal This class is internal. It is used to create a linestring from an array of points or an indexed array.
 *
 * Developer can use it, but be aware that there is no backward compatibility pledge.
 */
class FactoryLineString
{
    /**
     * Create a linestring from an array of points.
     *
     * @param PointInterface[] $points        array of points
     * @param int              $srid          SRID
     * @param FamilyEnum       $family        family
     * @param DimensionEnum    $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the linestring
     */
    public static function fromArrayOfPoints(array $points, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): LineStringInterface
    {
        foreach ($points as $point) {
            if (!$point instanceof PointInterface) {
                throw new InvalidValueException('The array must contain only objects implementing PointInterface.');
            }
        }

        return SpatialFamilyFactoryResolver::resolveLineStringFactory($family)->create(
            $points,
            new SpatialContext($srid, $family, $dimensionEnum)
        );
    }

    /**
     * Create a linestring from an indexed array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|PointInterface[] $indexedArray indexed array
     * @param int                                                                                                          $srid         SRID
     * @param FamilyEnum                                                                                                   $family       family
     * @param DimensionEnum                                                                                                $dimension    dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the point or the linestring
     */
    public static function fromIndexedArray(array $indexedArray, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): LineStringInterface
    {
        $context = new SpatialContext($srid, $family, $dimension);

        return self::fromArrayOfPoints((new SpatialArrayHydrator())->hydrateLineString($indexedArray, $context), $srid, $family, $dimension);
    }
}
