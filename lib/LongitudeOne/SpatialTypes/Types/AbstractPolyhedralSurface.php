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
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Validator\PolyhedralSurfaceValidation;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract polyhedral surface class.
 *
 * @phpstan-type IndexedCoordinates array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}
 * @phpstan-type IndexedPolygon array<array<IndexedCoordinates>>
 *
 * @internal this class provides common behaviour for geometry and geography polyhedral surfaces
 */
abstract class AbstractPolyhedralSurface extends AbstractSpatialType implements PolyhedralSurfaceInterface
{
    /**
     * @var PolygonInterface[] Surface patches
     */
    private array $patches = [];

    /**
     * AbstractPolyhedralSurface constructor.
     *
     * @param (IndexedPolygon|PolygonInterface)[] $patches Surface patches
     * @param int|SpatialReference                $srid    Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the polygon dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the polygon SRID
     * @throws InvalidValueException     when coordinates or surface topology are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $patches = [], int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addPatches($patches);
        PolyhedralSurfaceValidation::assertValid($this);
    }

    /**
     * Return the polygons in this polyhedral surface.
     *
     * @return PolygonInterface[]
     */
    public function getElements(): array
    {
        return $this->getPatches();
    }

    /**
     * Return a polygon from this polyhedral surface.
     *
     * @param int $index Index of the patch
     *
     * @throws OutOfBoundsException when the polyhedral surface is empty
     */
    public function getPatch(int $index): PolygonInterface
    {
        if (empty($this->getPatches())) {
            throw new OutOfBoundsException('The current surface has no patches.');
        }

        $index = $index % count($this->getPatches());

        if ($index < 0) {
            $index = count($this->getPatches()) + $index;
        }

        return $this->getPatches()[$index];
    }

    /**
     * Return the polygon patches of this surface.
     *
     * @return PolygonInterface[]
     */
    public function getPatches(): array
    {
        return $this->patches;
    }

    /**
     * Is this surface empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->getPatches());
    }

    /**
     * Return an array representation of this polyhedral surface.
     *
     * @return (float|int)[][][][]
     */
    public function toArray(): array
    {
        $patches = $this->getPatches();

        return array_map(
            static fn (PolygonInterface $patch) => $patch->toArray(),
            $patches
        );
    }

    /**
     * Replace all patches in an independent surface, including the empty surface.
     *
     * @param IndexedPolygon[] $coordinates Replacement patch coordinates
     *
     * @throws InvalidValueException when the replacement violates surface constraints
     */
    public function withArrayOfCoordinates(array $coordinates): static
    {
        $surface = clone $this;
        $surface->patches = [];
        $surface->addPatches($coordinates);
        PolyhedralSurfaceValidation::assertValid($surface);

        return $surface;
    }

    /**
     * Return a deep copy of this polyhedral surface with one replacement polygon.
     *
     * @param int                                                                                                     $patchIndex  index of the polygon to replace; negative indexes count from the end
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates replacement polygon coordinates
     *
     * @throws OutOfBoundsException  when the polyhedral surface has no patches
     * @throws InvalidValueException when the replacement violates surface constraints
     */
    public function withPatch(int $patchIndex, array $coordinates): static
    {
        $patchIndex = $this->normalizePatchIndex($patchIndex);
        $surface = clone $this;
        $surface->patches = [];

        foreach ($this->patches as $index => $patch) {
            $surface->addPatch($index === $patchIndex ? $coordinates : $patch->withSpatialReference($patch->getSpatialReference()));
        }

        PolyhedralSurfaceValidation::assertValid($surface);

        return $surface;
    }

