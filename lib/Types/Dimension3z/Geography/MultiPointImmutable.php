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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiPointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\Dimension3z\AbstractMultiPointImmutable;

final class MultiPointImmutable extends AbstractMultiPointImmutable implements ImmutableMultiPointInterface
{
    /**
     * Define the family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOGRAPHY;
    }
}
