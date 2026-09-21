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

namespace LongitudeOne\SpatialTypes\Types\Collection;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractSpatialType;

/**
 * Common point collection for point-defined spatial values.
 *
 * ISO/IEC 13249-3 exposes the point collection of a line string and defines a
 * multi-point as a collection of points. This is deliberately not named a
 * sequence: a multi-point has no ordering semantics.
 *
 * @internal
 */
abstract class AbstractPointCollection extends AbstractSpatialType
{
    /** @var PointInterface[] */
    protected array $points = [];

    /**
     * Return a point using the collection index convention.
     *
     * @param int $index Requested index
     */
    public function getPoint(int $index): PointInterface
    {
        if ([] === $this->points) {
            throw new OutOfBoundsException('The current collection of points is empty.');
        }

        $index %= count($this->points);

        return $this->points[$index < 0 ? count($this->points) + $index : $index];
    }

    /** @return PointInterface[] */
    public function getPoints(): array
    {
        return $this->points;
    }

    /** Whether this point collection is empty. */
    public function isEmpty(): bool
    {
        return [] === $this->points;
    }

    /** Whether no two distinct points are equal. */
    public function isSimple(): bool
    {
        foreach ($this->points as $point) {
            foreach ($this->points as $otherPoint) {
                if ($point !== $otherPoint && $point->equalsTo($otherPoint)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return (float|int)[][]
     */
    public function toArray(): array
    {
        return array_map(
            static fn (PointInterface $point): array => $point->toArray(),
            $this->points
        );
    }

    /**
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface $point Point or coordinate tuple to add
     *
     * @throws InvalidDimensionException When the point dimension differs
     * @throws InvalidFamilyException    When the point family differs
     * @throws InvalidValueException     When coordinates are invalid
     * @throws MissingValueException     When a coordinate is missing
     */
    protected function addPoint(array|PointInterface $point): static
    {
        if (is_array($point)) {
            $point = $this->createPointFromCoordinates($point);
        }

        $this->assertSameDimension($point, 'The point dimension is not compatible with the dimension of the current spatial collection.');

        $this->assertSameSpatialReference($point, 'point');

        $this->assertSameFamily($point, 'The point family is not compatible with the family of the current spatial collection.');

        $this->points[] = $point;

        return $this;
    }

    /**
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface> $points Points or tuples to add
     */
    protected function addPoints(array $points): static
    {
        foreach ($points as $point) {
            if (!is_array($point) && !$point instanceof PointInterface) {
                throw new InvalidValueException('Argument shall contain an array of PointInterface or an array of coordinates.');
            }

            $this->addPoint($point);
        }

        return $this;
    }
}
