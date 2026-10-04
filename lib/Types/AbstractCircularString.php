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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\CircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Shared circular-string storage and immutable validation.
 *
 * @internal
 */
abstract class AbstractCircularString extends AbstractSpatialType implements CircularStringInterface
{
    /** @var PointInterface[] */
    protected array $points = [];

    /**
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface> $points Defining points or tuples
     * @param int|SpatialReference                                                                                            $srid   Spatial reference
     */
    final public function __construct(array $points, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addPoints($points);
    }

    /** @return PointInterface[] */
    public function getElements(): array
    {
        return $this->getPoints();
    }

    /** Return the last defining point, or null for an empty curve. */
    public function getEndPoint(): ?PointInterface
    {
        return $this->isEmpty() ? null : $this->getPoint(-1);
    }

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

    /** Return the first defining point, or null for an empty curve. */
    public function getStartPoint(): ?PointInterface
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
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates Replacement tuples
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
            static fn (PointInterface $item): PointInterface => $item->withSpatialReference($item->getSpatialReference()),
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
            static fn (PointInterface $point): PointInterface => $point->withSpatialReference($reference),
            $this->points
        ), $reference);
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

        if ($point->isEmpty()) {
            throw new InvalidValueException('A circular string cannot contain an empty point.');
        }

        $this->assertSameDimension($point, 'The point dimension is not compatible with the dimension of the current spatial collection.');

        $this->assertSameSpatialReference($point, 'point');

        $this->assertSameFamily($point, 'The point family is not compatible with the family of the current spatial collection.');

        // Compare normalized numeric ordinates, not their PHP integer/float storage types.
        if ([] !== $this->points && $this->points[count($this->points) - 1]->toArray() == $point->toArray()) {
            throw new InvalidValueException('The intermediate point of a circular arc must differ from its endpoints.');
        }

        $this->points[] = $point;

        return $this;
    }

    /**
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface> $points Points or tuples to add
     */
    protected function addPoints(array $points): static
    {
        if ([] !== $points && (count($points) < 3 || 0 === count($points) % 2)) {
            throw new InvalidValueException('A non-empty circular string requires an odd number of at least three points.');
        }

        foreach ($points as $point) {
            if (!is_array($point) && !$point instanceof PointInterface) {
                throw new InvalidValueException('Argument shall contain an array of PointInterface or an array of coordinates.');
            }

            $this->addPoint($point);
        }

        return $this;
    }
}
