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

/**
 * Point interface.
 *
 * The Point type is a subtype of Geometry. The Point type is instantiable. A Point value
 * is a 0-dimensional geometry and represents a single location. A Point has an x coordinate value, a
 * y coordinate value, an optional z coordinate value, and an optional m coordinate value.
 */
interface PointInterface extends SpatialInterface
{
    /**
     * Point constructor.
     *
     * @param float|int|string $x    X coordinate
     * @param float|int|string $y    Y coordinate
     * @param null|int         $srid SRID
     */
    public function __construct(float|int|string $x, float|int|string $y, ?int $srid = null);

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
    public function getM(): \DateTimeInterface|float|int;

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
     * @return (\DateTimeInterface|float|int)[]
     */
    public function toArray(): array;
}
