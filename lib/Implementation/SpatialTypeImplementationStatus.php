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

namespace LongitudeOne\SpatialTypes\Implementation;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;

/**
 * Implementation coverage of the geometry types in the spatial model.
 */
final class SpatialTypeImplementationStatus
{
    /**
     * Report whether a type is fully implemented for its applicable layouts.
     *
     * @param GeometryTypeEnum $type Requested geometry type
     *
     * @throws InvalidValueException when the geometry type is non-instantiable
     */
    public static function isFullyImplemented(GeometryTypeEnum $type): bool
    {
        return match ($type) {
            GeometryTypeEnum::POINT,
            GeometryTypeEnum::LINESTRING,
            GeometryTypeEnum::POLYGON,
            GeometryTypeEnum::TRIANGLE,
            GeometryTypeEnum::POLYHEDRALSURFACE,
            GeometryTypeEnum::GEOMETRYCOLLECTION,
            GeometryTypeEnum::MULTIPOINT,
            GeometryTypeEnum::MULTILINESTRING,
            GeometryTypeEnum::MULTIPOLYGON => true,
            GeometryTypeEnum::CIRCULARSTRING,
            GeometryTypeEnum::COMPOUNDCURVE,
            GeometryTypeEnum::CURVEPOLYGON,
            GeometryTypeEnum::TIN,
            GeometryTypeEnum::MULTICURVE,
            GeometryTypeEnum::MULTISURFACE => false,
            GeometryTypeEnum::GEOMETRY,
            GeometryTypeEnum::CURVE,
            GeometryTypeEnum::SURFACE,
            GeometryTypeEnum::SOLID => throw new InvalidValueException(sprintf(
                'Implementation status is not applicable to non-instantiable GeometryTypeEnum::%s.',
                $type->name
            )),
        };
    }
}
