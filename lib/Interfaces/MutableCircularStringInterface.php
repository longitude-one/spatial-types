<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Interfaces;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** In-place ordered point editing; immutable components are rejected. */
interface MutableCircularStringInterface extends CircularStringInterface, MutableCurveInterface
{
    /** @param MutablePointInterface $point Point retained without cloning */
    public function addPoint(MutablePointInterface $point): static;

    /**
     * @param MutablePointInterface $passingPoint     Intermediate point retained without cloning
     * @param MutablePointInterface $destinationPoint End point retained without cloning
     */
    public function appendArc(MutablePointInterface $passingPoint, MutablePointInterface $destinationPoint): static;

    /** @return MutablePointInterface[] */
    public function getElements(): array;

    /** Return the last point, or null for EMPTY. */
    public function getEndPoint(): ?MutablePointInterface;

    /** @param int $index Existing zero-based point index */
    public function getPoint(int $index): MutablePointInterface;

    /** @return MutablePointInterface[] */
    public function getPoints(): array;

    /** Return the first point, or null for EMPTY. */
    public function getStartPoint(): ?MutablePointInterface;

    /**
     * @param int                   $index Zero-based insertion index, up to the point count
     * @param MutablePointInterface $point Point retained without cloning
     */
    public function insertPoint(int $index, MutablePointInterface $point): static;

    /** @param int $index Existing zero-based point index */
    public function removePoint(int $index): static;

    /**
     * @param int                   $index            Zero-based arc index; all three defining points must exist
     * @param MutablePointInterface $passingPoint     Replacement intermediate point
     * @param MutablePointInterface $destinationPoint Replacement shared endpoint
     */
    public function replaceArc(int $index, MutablePointInterface $passingPoint, MutablePointInterface $destinationPoint): static;

    /**
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}> $coordinates Replacement tuples
     */
    public function setArrayOfCoordinates(array $coordinates): static;

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update every contained point
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static;

    /** @param MutablePointInterface $point First point retained without cloning */
    public function setOrigin(MutablePointInterface $point): static;

    /**
     * @param int                   $index Existing zero-based point index
     * @param MutablePointInterface $point Point retained without cloning
     */
    public function setPoint(int $index, MutablePointInterface $point): static;

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update every contained point
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static;

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update every contained point
     */
    public function setSrid(int $srid, bool $propagate = true): static;
}
