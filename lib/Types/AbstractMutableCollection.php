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

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableCollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Shared atomic ordered membership and declared context.
 * @internal
 *
 * @template T of MutableSpatialInterface
 * @template TInput
 *
 * @implements MutableCollectionInterface<T, TInput>
 */
abstract class AbstractMutableCollection extends AbstractSpatialType implements MutableCollectionInterface
{
    protected SpatialModelEnum $family;

    /** @var list<T> */
    private array $elements = [];

    /**
     * @param array<T|TInput>      $elements Ordered members or supported coordinate arrays
     * @param int|SpatialReference $srid     Declared reference
     * @param SpatialModelEnum     $family   Declared family
     */
    final public function __construct(array $elements = [], int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)
    {
        $this->family = $family;
        $this->initializeSpatialReference($srid);
        $this->setElements($elements);
    }

    /**
     * @param MutableSpatialInterface $element Member retained without copying
     *
     * @phpstan-param T $element */
    public function addElement(MutableSpatialInterface $element): static
    {
        $this->elements[] = $this->hydrateElement($element);

        return $this;
    }

    /**
     * @param int $index Existing zero-based member index
     *
     * @return T
     */
    public function getElement(int $index): MutableSpatialInterface
    {
        $this->requireElementIndex($index);

        return $this->elements[$index];
    }

    /** @return list<T> */
    public function getElements(): array
    {
        return $this->elements;
    }

    /** Return the declared family. */
    public function getFamily(): SpatialModelEnum
    {
        return $this->family;
    }

    /**
     * @param int                     $index   Insertion index, including the member count
     * @param MutableSpatialInterface $element Member retained without copying
     *
     * @phpstan-param T $element
     */
    public function insertElement(int $index, MutableSpatialInterface $element): static
    {
        if ($index < 0 || $index > count($this->elements)) {
            throw new OutOfBoundsException(sprintf('Member index %d is outside the insertion range.', $index));
        }
        $checked = $this->hydrateElement($element);
        array_splice($this->elements, $index, 0, [$checked]);

        return $this;
    }

    /** Whether membership is empty. */
    public function isEmpty(): bool
    {
        return [] === $this->elements;
    }

    /** @param int $index Existing zero-based member index */
    public function removeElement(int $index): static
    {
        $this->requireElementIndex($index);
        array_splice($this->elements, $index, 1);

        return $this;
    }

    /**
     * @param int                     $index   Existing zero-based member index
     * @param MutableSpatialInterface $element Replacement retained without copying
     *
     * @phpstan-param T $element
     */
    public function setElement(int $index, MutableSpatialInterface $element): static
    {
        $this->requireElementIndex($index);
        $checked = $this->hydrateElement($element);
        array_splice($this->elements, $index, 1, [$checked]);

        return $this;
    }

    /** @param array<T|TInput> $elements Complete ordered membership */
    public function setElements(array $elements): static
    {
        $checked = [];
        foreach ($elements as $element) {
            $checked[] = $this->hydrateElement($element);
        }
        $this->elements = $checked;

        return $this;
    }

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update all descendants
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->elements as $element) {
                $element->setFamily($family);
            }
        }
        $this->family = $family;

        return $this;
    }

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update all descendants
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->elements as $element) {
                $element->setSpatialReference($reference);
            }
        }
        $this->spatialReference = $reference;

        return $this;
    }

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update all descendants
     */
    public function setSrid(int $srid, bool $propagate = true): static
    {
        return $this->setSpatialReference(SpatialReference::fromSrid($srid), $propagate);
    }

    /** @param int $index Existing zero-based member index */
    private function requireElementIndex(int $index): void
    {
        if ($index < 0 || $index >= count($this->elements)) {
            throw new OutOfBoundsException(sprintf('Member index %d does not address an existing member.', $index));
        }
    }

    /**
     * @param T|TInput $element Member or supported coordinate array
     *
     * @return T
     */
    abstract protected function hydrateElement(mixed $element): MutableSpatialInterface;
}