    /**
     * Return a deep copy of this polyhedral surface with one replacement point.
     *
     * @param int         $patchIndex  index of the polygon to replace; negative indexes count from the end
     * @param int         $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     *
     * @throws OutOfBoundsException  when the polyhedral surface has no patches
     * @throws InvalidValueException when the replacement violates surface constraints
     */
    public function withPoint(int $patchIndex, int $ringIndex, int $pointIndex, Coordinates $coordinates): static
    {
        $patchIndex = $this->normalizePatchIndex($patchIndex);

        return $this->withReplacedPatch(
            $patchIndex,
            fn (PolygonInterface $patch): PolygonInterface => $patch->withPoint($ringIndex, $pointIndex, $coordinates)
        );
    }

    /**
     * Return a deep copy of this polyhedral surface with one replacement ring.
     *
     * @param int                                                                                              $patchIndex  index of the polygon to replace; negative indexes count from the end
     * @param int                                                                                              $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates replacement ring coordinates
     *
     * @throws OutOfBoundsException  when the polyhedral surface has no patches
     * @throws InvalidValueException when the replacement violates surface constraints
     */
    public function withRing(int $patchIndex, int $ringIndex, array $coordinates): static
    {
        $patchIndex = $this->normalizePatchIndex($patchIndex);

        return $this->withReplacedPatch(
            $patchIndex,
            fn (PolygonInterface $patch): PolygonInterface => $patch->withRing($ringIndex, $coordinates)
        );
    }

    /**
     * Return a deep copy declared in the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $surface = parent::withSpatialReference($reference);
        $surface->patches = array_map(
            static fn (PolygonInterface $patch): PolygonInterface => $patch->withSpatialReference($reference),
            $this->patches
        );

        PolyhedralSurfaceValidation::assertValid($surface);

        return $surface;
    }

    /**
     * Return a copy of this polyhedral surface with the given Spatial Reference Identifier (SRID).
     *
     * Every polygon, ring, and point is copied with the requested SRID to
     * preserve the aggregate's internal SRID consistency.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Add a polygon to this polyhedral surface.
     *
     * @param IndexedPolygon|PolygonInterface $patch polygon
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the polygon
     */
    protected function addPatch(array|PolygonInterface $patch): static
    {
        if (is_array($patch)) {
            $patch = $this->createPolygonFromCoordinates($patch);
        }

        $this->assertSameSpatialReference($patch, 'polygon');

        $this->assertSameFamily($patch, 'The polygon family is not compatible with the family of the current polyhedral surface.');

        $this->assertSameDimension($patch, 'The polygon is not compatible with the dimension of the current polyhedral surface.');

        $this->patches[] = $patch;

        return $this;
    }

    /**
     * Add polygons to the polyhedral surface instance.
     *
     * @param (IndexedPolygon|PolygonInterface)[] $patches Surface patches
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the polygons
     */
    protected function addPatches(array $patches): static
    {
        foreach ($patches as $patch) {
            $this->addPatch($patch);
        }

        return $this;
    }

    /**
     * Normalize a polygon index according to the polyhedral surface accessor convention.
     *
     * @param int $patchIndex polygon index to normalize
     *
     * @throws OutOfBoundsException when the polyhedral surface has no patches
     */
    private function normalizePatchIndex(int $patchIndex): int
    {
        $patchCount = count($this->patches);
        if (0 === $patchCount) {
            throw new OutOfBoundsException('The current surface has no patches.');
        }

        $patchIndex %= $patchCount;

        return $patchIndex < 0 ? $patchCount + $patchIndex : $patchIndex;
    }

    /**
     * Return a deep copy with one transformed polygon.
     *
     * @param int      $patchIndex index of the polygon to transform
     * @param \Closure $transform  transformation to apply
     */
    private function withReplacedPatch(int $patchIndex, \Closure $transform): static
    {
        $surface = clone $this;
        $surface->patches = [];

        foreach ($this->patches as $index => $patch) {
            $surface->patches[] = $index === $patchIndex
                ? $transform($patch)
                : $patch->withSpatialReference($patch->getSpatialReference());
        }

        PolyhedralSurfaceValidation::assertValid($surface);

        return $surface;
    }
}
