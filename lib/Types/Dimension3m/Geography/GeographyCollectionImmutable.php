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

namespace LongitudeOne\SpatialTypes\Types\Dimension3m\Geography;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCollectionInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractCollectionImmutable;

/**
 * Geographic collection using the XYM layout.
 */
final class GeographyCollectionImmutable extends AbstractCollectionImmutable implements ImmutableCollectionInterface
{
    /**
     * Return the XYM dimension.
     */
    public function getDimension(): CoordinateDimensionEnum
    {
        return CoordinateDimensionEnum::XYM;
    }

    /**
     * Return the geography family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOGRAPHY;
    }

    /**
     * Return the geography collection type.
     */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::GEOMETRYCOLLECTION;
    }
}
