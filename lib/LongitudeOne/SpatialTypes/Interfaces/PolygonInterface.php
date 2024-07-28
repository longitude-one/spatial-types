<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Interfaces;

interface PolygonInterface extends SpatialInterface
{
    /**
     * Return each ring composing the polygon.
     *
     * @return LineStringInterface[]
     */
    public function getRings(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int)[][][]
     */
    public function toArray(): array;
}
