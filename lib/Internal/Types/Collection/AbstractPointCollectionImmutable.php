<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Internal\Types\Collection;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable;
use LongitudeOne\SpatialTypes\Internal\Types\ImmutableComponentGuard;

/**
 * Common point collection for point-defined spatial values.
 *
 * ISO/IEC 13249-3 exposes the point collection of a line string and defines a
 * multi-point as a collection of points. This is deliberately not named a
 * sequence: a multi-point has no ordering semantics.
 *
 * @internal
 */
abstract class AbstractPointCollectionImmutable extends AbstractSpatialTypeImmutable
{
    /** @var ImmutablePointInterface[] */
    protected array $points = [];

    /** Copy every descendant while retaining its individual spatial reference. */
    public function copy(): static
    {
        $copy = clone $this;
        $copy->points = array_map(
            static fn (ImmutablePointInterface $component): ImmutablePointInterface => self::copyComponent($component),
            $this->points
        );

        return $copy;
    }

    /**
     * Return a point using the collection index convention.
     *
     * @param int $index Requested index
     */
    public function getPoint(int $index): ImmutablePointInterface
    {
        if ([] === $this->points) {
            throw new OutOfBoundsException('The current collection of points is empty.');
        }

        $index %= \count($this->points);

        return $this->points[$index < 0 ? \count($this->points) + $index : $index];
    }

    /** @return ImmutablePointInterface[] */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * @return (float|int|null)[][]
     */
    public function toArray(): array
    {
        return array_map(
            static fn (ImmutablePointInterface $point): array => $point->toArray(),
            $this->points
        );
    }

    /**
     * @param array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}|ImmutablePointInterface $point Point or coordinate tuple to add
     *
     * @throws InvalidDimensionException When a coordinate tuple has an incompatible layout
     * @throws InvalidValueException     When coordinates are invalid
     * @throws MissingValueException     When a coordinate is missing
     */
    protected function addPoint(array|ImmutablePointInterface $point): static
    {
        if (\is_array($point)) {
            $point = $this->createPointFromCoordinates($point);
        }

        ImmutableComponentGuard::check($point);
        $this->points[] = $point;

        return $this;
    }

    /**
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}|ImmutablePointInterface> $points Points or tuples to add
     */
    protected function addPoints(array $points): static
    {
        foreach ($points as $point) {
            if (!\is_array($point) && !$point instanceof ImmutablePointInterface) {
                throw new InvalidValueException('Argument shall contain an array of ImmutablePointInterface or an array of coordinates.');
            }

            $this->addPoint($point);
        }

        return $this;
    }
}
