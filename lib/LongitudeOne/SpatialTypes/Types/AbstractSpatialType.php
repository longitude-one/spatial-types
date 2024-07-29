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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\JsonException;
use LongitudeOne\SpatialTypes\Helper\DimensionHelper;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * Abstract Spatial Type.
 *
 * This class is the base class for all spatial types.
 */
abstract class AbstractSpatialType implements SpatialInterface
{
    /**
     * @var DimensionEnum the dimension of the object (2D, 3D with elevation, 3D with moment, 4D)
     */
    protected DimensionEnum $dimension;

    /**
     * @var FamilyEnum the family of the object (Geometry, Geography)
     */
    protected FamilyEnum $family;

    /**
     * @var null|int the SpatialTypes Reference Identifier (SRID)
     */
    protected ?int $srid = null;

    /**
     * @var TypeEnum The type of the object (Point, LineString, Polygon, etc.).
     */
    protected TypeEnum $type;

    /**
     * Dimension getter.
     */
    public function getDimension(): DimensionEnum
    {
        return $this->dimension;
    }

    /**
     * Family getter.
     *
     * @return FamilyEnum the family of the object (Geometry, Geography)
     */
    public function getFamily(): FamilyEnum
    {
        return $this->family;
    }

    /**
     * SRID getter.
     */
    public function getSrid(): ?int
    {
        return $this->srid;
    }

    /**
     * Type getter.
     */
    public function getType(): string
    {
        return $this->type->value;
    }

    /**
     * Does this object (or point of this object) have an M coordinate?
     */
    public function hasM(): bool
    {
        $dimensionHelper = new DimensionHelper($this->dimension);

        return $dimensionHelper->hasM();
    }

    /**
     * Does this object (or point of this object) have a Z coordinate?
     */
    public function hasZ(): bool
    {
        $dimensionHelper = new DimensionHelper($this->dimension);

        return $dimensionHelper->hasZ();
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
     * Convert any spatial object to its JSON representation.
     *
     * @throws JsonException if an error occurred during the JSON encoding
     */
    public function toJson(): string
    {
        $json = json_encode($this);

        if (false === $json) {
            throw new JsonException('An error occurred during the JSON encoding.');
        }

        return $json;
    }

    /**
     * Return the namespace of this class.
     */
    protected function getNamespace(): string
    {
        $class = static::class;

        return mb_substr($class, 0, mb_strrpos($class, '\\') - mb_strlen($class));
    }

    /**
     * Pre-construct method.
     */
    protected function preConstruct(): void
    {
        $this->type = $this->initType();
        $this->family = $this->initFamily();
        $this->dimension = $this->initDimension();
    }

    /**
     * This function is called in the main constructor.
     *
     * @return DimensionEnum the dimension of the object (2D, 3D with elevation, 3D with moment, 4D)
     */
    abstract protected function initDimension(): DimensionEnum;

    /**
     * This function is called in the main constructor.
     *
     * @return FamilyEnum the family of the object (Geometry, Geography)
     */
    abstract protected function initFamily(): FamilyEnum;

    /**
     * This function is called in the main constructor.
     *
     * @return TypeEnum The type of the object (Point, LineString, Polygon, etc.).
     */
    abstract protected function initType(): TypeEnum;

    /**
     * Convert any spatial object to its array representation.
     *
     * @return (\DateTimeInterface|float|int)[]|(\DateTimeInterface|float|int)[][]|(\DateTimeInterface|float|int)[][][]|(\DateTimeInterface|float|int)[][][][]|SpatialInterface[]
     */
    abstract public function toArray(): array;
}
