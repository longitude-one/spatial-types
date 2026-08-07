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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString as GeographyLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString as GeometryLineString;

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
        if (DimensionEnum::X_Y !== $dimensionEnum) {
            throw new InvalidDimensionException('Only the two-dimensions line-strings and the three-dimensions elevation line-strings are yet supported.');
        }

        foreach ($points as $point) {
            if (!$point instanceof PointInterface) {
                throw new InvalidValueException('The array must only contain objects implementing PointInterface.');
            }
        }

        $lineString = match ($family) {
            FamilyEnum::GEOGRAPHY => new GeographyLineString([], $srid),
            FamilyEnum::GEOMETRY => new GeometryLineString([], $srid),
        };

        foreach ($points as $point) {
            $lineString->addPoint($point);
        }

        return $lineString;
    }
}
