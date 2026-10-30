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
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\Collection\AbstractPointCollectionImmutable;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract LineString class.
 *
 * @internal this class provides common behaviour for geometry and geography line strings
 */
abstract class AbstractLineStringImmutable extends AbstractPointCollectionImmutable implements ImmutableLineStringInterface
{
    /**
     * Get the elements of this line string.
     *
     * @return ImmutablePointInterface[]
     */
    public function getElements(): array
    {
        return $this->getPoints();
    }

    /** Return the last defining point, or null for an empty curve. */
    public function getEndPoint(): ?ImmutablePointInterface
    {
        return $this->isEmpty() ? null : $this->getPoint(-1);
    }

    /** Return the first defining point, or null for an empty curve. */
    public function getStartPoint(): ?ImmutablePointInterface
    {
        return $this->isEmpty() ? null : $this->getPoint(0);
    }

    /**
     * This line string is closed when the first point is the same as the last point.
     */
    public function isClosed(): bool
    {
        return count($this->points) >= 2 && $this->points[0]->equalsTo($this->points[count($this->points) - 1]);
    }

    /**
     * Is this line string a line?
     *
     * A line is composed of exactly two points.
     */
    public function isLine(): bool
    {
        return 2 === count($this->points);
    }

    /** Detect proper interior crossings of complete, compatible XY segments. */
    public function isSimple(): bool
    {
        if (!parent::isSimple()) {
            return false;
        }

        $positions = $this->completeXyPositions();

        return null === $positions || !$this->hasProperCrossing($positions);
    }

    /**
     * Return an array representation of the line string.
     *
     * @return (null|float|int)[][]
     */
    public function toArray(): array
    {
        $points = $this->getPoints();

        return array_map(
            static fn (ImmutablePointInterface $point) => $point->toArray(),
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
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates replacement coordinates
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
            static fn (ImmutablePointInterface $point): ImmutablePointInterface => $point->withSrid($point->getSrid()),
            $this->points
        );
        $lineString->points[$pointIndex] = $lineString->points[$pointIndex]->withCoordinates($coordinates);

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
            static fn (ImmutablePointInterface $point): ImmutablePointInterface => $point->withSpatialReference($reference),
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
     * Keep unresolved partial-position, dimensional and context semantics unchanged.
     *
     * @return null|list<array{float|int, float|int}>
     */
    private function completeXyPositions(): ?array
    {
        if (CoordinateDimensionEnum::XY !== $this->getDimension()) {
            return null;
        }

        $positions = [];
        foreach ($this->points as $point) {
            $x = $point->getX();
            $y = $point->getY();
            if (null === $x || null === $y || !is_finite($x) || !is_finite($y)) {
                return null;
            }
            if ($point->getDimension() !== $this->getDimension() || $point->getFamily() !== $this->getFamily()
                || !$point->getSpatialReference()->equals($this->getSpatialReference())) {
                return null;
            }

            $positions[] = [$x, $y];
        }

        return $positions;
    }

    /** @param list<array{float|int, float|int}> $positions Complete XY positions */
    private function hasProperCrossing(array $positions): bool
    {
        $segmentCount = count($positions) - 1;
        for ($first = 0; $first < $segmentCount; ++$first) {
            for ($second = $first + 2; $second < $segmentCount; ++$second) {
                if ($this->segmentsCross($positions[$first], $positions[$first + 1], $positions[$second], $positions[$second + 1])) {
                    return true;
                }
            }
        }

        return false;
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

    /**
     * @param float|int $first  First orientation
     * @param float|int $second Second orientation
     */
    private function oppositeSides(float|int $first, float|int $second): bool
    {
        return ($first < 0 && $second > 0) || ($first > 0 && $second < 0);
    }

    /**
     * @param array{float|int, float|int} $start      First segment start
     * @param array{float|int, float|int} $end        First segment end
     * @param array{float|int, float|int} $otherStart Second segment start
     * @param array{float|int, float|int} $otherEnd   Second segment end
     */
    private function segmentsCross(array $start, array $end, array $otherStart, array $otherEnd): bool
    {
        return $this->oppositeSides($this->side($start, $end, $otherStart), $this->side($start, $end, $otherEnd))
            && $this->oppositeSides($this->side($otherStart, $otherEnd, $start), $this->side($otherStart, $otherEnd, $end));
    }

    /**
     * @param array{float|int, float|int} $start Segment start
     * @param array{float|int, float|int} $end   Segment end
     * @param array{float|int, float|int} $point Position being compared
     */
    private function side(array $start, array $end, array $point): float|int
    {
        return ($end[0] - $start[0]) * ($point[1] - $start[1])
            - ($end[1] - $start[1]) * ($point[0] - $start[0]);
    }
}
