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

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;

/**
 * Base spatial type interface.
 *
 * The Geometry family follows the ST_Geometry hierarchy described by ISO/IEC
 * 13249-3. This library also exposes a Geography family. Both families provide
 * the type, spatial reference identifier (SRID), and coordinate-dimension
 * information required by the concrete spatial types.
 */
interface SpatialInterface extends \JsonSerializable
{
    /**
     * Return the family of this spatial object.
     */
    public function getFamily(): FamilyEnum;

    /**
     * Return the spatial reference identifier (SRID) of this object.
     */
    public function getSrid(): ?int;

    /**
     * Return the type of this spatial object.
     *
     * This method is used internally to identify the object's type.
     */
    public function getType(): string;

    /**
     * Does this spatial object have an M (measure) dimension?
     */
    public function hasM(): bool;

    /**
     * Determine whether this object and another object have the same coordinate dimension.
     *
     * @param SpatialInterface $spatial the spatial instance to compare
     */
    public function hasSameDimension(SpatialInterface $spatial): bool;

    /**
     * Does this spatial object have a Z (elevation) dimension?
     */
    public function hasZ(): bool;

    /**
     * Set this object's spatial reference identifier (SRID).
     *
     * @param ?int $srid the SpatialTypes Reference Identifier (SRID)
     */
    public function setSrid(?int $srid): static;

    /**
     * Convert this spatial object to its array representation.
     *
     * The array contains only nested arrays of floats and integers.
     *
     * Some information is lost in this representation. For example, a line string
     * containing two points has the same representation as a multi-point containing
     * those points, and the SRID is not exported.
     *
     * Use the longitude-one/spatial-writer library to export all data.
     *
     * @return (float|int)[]|(float|int)[][]|(float|int)[][][]|(float|int)[][][][]
     */
    public function toArray(): array;
}
