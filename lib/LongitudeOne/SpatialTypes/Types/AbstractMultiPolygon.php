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
use LongitudeOne\SpatialTypes\Interfaces\MultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract multi-polygon class.
 *
 * @phpstan-type IndexedCoordinates array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}
 * @phpstan-type IndexedPolygon array<array<IndexedCoordinates>>
 *
 * @internal this class provides common behaviour for geometry and geography multi-polygons
 */
abstract class AbstractMultiPolygon extends AbstractSpatialType implements MultiPolygonInterface
{
    /**
     * @var PolygonInterface[] Polygons
     */
    private array $polygons = [];

    /**
     * AbstractMultiPolygon constructor.
     *
     * @param (IndexedPolygon|PolygonInterface)[] $polygons polygons
     * @param int|SpatialReference                $srid     Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the polygon dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the polygon SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $polygons, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addPolygons($polygons);
    }

    /**
     * Return the polygons in this multi-polygon.
     *
     * @return PolygonInterface[]
     */
    public function getElements(): array
    {
        return $this->getPolygons();
    }

    /**
     * Return a polygon from this multi-polygon.
     *
     * @param int $index Index of the ring
     *
     * @throws OutOfBoundsException when the multipolygon is empty
     */
    public function getPolygon(int $index): PolygonInterface
    {
        if (empty($this->getPolygons())) {
            throw new OutOfBoundsException('The current collection of polygons is empty.');
        }

        $index = $index % count($this->getPolygons());

        if ($index < 0) {
            $index = count($this->getPolygons()) + $index;
        }

        return $this->getPolygons()[$index];
    }

    /**
     * Get the rings of the spatial collection.
     *
     * @return PolygonInterface[]
     *
     * @throws InvalidValueException when at least one of the line strings is not a ring
     */
    public function getPolygons(): array
    {
        return $this->polygons;
    }

    /**
     * Is MultiPolygon empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->getPolygons());
    }

    /**
     * Return an array representation of this multi-polygon.
     *
     * @return (float|int)[][][][]
     */
    public function toArray(): array
    {
        $polygons = $this->getPolygons();

        return array_map(
            static fn (PolygonInterface $polygon) => $polygon->toArray(),
            $polygons
        );
    }

    /**
     * Return a deep copy of this multi-polygon with one replacement point.
     *
     * @param int         $polygonIndex index of the polygon to replace; negative indexes count from the end
     * @param int         $ringIndex    index of the ring to replace; negative indexes count from the end
     * @param int         $pointIndex   index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates  replacement point coordinates
     *
     * @throws OutOfBoundsException when the multi-polygon has no polygons
     */
    public function withPoint(int $polygonIndex, int $ringIndex, int $pointIndex, Coordinates $coordinates): static
    {
        $polygonIndex = $this->normalizePolygonIndex($polygonIndex);

        return $this->withReplacedPolygon(
            $polygonIndex,
            fn (PolygonInterface $polygon): PolygonInterface => $polygon->withPoint($ringIndex, $pointIndex, $coordinates)
        );
    }

    /**
     * Return a deep copy of this multi-polygon with one replacement polygon.
     *
     * @param int                                                                                                     $polygonIndex index of the polygon to replace; negative indexes count from the end
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates  replacement polygon coordinates
     *
     * @throws OutOfBoundsException when the multi-polygon has no polygons
     */
    public function withPolygon(int $polygonIndex, array $coordinates): static
    {
        $polygonIndex = $this->normalizePolygonIndex($polygonIndex);
        $multiPolygon = clone $this;
        $multiPolygon->polygons = [];

        foreach ($this->polygons as $index => $polygon) {
            $multiPolygon->addPolygon($index === $polygonIndex ? $coordinates : $polygon->withSrid($polygon->getSrid()));
        }

        return $multiPolygon;
    }

    /**
     * Return a deep copy of this multi-polygon with one replacement ring.
     *
     * @param int                                                                                              $polygonIndex index of the polygon to replace; negative indexes count from the end
     * @param int                                                                                              $ringIndex    index of the ring to replace; negative indexes count from the end
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates  replacement ring coordinates
     *
     * @throws OutOfBoundsException when the multi-polygon has no polygons
     */
    public function withRing(int $polygonIndex, int $ringIndex, array $coordinates): static
    {
        $polygonIndex = $this->normalizePolygonIndex($polygonIndex);

        return $this->withReplacedPolygon(
            $polygonIndex,
            fn (PolygonInterface $polygon): PolygonInterface => $polygon->withRing($ringIndex, $coordinates)
        );
    }

    /**
     * Return a deep copy declared in the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $multiPolygon = parent::withSpatialReference($reference);
        $multiPolygon->polygons = array_map(
            static fn (PolygonInterface $polygon): PolygonInterface => $polygon->withSpatialReference($reference),
            $this->polygons
        );

        return $multiPolygon;
    }

    /**
     * Return a copy of this multi-polygon with the given Spatial Reference Identifier (SRID).
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
     * Add a polygon to this multi-polygon.
     *
     * @param IndexedPolygon|PolygonInterface $polygon polygon
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the polygon
     */
    protected function addPolygon(array|PolygonInterface $polygon): static
    {
        if (is_array($polygon)) {
            $polygon = $this->createPolygonFromCoordinates($polygon);
        }

        $this->assertSameSpatialReference($polygon, 'polygon');

        $this->assertSameFamily($polygon, 'The polygon family is not compatible with the family of the current multipolygon.');

        if (!$polygon->hasSameDimension($this)) {
            throw new InvalidDimensionException('The polygon is not compatible with the dimension of the current multipolygon.');
        }

        $this->polygons[] = $polygon;

        return $this;
    }

    /**
     * Add polygons to the multipolygon instance.
     *
     * @param (IndexedPolygon|PolygonInterface)[] $polygons polygons
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the polygons
     */
    protected function addPolygons(array $polygons): static
    {
        foreach ($polygons as $polygon) {
            $this->addPolygon($polygon);
        }

        return $this;
    }

    /**
     * Normalize a polygon index according to the multi-polygon accessor convention.
     *
     * @param int $polygonIndex polygon index to normalize
     *
     * @throws OutOfBoundsException when the multi-polygon has no polygons
     */
    private function normalizePolygonIndex(int $polygonIndex): int
    {
        $polygonCount = count($this->polygons);
        if (0 === $polygonCount) {
            throw new OutOfBoundsException('The current collection of polygons is empty.');
        }

        $polygonIndex %= $polygonCount;

        return $polygonIndex < 0 ? $polygonCount + $polygonIndex : $polygonIndex;
    }

    /**
     * Return a deep copy with one transformed polygon.
     *
     * @param int      $polygonIndex index of the polygon to transform
     * @param \Closure $transform    transformation to apply
     */
    private function withReplacedPolygon(int $polygonIndex, \Closure $transform): static
    {
        $multiPolygon = clone $this;
        $multiPolygon->polygons = [];

        foreach ($this->polygons as $index => $polygon) {
            $multiPolygon->polygons[] = $index === $polygonIndex
                ? $transform($polygon)
                : $polygon->withSrid($polygon->getSrid());
        }

        return $multiPolygon;
    }
}
