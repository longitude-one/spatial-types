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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z;
use LongitudeOne\SpatialTypes\Types\Dimension4zm;

/** Mutable ordered patches without geometric validation.
 * @internal
 *
 * @phpstan-import-type PatchInput from MutablePolyhedralSurfaceInterface
 * @phpstan-import-type PatchCoordinates from MutablePolyhedralSurfaceInterface
 */
abstract class AbstractPolyhedralSurface extends AbstractSpatialType implements MutablePolyhedralSurfaceInterface
{
    protected SpatialModelEnum $family;

    /** @var list<MutablePolygonInterface> */
    private array $patches = [];

    /**
     * @param array<PatchInput>    $patches Ordered patches or ring sequences
     * @param int|SpatialReference $srid    Declared reference
     * @param SpatialModelEnum     $family  Declared family
     */
    final public function __construct(array $patches = [], int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)
    {
        $this->family = $family;
        $this->initializeSpatialReference($srid);
        $this->patches = $this->hydratePatches($patches);
    }

    /** @param MutablePolygonInterface $patch Patch retained without cloning */
    public function addPatch(MutablePolygonInterface $patch): static
    {
        $this->checkPatchComponents($patch);
        $this->patches[] = $patch;

        return $this;
    }

    /**
     * @param int                        $patchIndex Existing zero-based patch index
     * @param MutableLineStringInterface $ring       Ring retained without cloning
     */
    public function addRing(int $patchIndex, MutableLineStringInterface $ring): static
    {
        $this->getPatch($patchIndex)->addRing($ring);

        return $this;
    }

    /** @return list<MutablePolygonInterface> */
    public function getElements(): array
    {
        return $this->patches;
    }

    /** Return the declared family. */
    public function getFamily(): SpatialModelEnum
    {
        return $this->family;
    }

    /** @param int $index Existing zero-based patch index */
    public function getPatch(int $index): MutablePolygonInterface
    {
        $this->requirePatchIndex($index);

        return $this->patches[$index];
    }

    /** @return list<MutablePolygonInterface> */
    public function getPatches(): array
    {
        return $this->patches;
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POLYHEDRALSURFACE;
    }

    /**
     * @param int                     $index Zero-based insertion index, up to the patch count
     * @param MutablePolygonInterface $patch Patch retained without cloning
     */
    public function insertPatch(int $index, MutablePolygonInterface $patch): static
    {
        if ($index < 0 || $index > count($this->patches)) {
            throw new OutOfBoundsException(sprintf('Patch index %d is outside the insertion range.', $index));
        }
        $this->checkPatchComponents($patch);
        array_splice($this->patches, $index, 0, [$patch]);

        return $this;
    }

    /**
     * @param int                        $patchIndex Existing zero-based patch index
     * @param int                        $ringIndex  Zero-based insertion index, up to the ring count
     * @param MutableLineStringInterface $ring       Ring retained without cloning
     */
    public function insertRing(int $patchIndex, int $ringIndex, MutableLineStringInterface $ring): static
    {
        $this->getPatch($patchIndex)->insertRing($ringIndex, $ring);

        return $this;
    }

    /** Whether there are no patches. */
    public function isEmpty(): bool
    {
        return [] === $this->patches;
    }

    /** @param int $index Existing zero-based patch index */
    public function removePatch(int $index): static
    {
        $this->requirePatchIndex($index);
        array_splice($this->patches, $index, 1);

        return $this;
    }

    /**
     * @param int $patchIndex Existing zero-based patch index
     * @param int $ringIndex  Existing zero-based ring index
     */
    public function removeRing(int $patchIndex, int $ringIndex): static
    {
        $this->getPatch($patchIndex)->removeRing($ringIndex);

        return $this;
    }

