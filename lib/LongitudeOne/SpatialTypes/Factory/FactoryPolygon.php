<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
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
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Geography\Polygon as GeographyPolygon;
use LongitudeOne\SpatialTypes\Types\Geometry\Polygon as GeometryPolygon;

class FactoryPolygon
{
    /**
     * Create a polygon from an array of lineStrings.
     *
     * @param LineStringInterface[] $lineStrings   array of lineStrings
     * @param ?int                  $srid          SRID
     * @param FamilyEnum            $family        family
     * @param DimensionEnum         $dimensionEnum dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the polygon
     */
    public static function fromArrayOfLineStrings(array $lineStrings, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimensionEnum = DimensionEnum::X_Y): PolygonInterface
    {
        if (DimensionEnum::X_Y !== $dimensionEnum) {
            throw new InvalidDimensionException('Only the two-dimensions lineStrings are yet supported.');
        }

        foreach ($lineStrings as $lineString) {
            if (!$lineString instanceof LineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing LineStringInterface.');
            }
        }

        $polygon = match ($family) {
            FamilyEnum::GEOGRAPHY => new GeographyPolygon([], $srid),
            FamilyEnum::GEOMETRY => new GeometryPolygon([], $srid),
        };

        foreach ($lineStrings as $lineString) {
            $polygon->addRing($lineString);
        }

        return $polygon;
    }

    /**
     * Create a polygon from an indexed array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[][]|LineStringInterface[]|PointInterface[][] $indexedArray indexed array
     * @param ?int                                                                                                                                                      $srid         SRID
     * @param FamilyEnum                                                                                                                                                $family       family
     * @param DimensionEnum                                                                                                                                             $dimension    dimension
     *
     * @throws SpatialTypeExceptionInterface when something goes wrong during the creation of the lineString or the polygon
     */
    public static function fromIndexedArray(array $indexedArray, ?int $srid = null, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): PolygonInterface
    {
        $lineStrings = [];
        foreach ($indexedArray as $lineString) {
            if (!is_array($lineString) && !$lineString instanceof LineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing LineStringInterface or array of PointInterface or "array of array of coordinates".');
            }

            if ($lineString instanceof LineStringInterface) {
                $lineStrings[] = $lineString;

                continue;
            }

            $lineStrings[] = FactoryLineString::fromIndexedArray($lineString, $srid, $family, $dimension);
        }

        return self::fromArrayOfLineStrings($lineStrings, $srid, $family, $dimension);
    }
}
