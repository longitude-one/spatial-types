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

/** In-place ordered mutable line-string and circular-string components. */
interface MutableCompoundCurveInterface extends CompoundCurveInterface, MutableCurveInterface
{
    /** @param MutableCircularStringInterface|MutableLineStringInterface $curve Component retained without cloning */
    public function addCurve(MutableCircularStringInterface|MutableLineStringInterface $curve): static;

    /** @param int $index Existing zero-based component index */
    public function getCurve(int $index): MutableCircularStringInterface|MutableLineStringInterface;

    /** @return list<MutableCircularStringInterface|MutableLineStringInterface> */
    public function getCurves(): array;

    /** @return list<MutableCircularStringInterface|MutableLineStringInterface> */
    public function getElements(): array;

    /** Return the final component's endpoint, or null. */
    public function getEndPoint(): ?MutablePointInterface;

    /** Return the first component's start point, or null. */
    public function getStartPoint(): ?MutablePointInterface;

    /**
     * @param int                                                       $index Zero-based insertion index, up to the component count
     * @param MutableCircularStringInterface|MutableLineStringInterface $curve Component retained without cloning
     */
    public function insertCurve(int $index, MutableCircularStringInterface|MutableLineStringInterface $curve): static;

    /** @param int $index Existing zero-based component index */
    public function removeCurve(int $index): static;

    /**
     * @param int                                                       $index Existing zero-based component index
     * @param MutableCircularStringInterface|MutableLineStringInterface $curve Component retained without cloning
     */
    public function setCurve(int $index, MutableCircularStringInterface|MutableLineStringInterface $curve): static;

    /** @param array<MutableCircularStringInterface|MutableLineStringInterface> $curves Complete ordered replacement sequence */
    public function setCurves(array $curves): static;

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update components and their points
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static;

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update components and their points
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static;

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update components and their points
     */
    public function setSrid(int $srid, bool $propagate = true): static;
}
