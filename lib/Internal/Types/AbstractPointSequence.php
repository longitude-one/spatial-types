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

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\MutableCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\MutablePointHydrator;

/** Shared mutable ordered-point storage, reads and declared context.
 * @internal
 */
abstract class AbstractPointSequence extends AbstractSpatialType implements MutableCurveInterface
{
    protected SpatialModelEnum $family;

    /** @var list<MutablePointInterface> */
    protected array $points = [];

    /**
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|MutablePointInterface> $points Mutable points or tuples
     * @param int|SpatialReference                                                                                                             $srid   Declared reference
     * @param SpatialModelEnum                                                                                                                 $family Declared family
     */
    final public function __construct(array $points, int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)
    {
        $this->family = $family;
        $this->initializeSpatialReference($srid);
        $this->points = $this->hydratePoints($points);
    }

    /** @return MutablePointInterface[] */
    public function getElements(): array
    {
        return $this->points;
    }

    /** Return the last defining point, or null for EMPTY. */
    public function getEndPoint(): ?MutablePointInterface
    {
        return $this->isEmpty() ? null : $this->points[count($this->points) - 1];
    }

    /** Return the declared family. */
    public function getFamily(): SpatialModelEnum
    {
        return $this->family;
    }

    /** @param int $index Existing zero-based point index */
    public function getPoint(int $index): MutablePointInterface
    {
        $this->requirePointIndex($index);

        return $this->points[$index];
    }

    /** @return MutablePointInterface[] */
    public function getPoints(): array
    {
        return $this->points;
    }

    /** Return the first defining point, or null for EMPTY. */
    public function getStartPoint(): ?MutablePointInterface
    {
        return $this->points[0] ?? null;
    }

    /** Whether this sequence has equal endpoints. */
    public function isClosed(): bool
    {
        return !$this->isEmpty() && $this->points[0]->equalsTo($this->points[count($this->points) - 1]);
    }

    /** Whether there are no defining points. */
    public function isEmpty(): bool
    {
        return [] === $this->points;
    }

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update every contained point
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->points as $point) {
                $point->setFamily($family);
            }
        }

        $this->family = $family;

        return $this;
    }

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update every contained point
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->points as $point) {
                $point->setSpatialReference($reference);
            }
        }

        $this->spatialReference = $reference;

        return $this;
    }

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update every contained point
     */
    public function setSrid(int $srid, bool $propagate = true): static
    {
        return $this->setSpatialReference(SpatialReference::fromSrid($srid), $propagate);
    }

    /** @return (null|float|int)[][] */
    public function toArray(): array
    {
        return array_map(static fn (MutablePointInterface $point): array => $point->toArray(), $this->points);
    }

    /**
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|MutablePointInterface> $points Mutable points or tuples
     *
     * @return list<MutablePointInterface>
     */
    final protected function hydratePoints(array $points): array
    {
        $hydrated = [];
        $context = new SpatialContext($this->getSpatialReference(), $this->family, $this->getDimension());
        foreach ($points as $point) {
            if (is_array($point)) {
                $point = MutablePointHydrator::hydrate($point, $context);
            }

            if (!$point instanceof MutablePointInterface) {
                throw new InvalidValueException('Mutable point sequences accept only mutable points or coordinate tuples.');
            }

            $hydrated[] = $point;
        }

        return $hydrated;
    }

    /** @param int $index Existing zero-based point index */
    final protected function requirePointIndex(int $index): void
    {
        if ($index < 0 || $index >= count($this->points)) {
            throw new OutOfBoundsException(sprintf('Point index %d does not address an existing point.', $index));
        }
    }
}
