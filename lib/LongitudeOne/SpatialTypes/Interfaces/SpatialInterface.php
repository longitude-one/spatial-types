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

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;

/**
 * All geometric and geographic spatial objects implements this interface.
 *
 * This interface is used to get the family of the object (Geography or Geometry),
 * the type of the object (Point, LineString, Polygon, MultiPoint, MultiLineString, MultiPolygon, GeometryCollection),
 * the SpatialTypes Reference Identifier (SRID) and the dimension of the object (2D, 3D, 4D).
 */
interface SpatialInterface extends \JsonSerializable
{
    /**
     * Return the family of this spatial object.
     */
    public function getFamily(): FamilyEnum;

    /**
     * Return the SpatialTypes Reference Identifier (SRID) of this object.
     */
    public function getSrid(): ?int;

    /**
     * Return the type of this spatial object.
     *
     * This method is used by the spatial type to get the type of the object.
     */
    public function getType(): string;

    /**
     * Is this a spatial object with a time dimension? (M: moment, time dimension).
     */
    public function hasM(): bool;

    /**
     * Is this a spatial object with a Z dimension? (Z: elevation, third spatial dimension).
     */
    public function hasZ(): bool;

    /**
     * Set the SpatialTypes Reference Identifier (SRID) of this object.
     *
     * @param ?int $srid the SpatialTypes Reference Identifier (SRID)
     */
    public function setSrid(?int $srid): static;

    /**
     * Convert any spatial object to its array representation.
     *
     * Array contains SpatialInterface or multidimensional arrays of floats|integers.
     *
     * @return (float|int)[]|(float|int)[][]|(float|int)[][][]|(float|int)[][][][]|SpatialInterface[]
     */
    public function toArray(): array;
}
