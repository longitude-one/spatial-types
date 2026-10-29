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

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Ordered mutable membership, retaining supplied instances.
 * @template T of MutableSpatialInterface
 * @template TInput
 */
interface MutableCollectionInterface extends CollectionInterface, MutableSpatialInterface
{
    /**
     * @param MutableSpatialInterface $element Member retained without copying
     *
     * @phpstan-param T $element */
    public function addElement(MutableSpatialInterface $element): static;

    /**
     * @param int $index Existing zero-based member index
     *
     * @return T
     */
    public function getElement(int $index): MutableSpatialInterface;

    /** @return list<T> */
    public function getElements(): array;

    /**
     * @param int                     $index   Insertion index, including the member count
     * @param MutableSpatialInterface $element Member retained without copying
     *
     * @phpstan-param T $element
     */
    public function insertElement(int $index, MutableSpatialInterface $element): static;

    /** @param int $index Existing zero-based member index */
    public function removeElement(int $index): static;

    /**
     * @param int                     $index   Existing zero-based member index
     * @param MutableSpatialInterface $element Replacement retained without copying
     *
     * @phpstan-param T $element
     */
    public function setElement(int $index, MutableSpatialInterface $element): static;

    /** @param array<T|TInput> $elements Complete ordered membership, including supported coordinate arrays */
    public function setElements(array $elements): static;

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update all descendants
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static;

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update all descendants
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static;

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update all descendants
     */
    public function setSrid(int $srid, bool $propagate = true): static;
}
