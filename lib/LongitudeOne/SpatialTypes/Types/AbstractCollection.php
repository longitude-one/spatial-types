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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Types\Geography\GeographyCollection;
use LongitudeOne\SpatialTypes\Types\Geometry\GeometryCollection;

abstract class AbstractCollection extends AbstractSpatialType implements CollectionInterface
{
    /**
     * @var SpatialInterface[] Elements of the collection
     */
    private array $elements = [];

    /**
     * GeometryCollection constructor.
     *
     * @param null|int $srid Spatial Reference Identifier
     */
    public function __construct(?int $srid = null)
    {
        $this->setSrid($srid);
    }

    /**
     * Add a spatial object to the collection.
     *
     * @param SpatialInterface $spatial Spatial object to add to the collection
     */
    public function addElement(SpatialInterface $spatial): static
    {
        if ($spatial instanceof GeometryCollection || $spatial instanceof GeographyCollection) {
            throw new InvalidValueException(sprintf('An instance of %s cannot contain another GeometryCollection nor GeographyCollection.', static::class));
        }

        if (!$this->hasSameDimension($spatial)) {
            throw new InvalidDimensionException('Collection cannot contain elements with different dimensions.');
        }

        if ($this->getFamily() !== $spatial->getFamily()) {
            throw new InvalidFamilyException('Collection cannot contain elements with different families.');
        }

        if (!empty($spatial->getSrid()) && !empty($this->getSrid()) && $this->getSrid() !== $spatial->getSrid()) {
            throw new InvalidSridException('Collection cannot contain elements with different SRIDs.');
        }

        $this->elements[] = $spatial;

        return $this;
    }

    /**
     * Get an array of spatial objects in the collection.
     *
     * @return SpatialInterface[] Elements of the collection
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Does the collection contain the spatial object?
     *
     * @param SpatialInterface $spatial Spatial object to check
     */
    public function hasElement(SpatialInterface $spatial): bool
    {
        return in_array($spatial, $this->elements, true);
    }

    /**
     * Is the collection empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->getElements());
    }

    /**
     * Remove a spatial object from the collection.
     *
     * @param SpatialInterface $spatial Spatial object to remove
     */
    public function removeElement(SpatialInterface $spatial): static
    {
        $key = array_search($spatial, $this->elements, true);
        if (false !== $key) {
            unset($this->elements[$key]);
            $this->elements = array_values($this->elements);

            return $this;
        }

        throw new InvalidValueException('The spatial object is not in the collection.');
    }

    /**
     * Convert the collection to an array.
     *
     * @return (float|(float|int|string)[]|(float|int|string)[][]|(float|int|string)[][][]|int|string)[][]
     */
    public function toArray(): array
    {
        $collection = [];
        foreach ($this->getElements() as $element) {
            $collection[] = $element->toArray();
        }

        return $collection;
    }
}
