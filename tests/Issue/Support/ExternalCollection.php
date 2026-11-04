<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Issue\Support;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;

/** Third-party immutable collection from the independent #77 reproduction.
 * @internal
 */
final readonly class ExternalCollection implements ImmutableCollectionInterface
{
    /**
     * @param GeometryCollectionImmutable $inner Wrapped immutable collection
     */
    public function __construct(private GeometryCollectionImmutable $inner)
    {
    }

    /** Return an independent copy without propagating references. */
    public function copy(): static
    {
        return new self($this->inner->copy());
    }

    /** Return the coordinate layout. */
    public function getDimension(): CoordinateDimensionEnum
    {
        return $this->inner->getDimension();
    }

    /** @return \LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface[] */
    public function getElements(): array
    {
        return $this->inner->getElements();
    }

    /** Return the coordinate family. */
    public function getFamily(): SpatialModelEnum
    {
        return $this->inner->getFamily();
    }

    /** Return the declared reference. */
    public function getSpatialReference(): SpatialReference
    {
        return $this->inner->getSpatialReference();
    }

    /** Return the declared reference identifier. */
    public function getSrid(): int
    {
        return $this->inner->getSrid();
    }

    /** Return the spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return $this->inner->getType();
    }

    /** Whether a measure ordinate is supported. */
    public function hasM(): bool
    {
        return $this->inner->hasM();
    }

    /**
     * Compare layouts without inspecting coordinates.
     *
     * @param SpatialInterface $spatial Value to compare
     */
    public function hasSameDimension(SpatialInterface $spatial): bool
    {
        return $this->inner->hasSameDimension($spatial);
    }

    /** Whether an elevation ordinate is supported. */
    public function hasZ(): bool
    {
        return $this->inner->hasZ();
    }

    /** @return (float|int|(float|int|string|null)[]|(float|int|string|null)[][]|(float|int|string|null)[][][]|string|null)[][] */
    public function jsonSerialize(): array
    {
        return $this->inner->toArray();
    }

    /** @return (float|int|(float|int|string|null)[]|(float|int|string|null)[][]|(float|int|string|null)[][][]|string|null)[][] */
    public function toArray(): array
    {
        return $this->inner->toArray();
    }

    /**
     * Explicit reference replacement propagates to all descendants.
     *
     * @param SpatialReference $reference New declared reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        return new self($this->inner->withSpatialReference($reference));
    }

    /**
     * Explicit identifier replacement propagates to all descendants.
     *
     * @param int $srid New declared reference identifier
     */
    public function withSrid(int $srid): static
    {
        return new self($this->inner->withSrid($srid));
    }
}
