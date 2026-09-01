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

use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Collection\AbstractPointCollection;
use LongitudeOne\SpatialTypes\Validator\LineStringValidation;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract LineString class.
 *
 * @internal this class provides common behaviour for geometry and geography line strings
 */
abstract class AbstractLineString extends AbstractPointCollection implements LineStringInterface
{
    /**
     * Get the elements of this line string.
     *
     * @return PointInterface[]
     */
    public function getElements(): array
    {
        return $this->getPoints();
    }

    /**
     * This line string is closed when the first point is the same as the last point.
     */
    public function isClosed(): bool
    {
        return $this->isLine() && $this->points[0]->equalsTo($this->points[count($this->points) - 1]);
    }

    /**
     * Is this line string a line?
     *
     * A line is composed of at least two points.
     */
    public function isLine(): bool
    {
        return count($this->points) >= 2;
    }

    /**
     * Return an array representation of the line string.
     *
     * @return (float|int)[][]
     */
    public function toArray(): array
    {
        $points = $this->getPoints();

        return array_map(
            static fn (PointInterface $point) => $point->toArray(),
            $points
        );
    }

    /**
     * Return a copy of this line string with replacement coordinates.
     *
     * The original line string and its points are left unchanged. The copied
     * instance retains its family, dimension, and Spatial Reference Identifier
     * (SRID); the existing point hydrator validates each tuple against that
     * retained context.
     *
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates replacement coordinates
     */
    public function withArrayOfCoordinates(array $coordinates): static
    {
        $lineString = clone $this;
        $lineString->points = [];
        $lineString->addPoints($coordinates);

        return $lineString;
    }

    /**
     * Return a deep copy of this line string with one replacement point.
     *
     * The original line string and all its points remain unchanged. The
     * replacement coordinates are validated by the selected point, which keeps
     * the line string's family, dimension, and Spatial Reference Identifier
     * (SRID) unchanged.
     *
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     *
     * @throws OutOfBoundsException when the line string has no points
     */
    public function withPoint(int $pointIndex, Coordinates $coordinates): static
    {
        $pointIndex = $this->normalizePointIndex($pointIndex);
        $lineString = clone $this;
        $lineString->points = array_map(
            static fn (PointInterface $point): PointInterface => $point->withSrid($point->getSrid()),
            $this->points
        );
        $lineString->points[$pointIndex] = $lineString->points[$pointIndex]->withCoordinates($coordinates);
        LineStringValidation::assertNoConsecutiveDuplicatePoints($lineString);

        return $lineString;
    }

    /**
     * Return a deep copy declared in the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $lineString = parent::withSpatialReference($reference);
        $lineString->points = array_map(
            static fn (PointInterface $point): PointInterface => $point->withSpatialReference($reference),
            $this->points
        );

        return $lineString;
    }

    /**
     * Return a copy of this line string with the given Spatial Reference Identifier (SRID).
     *
     * Every point is copied with the requested SRID so the returned line string
     * remains internally consistent, including when the original contains points
     * with the default SRID.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Add points then enforce the line-string point-sequence invariant.
     *
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface> $points Points or tuples to add
     */
    protected function addPoints(array $points): static
    {
        parent::addPoints($points);
        LineStringValidation::assertNoConsecutiveDuplicatePoints($this);

        return $this;
    }

    /**
     * Normalize a point index according to the line-string accessor convention.
     *
     * @param int $pointIndex point index to normalize
     *
     * @throws OutOfBoundsException when the line string has no points
     */
    private function normalizePointIndex(int $pointIndex): int
    {
        $pointCount = count($this->points);
        if (0 === $pointCount) {
            throw new OutOfBoundsException('The current collection of points is empty.');
        }

        $pointIndex %= $pointCount;

        return $pointIndex < 0 ? $pointCount + $pointIndex : $pointIndex;
    }
}
