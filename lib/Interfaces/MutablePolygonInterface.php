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

/** In-place ordered ring and point editing without geometric validation. */
interface MutablePolygonInterface extends MutableSpatialInterface, PolygonInterface
{
    /** @param MutableLineStringInterface $ring Ring retained without cloning */
    public function addRing(MutableLineStringInterface $ring): static;

    /** @return list<MutableLineStringInterface> */
    public function getElements(): array;

    /** @param int $index Existing zero-based ring index */
    public function getRing(int $index): MutableLineStringInterface;

    /** @return list<MutableLineStringInterface> */
    public function getRings(): array;

    /**
     * @param int                        $index Zero-based insertion index, up to the ring count
     * @param MutableLineStringInterface $ring  Ring retained without cloning
     */
    public function insertRing(int $index, MutableLineStringInterface $ring): static;

    /** @param int $index Existing zero-based ring index */
    public function removeRing(int $index): static;

    /** @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates Complete replacement ring coordinates */
    public function setArrayOfCoordinates(array $coordinates): static;

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update rings and their points
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static;

    /**
     * @param int                   $ringIndex  Existing zero-based ring index
     * @param int                   $pointIndex Existing zero-based point index within the ring
     * @param MutablePointInterface $point      Point retained without cloning
     */
    public function setPoint(int $ringIndex, int $pointIndex, MutablePointInterface $point): static;

    /**
     * @param int                        $index Existing zero-based ring index
     * @param MutableLineStringInterface $ring  Ring retained without cloning
     */
    public function setRing(int $index, MutableLineStringInterface $ring): static;

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update rings and their points
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static;

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update rings and their points
     */
    public function setSrid(int $srid, bool $propagate = true): static;
}
