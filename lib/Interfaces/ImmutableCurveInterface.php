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

namespace LongitudeOne\SpatialTypes\Interfaces;

/** Immutable curve read and copy contract. */
interface ImmutableCurveInterface extends CurveInterface, ImmutableSpatialInterface
{
    /** Return the end point, or null for an empty curve. */
    public function getEndPoint(): ?ImmutablePointInterface;

    /** Return the start point, or null for an empty curve. */
    public function getStartPoint(): ?ImmutablePointInterface;
}
