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

/** In-place ordered point editing; immutable components are rejected. */
interface MutableLineStringInterface extends LineStringInterface, MutableCurveInterface
{
    /** @param MutablePointInterface $point Point retained without cloning */
    public function addPoint(MutablePointInterface $point): static;

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
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates Replacement tuples
     */
    public function setArrayOfCoordinates(array $coordinates): static;

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update every contained point
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static;

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
