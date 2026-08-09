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
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver;

/**
 * Polygon factory.
 *
 * Creates polygons from line strings, points, or nested coordinate arrays.
 *
 * @internal This class is internal. You can use it, but be aware that there is no backward compatibility pledge.
 */
class FactoryPolygon
{
    /**
     * Create a polygon from an array of line strings.
     *
     * @param LineStringInterface[] $lineStrings   Line strings that form the polygon rings
     * @param int                   $srid          SRID
     * @param FamilyEnum            $family        family
     * @param DimensionEnum         $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the polygon
     */
    public static function fromArrayOfLineStrings(array $lineStrings, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): PolygonInterface
    {
        foreach ($lineStrings as $lineString) {
            if (!$lineString instanceof LineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing LineStringInterface.');
            }
        }

        return SpatialFamilyFactoryResolver::resolvePolygonFactory($family)->create(
            $lineStrings,
            new SpatialContext($srid, $family, $dimensionEnum)
        );
    }

    /**
     * Create a polygon from an indexed array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[][]|LineStringInterface[]|PointInterface[][] $indexedArray indexed array
     * @param int                                                                                                                                    $srid         SRID
     * @param FamilyEnum                                                                                                                             $family       family
     * @param DimensionEnum                                                                                                                          $dimension    dimension
     *
     * @throws SpatialTypeExceptionInterface when the line string or polygon cannot be created
     */
    public static function fromIndexedArray(array $indexedArray, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): PolygonInterface
    {
        $context = new SpatialContext($srid, $family, $dimension);

        return self::fromArrayOfLineStrings((new SpatialArrayHydrator())->hydratePolygon($indexedArray, $context), $srid, $family, $dimension);
    }
}
