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

use LongitudeOne\SpatialTypes\Value\Coordinates;

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
     * Return the normalized coordinates of this point.
     */
    public function getCoordinates(): ?Coordinates;

    /**
     * Get the latitude.
     */
    public function getLatitude(): float|int|null;

    /**
     * Get the longitude.
     */
    public function getLongitude(): float|int|null;

    /**
     * Get the M coordinate.
     */
    public function getM(): float|int|null;

    /**
     * Get the X coordinate.
     */
    public function getX(): float|int|null;

    /**
     * Get the Y coordinate.
     */
    public function getY(): float|int|null;

    /**
     * Get the Z coordinate (elevation).
     */
    public function getZ(): float|int|null;

    /**
     * Return an array of all coordinates.
     *
     * @return (float|int)[]
     */
    public function toArray(): array;

    /**
     * Return a new point with the supplied normalized coordinates.
     *
     * The coordinates must use the same dimension as this point. The point's
     * family and Spatial Reference Identifier (SRID) are preserved.
     *
     * @param Coordinates $coordinates replacement coordinates
     */
    public function withCoordinates(Coordinates $coordinates): static;
}
