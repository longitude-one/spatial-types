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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection;

/**
 * Abstract collection class.
 *
 * @internal this class provides common behaviour for geometry and geography collections
 */
abstract class AbstractCollection extends AbstractSpatialType implements CollectionInterface
{
    /**
     * @var SpatialInterface[] Elements of the collection
     */
    private array $elements = [];

    /**
     * GeometryCollection constructor.
     *
     * @param int|SpatialReference $srid     Spatial Reference Identifier
     * @param SpatialInterface[]   $elements initial elements of the collection
     */
    public function __construct(int|SpatialReference $srid = 0, array $elements = [])
    {
        $this->initializeSpatialReference($srid);
        $this->addElements($elements);
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

    /**
     * Return a deep copy of this collection with one replacement element.
     *
     * The replacement is validated through the existing collection membership
     * rules. Every unchanged element is copied to keep the returned collection
     * independent from its source.
     *
     * @param int              $elementIndex index of the element to replace; negative indexes count from the end
     * @param SpatialInterface $element      replacement spatial element
     *
     * @throws OutOfBoundsException when the collection has no elements
     */
    public function withElement(int $elementIndex, SpatialInterface $element): static
    {
        $elementIndex = $this->normalizeElementIndex($elementIndex);
        $collection = clone $this;
        $collection->elements = [];

        foreach ($this->elements as $index => $currentElement) {
            $collection->addElement($index === $elementIndex ? $element : $currentElement->withSrid($currentElement->getSrid()));
        }

        return $collection;
    }

    /**
     * Return a deep copy declared in the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $collection = parent::withSpatialReference($reference);
        $collection->elements = array_map(
            static fn (SpatialInterface $element): SpatialInterface => $element->withSpatialReference($reference),
            $this->elements
        );

        return $collection;
    }

    /**
     * Return a copy of this collection with the given Spatial Reference Identifier (SRID).
     *
     * Every contained spatial object is copied with the requested SRID, including
     * all of its nested elements, to preserve the collection's consistency.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Add a spatial object to the collection.
     *
     * @param SpatialInterface $spatial Spatial object to add to the collection
     */
    protected function addElement(SpatialInterface $spatial): static
    {
        if (!$this->hasSameDimension($spatial)) {
            throw new InvalidDimensionException('Collection cannot contain elements with different dimensions.');
        }

        if ($this->getFamily() !== $spatial->getFamily()) {
            throw new InvalidFamilyException('Collection cannot contain elements with different families.');
        }

        $this->assertSameSpatialReference($spatial, 'collection member');

        $this->elements[] = $spatial;

        return $this;
    }

    /**
     * Add spatial objects to the collection.
     *
     * @param SpatialInterface[] $elements spatial objects to add to the collection
     */
    protected function addElements(array $elements): static
    {
        foreach ($elements as $element) {
            if (!$element instanceof SpatialInterface) {
                throw new InvalidValueException('The array must contain only objects implementing SpatialInterface.');
            }

            $this->addElement($element);
        }

        return $this;
    }

    /**
     * Normalize an element index according to the collection accessor convention.
     *
     * @param int $elementIndex element index to normalize
     *
     * @throws OutOfBoundsException when the collection has no elements
     */
    private function normalizeElementIndex(int $elementIndex): int
    {
        $elementCount = count($this->elements);
        if (0 === $elementCount) {
            throw new OutOfBoundsException('The current collection of elements is empty.');
        }

        $elementIndex %= $elementCount;

        return $elementIndex < 0 ? $elementCount + $elementIndex : $elementIndex;
    }
}
