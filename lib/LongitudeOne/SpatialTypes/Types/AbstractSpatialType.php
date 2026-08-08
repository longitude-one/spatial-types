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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * Abstract Spatial Type class.
 *
 * @internal This class is internal. It is used to create geometry or geography objects.
 */
abstract class AbstractSpatialType implements SpatialInterface
{
    /**
     * @var null|int the SpatialTypes Reference Identifier (SRID)
     */
    protected ?int $srid = null;

    /**
     * SRID getter.
     */
    public function getSrid(): ?int
    {
        return $this->srid;
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
     * @return array{type: string, coordinates: (\DateTimeInterface|float|int)[]|(\DateTimeInterface|float|int)[][]|(\DateTimeInterface|float|int)[][][]|(\DateTimeInterface|float|int)[][][][]|SpatialInterface[], srid: ?int}
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'coordinates' => $this->toArray(),
            'srid' => $this->getSrid(),
        ];
    }

    /**
     * SRID setter.
     *
     * @param null|int $srid the new SRID
     */
    public function setSrid(?int $srid): static
    {
        $this->srid = $srid;

        return $this;
    }

    /**
     * Dimension getter.
     */
    abstract protected function getDimension(): DimensionEnum;

    /**
     * Family getter.
     *
     * @return FamilyEnum the family of the object (Geometry, Geography)
     */
    abstract public function getFamily(): FamilyEnum;

    /**
     * Type getter.
     */
    abstract public function getType(): string;

    /**
     * Convert any spatial object to its array representation.
     *
     * @return (\DateTimeInterface|float|int)[]|(\DateTimeInterface|float|int)[][]|(\DateTimeInterface|float|int)[][][]|(\DateTimeInterface|float|int)[][][][]|SpatialInterface[]
     */
    abstract public function toArray(): array;
}