    /** @param array<PatchCoordinates> $coordinates Complete replacement patch coordinates */
    public function setArrayOfCoordinates(array $coordinates): static
    {
        $this->patches = $this->hydratePatches($coordinates);

        return $this;
    }

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update patches, rings and points
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->patches as $patch) {
                $patch->setFamily($family);
            }
        }
        $this->family = $family;

        return $this;
    }

    /**
     * @param int                     $index Existing zero-based patch index
     * @param MutablePolygonInterface $patch Patch retained without cloning
     */
    public function setPatch(int $index, MutablePolygonInterface $patch): static
    {
        $this->requirePatchIndex($index);
        $this->checkPatchComponents($patch);
        array_splice($this->patches, $index, 1, [$patch]);

        return $this;
    }

    /**
     * @param int                   $patchIndex Existing zero-based patch index
     * @param int                   $ringIndex  Existing zero-based ring index within the patch
     * @param int                   $pointIndex Existing zero-based point index within the ring
     * @param MutablePointInterface $point      Point retained without cloning
     */
    public function setPoint(int $patchIndex, int $ringIndex, int $pointIndex, MutablePointInterface $point): static
    {
        $this->getPatch($patchIndex)->setPoint($ringIndex, $pointIndex, $point);

        return $this;
    }

    /**
     * @param int                        $patchIndex Existing zero-based patch index
     * @param int                        $ringIndex  Existing zero-based ring index within the patch
     * @param MutableLineStringInterface $ring       Ring retained without cloning
     */
    public function setRing(int $patchIndex, int $ringIndex, MutableLineStringInterface $ring): static
    {
        $this->getPatch($patchIndex)->setRing($ringIndex, $ring);

        return $this;
    }

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update patches, rings and points
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->patches as $patch) {
                $patch->setSpatialReference($reference);
            }
        }
        $this->spatialReference = $reference;

        return $this;
    }

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update patches, rings and points
     */
    public function setSrid(int $srid, bool $propagate = true): static
    {
        return $this->setSpatialReference(SpatialReference::fromSrid($srid), $propagate);
    }

    /** @return (null|float|int)[][][][] */
    public function toArray(): array
    {
        return array_map(static fn (MutablePolygonInterface $patch): array => $patch->toArray(), $this->patches);
    }

    /** @param MutablePolygonInterface $patch Patch whose descendants must be mutable */
    private function checkPatchComponents(MutablePolygonInterface $patch): void
    {
        foreach ($patch->getRings() as $ring) {
            if (!$ring instanceof MutableLineStringInterface) {
                throw new InvalidValueException('Mutable surface patches require mutable line-string rings.');
            }
            foreach ($ring->getPoints() as $point) {
                if (!$point instanceof MutablePointInterface) {
                    throw new InvalidValueException('Mutable surface patches require mutable point components.');
                }
            }
        }
    }

    /**
     * @param array<PatchInput> $patches Ordered patches or ring sequences
     *
     * @return list<MutablePolygonInterface>
     */
    private function hydratePatches(array $patches): array
    {
        $hydrated = [];
        foreach ($patches as $patch) {
            if (is_array($patch)) {
                $patch = match ($this->getDimension()) {
                    CoordinateDimensionEnum::XYZ => new Dimension3z\Polygon($patch, $this->spatialReference, $this->family),
                    CoordinateDimensionEnum::XYZM => new Dimension4zm\Polygon($patch, $this->spatialReference, $this->family),
                    default => throw new InvalidValueException('Mutable polyhedral surfaces require XYZ or XYZM coordinates.'),
                };
            }
            if (!$patch instanceof MutablePolygonInterface) {
                throw new InvalidValueException('Mutable surfaces require mutable polygon patches or ring sequences.');
            }
            $this->checkPatchComponents($patch);
            $hydrated[] = $patch;
        }

        return $hydrated;
    }

    /** @param int $index Existing zero-based patch index */
    private function requirePatchIndex(int $index): void
    {
        if ($index < 0 || $index >= count($this->patches)) {
            throw new OutOfBoundsException(sprintf('Patch index %d does not address an existing patch.', $index));
        }
    }
}
