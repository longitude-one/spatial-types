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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiPolygonInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPolygonImmutable;

final class MultiPolygonImmutable extends AbstractMultiPolygonImmutable implements ImmutableMultiPolygonInterface
{
    /**
     * Return the four-dimensional XYZM coordinate layout.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYZM;
    }

    /**
     * Return the geography family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOGRAPHY;
    }

    /**
     * Return the multipolygon type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::MULTIPOLYGON;
    }
}
