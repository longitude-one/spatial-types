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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\ImmutableComponentGuard;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Shared circular-string storage and immutable replacement.
 *
 * @internal
 */
abstract class AbstractCircularStringImmutable extends AbstractSpatialTypeImmutable implements ImmutableCircularStringInterface
{
    /** @var ImmutablePointInterface[] */
    protected array $points = [];

    /**
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface> $points Defining points or tuples
     * @param int|SpatialReference                                                                                                               $srid   Spatial reference
     */
    final public function __construct(array $points, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addPoints($points);
    }

    /** @return ImmutablePointInterface[] */
    public function getElements(): array
    {
        return $this->getPoints();
    }

    /** Return the last defining point, or null for an empty curve. */
    public function getEndPoint(): ?ImmutablePointInterface
    {
        return $this->isEmpty() ? null : $this->getPoint(-1);
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

        $index %= count($this->points);

        return $this->points[$index < 0 ? count($this->points) + $index : $index];
    }

    /** @return ImmutablePointInterface[] */
    public function getPoints(): array
    {
        return $this->points;
    }

    /** Return the first defining point, or null for an empty curve. */
    public function getStartPoint(): ?ImmutablePointInterface
    {
        return $this->isEmpty() ? null : $this->getPoint(0);
    }

    /** Return the circular-string type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::CIRCULARSTRING;
    }

    /** Whether the first and last defining points are equal. */
    public function isClosed(): bool
    {
        return !$this->isEmpty() && $this->getPoint(0)->equalsTo($this->getPoint(-1));
    }

    /** Whether this point collection is empty. */
    public function isEmpty(): bool
    {
        return [] === $this->points;
    }

    /**
     * @return (null|float|int)[][]
     */
    public function toArray(): array
    {
        return array_map(
            static fn (ImmutablePointInterface $point): array => $point->toArray(),
            $this->points
        );
    }

    /**
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates Replacement tuples
     */
    public function withArrayOfCoordinates(array $coordinates): static
    {
        return new static($coordinates, $this->getSpatialReference());
    }

    /**
     * @param int         $pointIndex  Index, with negative values counting from the end
     * @param Coordinates $coordinates Replacement coordinates
     */
    public function withPoint(int $pointIndex, Coordinates $coordinates): static
    {
        $point = $this->getPoint($pointIndex);
        $pointIndex %= count($this->points);
        if ($pointIndex < 0) {
            $pointIndex += count($this->points);
        }
        $points = array_map(
            static fn (ImmutablePointInterface $item): ImmutablePointInterface => $item->withSpatialReference($item->getSpatialReference()),
            $this->points
        );
        $points[$pointIndex] = $point->withCoordinates($coordinates);

        return new static($points, $this->getSpatialReference());
    }

    /**
     * @param SpatialReference $reference New declared reference, without transformation
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        return new static(array_map(
            static fn (ImmutablePointInterface $point): ImmutablePointInterface => $point->withSpatialReference($reference),
            $this->points
        ), $reference);
    }

    /**
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface $point Point or coordinate tuple to add
     *
     * @throws InvalidDimensionException When a coordinate tuple has an incompatible layout
     * @throws InvalidValueException     When coordinates are invalid
     * @throws MissingValueException     When a coordinate is missing
     */
    protected function addPoint(array|ImmutablePointInterface $point): static
    {
        if (is_array($point)) {
            $point = $this->createPointFromCoordinates($point);
        }

        ImmutableComponentGuard::check($point);
        $this->points[] = $point;

        return $this;
    }

    /**
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface> $points Points or tuples to add
     */
    protected function addPoints(array $points): static
    {
        foreach ($points as $point) {
            if (!is_array($point) && !$point instanceof ImmutablePointInterface) {
                throw new InvalidValueException('Argument shall contain an array of ImmutablePointInterface or an array of coordinates.');
            }

            $this->addPoint($point);
        }

        return $this;
    }
}
