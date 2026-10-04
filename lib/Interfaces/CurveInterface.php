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

namespace LongitudeOne\SpatialTypes\Interfaces;

/** Common endpoint and closure contract for supported curves. */
interface CurveInterface extends SpatialInterface
{
    /** Return the end point, or null for an empty curve. */
    public function getEndPoint(): ?PointInterface;

    /** Return the start point, or null for an empty curve. */
    public function getStartPoint(): ?PointInterface;

    /** Whether this non-empty curve has equal start and end points. */
    public function isClosed(): bool;
}
