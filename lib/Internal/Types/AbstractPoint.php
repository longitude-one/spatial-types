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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Common in-place point editing, without geometric validation.
 * @internal
 */
abstract class AbstractPoint extends AbstractSpatialType implements MutablePointInterface
{
    use PointBehaviorTrait;

    protected SpatialModelEnum $family = SpatialModelEnum::GEOMETRY;

    protected float|int|null $m = null;

    protected float|int|null $z = null;

    /** Return the family. */
    public function getFamily(): SpatialModelEnum
    {
        return $this->family;
    }

    /** Return the m. */
    public function getM(): float|int|null
    {
        if (!$this->hasM()) {
            throw BadMethodCallException::create(__METHOD__, $this->getDimension());
        }

        return $this->m;
    }

    /** Return the type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::POINT;
    }

    /** Return the z. */
    public function getZ(): float|int|null
    {
        if (!$this->hasZ()) {
            throw BadMethodCallException::create(__METHOD__, $this->getDimension());
        }

        return $this->z;
    }

    /** @param SpatialModelEnum $family Declared family */
    public function setFamily(SpatialModelEnum $family): static
    {
        $this->family = $family;

        return $this;
    }

    /** @param float|int|null $m Measure ordinate */
    public function setM(float|int|null $m): static
    {
        if (!$this->hasM()) {
            throw BadMethodCallException::create(__METHOD__, $this->getDimension());
        }

        $this->m = $m;
        $this->refreshEmptyState();

        return $this;
    }

    /** @param SpatialReference $reference Declared reference */
    public function setSpatialReference(SpatialReference $reference): static
    {
        $this->spatialReference = $reference;

        return $this;
    }

    /** @param int $srid Declared reference identifier */
    public function setSrid(int $srid): static
    {
        return $this->setSpatialReference(SpatialReference::fromSrid($srid));
    }

    /** @param float|int|string|null $x X ordinate, using constructor parsing */
    public function setX(float|int|string|null $x): static
    {
        $this->initializeX($x);
        $this->refreshEmptyState();

        return $this;
    }

    /** @param float|int|string|null $y Y ordinate, using constructor parsing */
    public function setY(float|int|string|null $y): static
    {
        $this->initializeY($y);
        $this->refreshEmptyState();

        return $this;
    }

    /** @param float|int|null $z Elevation ordinate */
    public function setZ(float|int|null $z): static
    {
        if (!$this->hasZ()) {
            throw BadMethodCallException::create(__METHOD__, $this->getDimension());
        }

        $this->z = $z;
        $this->refreshEmptyState();

        return $this;
    }

    /** @return (float|int|null)[] */
    public function toArray(): array
    {
        return $this->getCoordinates()?->toArray() ?? [];
    }

    /** Recompute EMPTY after a successful ordinate edit. */
    private function refreshEmptyState(): void
    {
        $this->hasOnlyNullCoordinates($this->x, $this->y, $this->z, $this->m);
    }
}
