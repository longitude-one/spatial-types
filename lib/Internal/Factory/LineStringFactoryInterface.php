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

namespace LongitudeOne\SpatialTypes\Internal\Factory;

use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;

/**
 * Creates line strings for a spatial family.
 *
 * @internal
 */
interface LineStringFactoryInterface
{
    /**
     * Create a line string.
     *
     * @param ImmutablePointInterface[] $points  points composing the line string
     * @param SpatialContext            $context family, dimension, and SRID to apply
     */
    public function create(array $points, SpatialContext $context): ImmutableLineStringInterface;
}
