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

/** In-place patch, ring and point editing without geometric validation.
 *
 * @phpstan-type CoordinateTuple array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}
 * @phpstan-type PatchCoordinates array<array<CoordinateTuple>>
 * @phpstan-type PatchInput MutablePolygonInterface|array<MutableLineStringInterface|array<MutablePointInterface|CoordinateTuple>>
 */
interface MutablePolyhedralSurfaceInterface extends MutableSpatialInterface, PolyhedralSurfaceInterface
{
    /** @param MutablePolygonInterface $patch Patch retained without cloning */
    public function addPatch(MutablePolygonInterface $patch): static;

    /**
     * @param int                        $patchIndex Existing zero-based patch index
     * @param MutableLineStringInterface $ring       Ring retained without cloning
     */
    public function addRing(int $patchIndex, MutableLineStringInterface $ring): static;

    /** @return list<MutablePolygonInterface> */
    public function getElements(): array;

    /** @param int $index Existing zero-based patch index */
    public function getPatch(int $index): MutablePolygonInterface;

    /** @return list<MutablePolygonInterface> */
    public function getPatches(): array;

    /**
     * @param int                     $index Zero-based insertion index, up to the patch count
     * @param MutablePolygonInterface $patch Patch retained without cloning
     */
    public function insertPatch(int $index, MutablePolygonInterface $patch): static;

    /**
     * @param int                        $patchIndex Existing zero-based patch index
     * @param int                        $ringIndex  Zero-based insertion index, up to the ring count
     * @param MutableLineStringInterface $ring       Ring retained without cloning
     */
    public function insertRing(int $patchIndex, int $ringIndex, MutableLineStringInterface $ring): static;

    /** @param int $index Existing zero-based patch index */
    public function removePatch(int $index): static;

    /**
     * @param int $patchIndex Existing zero-based patch index
     * @param int $ringIndex  Existing zero-based ring index
     */
    public function removeRing(int $patchIndex, int $ringIndex): static;

    /** @param array<PatchCoordinates> $coordinates Complete replacement patch coordinates */
    public function setArrayOfCoordinates(array $coordinates): static;

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update patches, rings and points
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static;

    /**
     * @param int                     $index Existing zero-based patch index
     * @param MutablePolygonInterface $patch Patch retained without cloning
     */
    public function setPatch(int $index, MutablePolygonInterface $patch): static;

    /**
     * @param int                   $patchIndex Existing zero-based patch index
     * @param int                   $ringIndex  Existing zero-based ring index within the patch
     * @param int                   $pointIndex Existing zero-based point index within the ring
     * @param MutablePointInterface $point      Point retained without cloning
     */
    public function setPoint(int $patchIndex, int $ringIndex, int $pointIndex, MutablePointInterface $point): static;

    /**
     * @param int                        $patchIndex Existing zero-based patch index
     * @param int                        $ringIndex  Existing zero-based ring index within the patch
     * @param MutableLineStringInterface $ring       Ring retained without cloning
     */
    public function setRing(int $patchIndex, int $ringIndex, MutableLineStringInterface $ring): static;

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update patches, rings and points
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static;

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update patches, rings and points
     */
    public function setSrid(int $srid, bool $propagate = true): static;

    /** @return (null|float|int)[][][][] */
    public function toArray(): array;
}
