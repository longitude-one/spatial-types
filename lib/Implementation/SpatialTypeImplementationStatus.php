<?php
/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Implementation;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\LogicException;

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
     * @throws LogicException        when the geometry type has no implementation classification
     */
    public static function isFullyImplemented(GeometryTypeEnum $type): bool
    {
        if (!$type->isInstantiable()) {
            throw new InvalidValueException(\sprintf('Implementation status is not applicable to non-instantiable GeometryTypeEnum::%s.', $type->name));
        }

        return match ($type) {
            GeometryTypeEnum::CIRCULARSTRING,
            GeometryTypeEnum::COMPOUNDCURVE,
            GeometryTypeEnum::POINT,
            GeometryTypeEnum::LINESTRING,
            GeometryTypeEnum::POLYGON,
            GeometryTypeEnum::TRIANGLE,
            GeometryTypeEnum::POLYHEDRALSURFACE,
            GeometryTypeEnum::GEOMETRYCOLLECTION,
            GeometryTypeEnum::MULTIPOINT,
            GeometryTypeEnum::MULTILINESTRING,
            GeometryTypeEnum::MULTIPOLYGON => true,
            GeometryTypeEnum::BREPSOLID,
            GeometryTypeEnum::CIRCLE,
            GeometryTypeEnum::CLOTHOID,
            GeometryTypeEnum::COMPOUNDSURFACE,
            GeometryTypeEnum::ELLIPTICALCURVE,
            GeometryTypeEnum::GEODESICSTRING,
            GeometryTypeEnum::NURBSCURVE,
            GeometryTypeEnum::SPIRALCURVE,
            GeometryTypeEnum::CURVEPOLYGON,
            GeometryTypeEnum::TIN,
            GeometryTypeEnum::MULTICURVE,
            GeometryTypeEnum::MULTISURFACE => false,
            default => throw new LogicException(\sprintf('Implementation status is undefined for GeometryTypeEnum::%s.', $type->name)),
        };
    }
}
