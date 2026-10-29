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

/**
 * Defining points of a circularly interpolated curve.
 *
 * Non-empty values contain an odd number of at least three points. Each arc
 * uses a start, intermediate and end point; consecutive arcs share endpoints.
 * ISO/IEC CD 13249-3:201x(E), clause 4.2.6.
 */
interface CircularStringInterface extends CurveInterface
{
    /**
     * Return the points that compose the circular string.
     *
     * @return PointInterface[]
     */
    public function getElements(): array;

    /**
     * Return a point from the circular string.
     *
     * @param int $index index of the point. -1 is the last point. -2 is the penultimate point, etc.
     */
    public function getPoint(int $index): PointInterface;

    /**
     * Return the points that compose the circular string.
     *
     * @return PointInterface[]
     */
    public function getPoints(): array;

    /**
     * Is the circular string empty?
     *
     * A circular string is empty when it does not contain any point.
     */
    public function isEmpty(): bool;

    /**
     * Return an array of coordinates.
     *
     * @return (null|float|int)[][]
     */
    public function toArray(): array;
}
