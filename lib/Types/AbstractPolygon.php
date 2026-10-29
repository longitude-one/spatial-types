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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Mutable ordered rings without geometric validation.
 * @internal
 */
abstract class AbstractPolygon extends AbstractSpatialType implements MutablePolygonInterface
{
    protected SpatialModelEnum $family;

    /** @var list<MutableLineStringInterface> */
    protected array $rings = [];

    /**
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|MutablePointInterface>|MutableLineStringInterface> $rings  Ordered rings or point sequences
     * @param int|SpatialReference                                                                                                                                               $srid   Declared reference
     * @param SpatialModelEnum                                                                                                                                                   $family Declared family
     */
    final public function __construct(array $rings, int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)
    {
        $this->family = $family;
        $this->initializeSpatialReference($srid);
        $this->rings = $this->hydrateRings($rings);
    }

    /** @param MutableLineStringInterface $ring Ring retained without cloning */
    public function addRing(MutableLineStringInterface $ring): static
    {
        $this->checkRingPoints($ring);
        $this->rings[] = $ring;

        return $this;
    }

    /** @return list<MutableLineStringInterface> */
    public function getElements(): array
    {
        return $this->rings;
    }

    /** Return the declared family. */
    public function getFamily(): SpatialModelEnum
    {
        return $this->family;
    }

    /** @param int $index Existing zero-based ring index */
    public function getRing(int $index): MutableLineStringInterface
    {
        $this->requireRingIndex($index);

        return $this->rings[$index];
    }

    /** @return list<MutableLineStringInterface> */
    public function getRings(): array
    {
        return $this->rings;
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POLYGON;
    }

    /**
     * @param int                        $index Zero-based insertion index, up to the ring count
     * @param MutableLineStringInterface $ring  Ring retained without cloning
     */
    public function insertRing(int $index, MutableLineStringInterface $ring): static
    {
        if ($index < 0 || $index > count($this->rings)) {
            throw new OutOfBoundsException(sprintf('Ring index %d is outside the insertion range.', $index));
        }
        $this->checkRingPoints($ring);
        array_splice($this->rings, $index, 0, [$ring]);

        return $this;
    }

    /** Whether there are no rings. */
    public function isEmpty(): bool
    {
        return [] === $this->rings;
    }

    /** @param int $index Existing zero-based ring index */
    public function removeRing(int $index): static
    {
        $this->requireRingIndex($index);
        array_splice($this->rings, $index, 1);

        return $this;
    }

    /** @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates Complete replacement ring coordinates */
    public function setArrayOfCoordinates(array $coordinates): static
    {
        $this->rings = $this->hydrateRings($coordinates);

        return $this;
    }

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update rings and their points
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->rings as $ring) {
                $ring->setFamily($family);
            }
        }
        $this->family = $family;

        return $this;
    }

    /**
     * @param int                   $ringIndex  Existing zero-based ring index
     * @param int                   $pointIndex Existing zero-based point index within the ring
     * @param MutablePointInterface $point      Point retained without cloning
     */
    public function setPoint(int $ringIndex, int $pointIndex, MutablePointInterface $point): static
    {
        $this->getRing($ringIndex)->setPoint($pointIndex, $point);

        return $this;
    }

    /**
     * @param int                        $index Existing zero-based ring index
     * @param MutableLineStringInterface $ring  Ring retained without cloning
     */
    public function setRing(int $index, MutableLineStringInterface $ring): static
    {
        $this->requireRingIndex($index);
        $this->checkRingPoints($ring);
        array_splice($this->rings, $index, 1, [$ring]);

        return $this;
    }

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update rings and their points
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->rings as $ring) {
                $ring->setSpatialReference($reference);
            }
        }
        $this->spatialReference = $reference;

        return $this;
    }

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update rings and their points
     */
    public function setSrid(int $srid, bool $propagate = true): static
    {
        return $this->setSpatialReference(SpatialReference::fromSrid($srid), $propagate);
    }

    /** @return (null|float|int)[][][] */
    public function toArray(): array
    {
        return array_map(static fn (MutableLineStringInterface $ring): array => $ring->toArray(), $this->rings);
    }

    /** @param MutableLineStringInterface $ring Ring whose descendants must be mutable */
    private function checkRingPoints(MutableLineStringInterface $ring): void
    {
        foreach ($ring->getPoints() as $point) {
            if (!$point instanceof MutablePointInterface) {
                throw new InvalidValueException('Mutable polygon rings require mutable point components.');
            }
        }
    }

    /**
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|MutablePointInterface>|MutableLineStringInterface> $rings Ordered rings or point sequences
     *
     * @return list<MutableLineStringInterface>
     */
    private function hydrateRings(array $rings): array
    {
        $hydrated = [];
        foreach ($rings as $ring) {
            if (is_array($ring)) {
                $ring = match ($this->getDimension()) {
                    CoordinateDimensionEnum::XY => new Dimension2\LineString($ring, $this->spatialReference, $this->family),
                    CoordinateDimensionEnum::XYZ => new Dimension3z\LineString($ring, $this->spatialReference, $this->family),
                    CoordinateDimensionEnum::XYM => new Dimension3m\LineString($ring, $this->spatialReference, $this->family),
                    CoordinateDimensionEnum::XYZM => new Dimension4zm\LineString($ring, $this->spatialReference, $this->family),
                };
            }
            if (!$ring instanceof MutableLineStringInterface) {
                throw new InvalidValueException('Mutable polygons require mutable line-string rings or point sequences.');
            }
            $this->checkRingPoints($ring);
            $hydrated[] = $ring;
        }

        return $hydrated;
    }

    /** @param int $index Existing zero-based ring index */
    private function requireRingIndex(int $index): void
    {
        if ($index < 0 || $index >= count($this->rings)) {
            throw new OutOfBoundsException(sprintf('Ring index %d does not address an existing ring.', $index));
        }
    }
}
