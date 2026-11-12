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

/** Immutable triangle read and copy contract. */
interface ImmutableTriangleInterface extends ImmutablePolygonInterface, TriangleInterface
{
    /** Return an immutable boundary ring.
     * @param int $index Ring index
     */
    public function getRing(int $index): ImmutableLineStringInterface;
}
