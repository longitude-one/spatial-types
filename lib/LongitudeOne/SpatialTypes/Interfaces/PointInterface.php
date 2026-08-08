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
 * Point interface.
 *
 * The Point type corresponds to the instantiable ST_Point subtype of ST_Geometry
 * defined by ISO/IEC 13249-3. A point is a zero-dimensional geometry that
 * represents a single location. It has X and Y coordinates and may have Z
 * (elevation) and M (measure) coordinates.
 */
interface PointInterface extends SpatialInterface
{
    /**
     * Is this point equal to another point?
     *
     * @param PointInterface $point The point to compare
     */
    public function equalsTo(PointInterface $point): bool;

    /**
     * Get the latitude.
     */
    public function getLatitude(): float|int;

    /**
     * Get the longitude.
     */
    public function getLongitude(): float|int;

    /**
     * Get the M coordinate.
     */
    public function getM(): float|int;

    /**
     * Get the X coordinate.
     */
    public function getX(): float|int;

    /**
     * Get the Y coordinate.
     */
    public function getY(): float|int;

    /**
     * Get the Z coordinate (elevation).
     */
    public function getZ(): float|int;

    /**
     * Return an array of all coordinates.
     *
     * @return (float|int)[]
     */
    public function toArray(): array;
}
