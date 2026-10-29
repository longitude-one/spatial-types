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

use LongitudeOne\SpatialTypes\Boundary\PolygonBoundary;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract polygon class.
 *
 * @internal this class provides common behaviour for geometry and geography polygons
 */
abstract class AbstractPolygonImmutable extends AbstractSpatialTypeImmutable implements ImmutablePolygonInterface
{
    /** ISO polygon boundary: an exterior ring followed by interior rings. */
    protected PolygonBoundary $boundary;

    /**
     * AbstractPolygonImmutable constructor.
     *
     * @param (array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|ImmutableLineStringInterface|ImmutablePointInterface[])[] $rings rings of the polygon
     * @param int|SpatialReference                                                                                                                                             $srid  Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when a coordinate tuple does not match the polygon dimension
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $rings, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->boundary = new PolygonBoundary();
        $this->addRings($rings);
    }

    /**
     * Return the rings in this polygon.
     *
     * @return ImmutableLineStringInterface[]
     */
    public function getElements(): array
    {
        return $this->getRings();
    }

    /**
     * Return a ring from this polygon.
     *
     * @param int $index Index of the ring
     */
    public function getRing(int $index): ImmutableLineStringInterface
    {
        return $this->boundary->ringAt($index);
    }

    /**
     * Get the rings of the spatial collection.
     *
     * @return ImmutableLineStringInterface[]
     */
    public function getRings(): array
    {
        return $this->boundary->rings();
    }

    /**
     * Is this polygon empty?
     */
    public function isEmpty(): bool
    {
        return 0 === $this->boundary->count();
    }

    /**
     * Return an array representation of this polygon.
     *
     * @return (null|float|int)[][][]
     */
    public function toArray(): array
    {
        $rings = $this->getRings();

        return array_map(
            static fn (ImmutableLineStringInterface $ring) => $ring->toArray(),
            $rings
        );
    }

    /**
     * Return a copy of this polygon with replacement ring coordinates.
     *
     * The original polygon and its rings are left unchanged. The copied
     * instance retains its family, dimension, and Spatial Reference Identifier
     * (SRID); the existing line-string hydrator creates members from each tuple
     * against that retained context.
     *
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates replacement ring coordinates
     */
    public function withArrayOfCoordinates(array $coordinates): static
    {
        $polygon = clone $this;
        $polygon->boundary = new PolygonBoundary();
        $polygon->addRings($coordinates);

        return $polygon;
    }

    /**
     * Return a deep copy of this polygon with one replacement point in a ring.
     *
     * The original polygon and its rings remain unchanged. Replacing either
     * endpoint of a ring also replaces the opposite endpoint so that the ring
     * remains closed.
     *
     * @param int         $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     *
     * @throws OutOfBoundsException when the polygon has no rings
     */
    public function withPoint(int $ringIndex, int $pointIndex, Coordinates $coordinates): static
    {
        $ringIndex = $this->normalizeRingIndex($ringIndex);
        $polygon = clone $this;
        $polygon->boundary = new PolygonBoundary();

        foreach ($this->getRings() as $index => $ring) {
            if ($index === $ringIndex) {
                $polygon->boundary->addRing($this->replaceRingPoint($ring, $pointIndex, $coordinates));

                continue;
            }

            $polygon->boundary->addRing($ring->withSrid($ring->getSrid()));
        }

        return $polygon;
    }

    /**
     * Return a deep copy of this polygon with one replacement ring.
     *
     * The original polygon and all its rings remain unchanged. The replacement
     * coordinates are created through the polygon's existing family, dimension,
     * and Spatial Reference Identifier (SRID) context without enforcing ring validity.
     *
     * @param int                                                                                                        $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates replacement ring coordinates
     *
     * @throws OutOfBoundsException when the polygon has no rings
     */
    public function withRing(int $ringIndex, array $coordinates): static
    {
        $ringIndex = $this->normalizeRingIndex($ringIndex);
        $polygon = clone $this;
        $polygon->boundary = new PolygonBoundary();

        foreach ($this->getRings() as $index => $ring) {
            $polygon->addRing($index === $ringIndex ? $coordinates : $ring->withSrid($ring->getSrid()));
        }

        return $polygon;
    }

    /**
     * Return a deep copy declared in the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $polygon = parent::withSpatialReference($reference);
        $polygon->boundary = new PolygonBoundary(array_map(
            static fn (ImmutableLineStringInterface $ring): ImmutableLineStringInterface => $ring->withSpatialReference($reference),
            $this->getRings()
        ));

        return $polygon;
    }

    /**
     * Return a copy of this polygon with the given Spatial Reference Identifier (SRID).
     *
     * Every ring, and therefore every point in every ring, is copied with the
     * requested SRID to preserve the polygon's internal SRID consistency.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Get the line strings of the spatial collection.
     *
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|ImmutableLineStringInterface|ImmutablePointInterface[] $ring the ring to add
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the ring
     */
    protected function addRing(array|ImmutableLineStringInterface $ring): static
    {
        if (is_array($ring)) {
            $ring = $this->createLineStringFromCoordinates($ring);
        }

        ImmutableComponentGuard::check($ring);
        $this->boundary->addRing($ring);

        return $this;
    }

    /**
     * Add a ring to the spatial collection.
     *
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[][]|ImmutableLineStringInterface[]|ImmutablePointInterface[][] $rings the ring to add
     *
     * @throws SpatialTypeExceptionInterface when an input does not provide a line-string interface or coordinate tuples
     */
    protected function addRings(array $rings): static
    {
        foreach ($rings as $ring) {
            if (!is_array($ring) && !$ring instanceof ImmutableLineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing ImmutableLineStringInterface or array of coordinates.');
            }

            $this->addRing($ring);
        }

        return $this;
    }

    /**
     * Normalize a ring index according to the polygon accessor convention.
     *
     * @param int $ringIndex ring index to normalize
     *
     * @throws OutOfBoundsException when the polygon has no rings
     */
    private function normalizeRingIndex(int $ringIndex): int
    {
        $ringCount = $this->boundary->count();
        if (0 === $ringCount) {
            throw new OutOfBoundsException('The current collection of lineStrings is empty.');
        }

        $ringIndex %= $ringCount;

        return $ringIndex < 0 ? $ringCount + $ringIndex : $ringIndex;
    }

    /**
     * Replace a point in a copied ring while preserving its closure.
     *
     * @param ImmutableLineStringInterface $ring        ring to copy and update
     * @param int                          $pointIndex  index of the point to replace
     * @param Coordinates                  $coordinates replacement point coordinates
     */
    private function replaceRingPoint(ImmutableLineStringInterface $ring, int $pointIndex, Coordinates $coordinates): ImmutableLineStringInterface
    {
        $pointCount = count($ring->getPoints());
        if (0 === $pointCount) {
            throw new OutOfBoundsException('The current collection of points is empty.');
        }

        $normalizedPointIndex = $pointIndex % $pointCount;
        if ($normalizedPointIndex < 0) {
            $normalizedPointIndex += $pointCount;
        }

        $replacement = $ring->withPoint($normalizedPointIndex, $coordinates);
        $lastPointIndex = $pointCount - 1;
        if (0 === $normalizedPointIndex && 0 !== $lastPointIndex) {
            return $replacement->withPoint($lastPointIndex, $coordinates);
        }

        if ($lastPointIndex === $normalizedPointIndex && 0 !== $lastPointIndex) {
            return $replacement->withPoint(0, $coordinates);
        }

        return $replacement;
    }
}
