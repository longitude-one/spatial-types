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

use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;

/**
 * Creates points for a spatial family.
 *
 * @internal
 */
interface PointFactoryInterface
{
    /**
     * Create a point.
     *
     * @param Coordinates    $coordinates normalized X, Y, Z, and M values
     * @param SpatialContext $context     family, dimension, and SRID to apply
     */
    public function create(Coordinates $coordinates, SpatialContext $context): ImmutablePointInterface;

    /**
     * Create an empty point in the supplied context.
     *
     * @param SpatialContext $context family, dimension, and SRID to apply
     */
    public function createEmpty(SpatialContext $context): ImmutablePointInterface;
}
