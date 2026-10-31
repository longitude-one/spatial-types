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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/**
 * Abstract Spatial Type class.
 *
 * @internal This class is internal. It is used to create geometry or geography objects.
 */
abstract class AbstractSpatialType implements SpatialInterface
{
    /** Spatial reference system associated with this spatial value. */
    protected SpatialReference $spatialReference;

    /**
     * Return the spatial-reference identity.
     */
    public function getSpatialReference(): SpatialReference
    {
        return $this->spatialReference;
    }

    /**
     * SRID getter.
     */
    public function getSrid(): int
    {
        return $this->spatialReference->srid();
    }

    /**
     * Does this object (or point of this object) have an M coordinate?
     */
    public function hasM(): bool
    {
        return $this->getDimension()->hasM();
    }

    /**
     * Does this object have the same dimension as the other object?
     *
     * @param SpatialInterface $spatial the other object
     */
    public function hasSameDimension(SpatialInterface $spatial): bool
    {
        return !(($this->hasM() ^ $spatial->hasM()) || ($this->hasZ() ^ $spatial->hasZ()));
    }

    /**
     * Does this object (or point of this object) have a Z coordinate?
     */
    public function hasZ(): bool
    {
        return $this->getDimension()->hasZ();
    }

    /**
     * Define elements for the JSON serialization.
     *
     * @return array{type: string, coordinates: (null|float|int)[]|(null|float|int)[][]|(null|float|int)[][][]|(null|float|int)[][][][]|SpatialInterface[], srid: ?int}
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType()->value,
            'coordinates' => $this->toArray(),
            'srid' => $this->getSrid(),
        ];
    }

    /**
     * Initialize the reference system from the typed API or its legacy SRID adapter.
     *
     * @param int|SpatialReference $reference Typed reference or legacy SRID
     */
    final protected function initializeSpatialReference(int|SpatialReference $reference): void
    {
        $this->spatialReference = $reference instanceof SpatialReference
            ? $reference
            : SpatialReference::fromSrid($reference);
    }

    /**
     * Dimension getter.
     */
    abstract public function getDimension(): CoordinateDimensionEnum;

    /**
     * Family getter.
     *
     * @return SpatialModelEnum the family of the object (Geometry, Geography)
     */
    abstract public function getFamily(): SpatialModelEnum;

    /**
     * Type getter.
     */
    abstract public function getType(): GeometryTypeEnum;

    /**
     * Convert any spatial object to its array representation.
     *
     * @return (null|float|int)[]|(null|float|int)[][]|(null|float|int)[][][]|(null|float|int)[][][][]|SpatialInterface[]
     */
    abstract public function toArray(): array;
}
