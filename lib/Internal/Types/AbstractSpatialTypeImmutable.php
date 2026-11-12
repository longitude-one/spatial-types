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

use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\DefaultSpatialFactoryFactory;
use LongitudeOne\SpatialTypes\Internal\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Common immutable reference replacement.
 * @internal
 */
abstract class AbstractSpatialTypeImmutable extends AbstractSpatialType implements ImmutableSpatialInterface
{
    /**
     * Copy a component without replacing its declared reference.
     *
     * @template T of ImmutableSpatialInterface
     *
     * @param ImmutableSpatialInterface $component Component to copy
     *
     * @phpstan-param T $component
     *
     * @phpstan-return T
     */
    final protected static function copyComponent(ImmutableSpatialInterface $component): ImmutableSpatialInterface
    {
        return $component->copy();
    }

    /** Return an independent value with its original declared context. */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Return a copy declared with the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $spatial = clone $this;
        $spatial->spatialReference = $reference;

        return $spatial;
    }

    /**
     * Return a copy of this spatial object with the given Spatial Reference Identifier (SRID).
     *
     * Aggregate spatial types override this method to copy their contained
     * elements with the same SRID.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Hydrate a line-string tuple collection in this spatial value's context.
     *
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}|ImmutablePointInterface> $coordinates Point tuples
     */
    final protected function createLineStringFromCoordinates(array $coordinates): ImmutableLineStringInterface
    {
        return DefaultSpatialFactoryFactory::create()->createLineStringFromIndexedArray(
            $coordinates,
            new SpatialContext($this->getSpatialReference(), $this->getFamily(), $this->getDimension())
        );
    }

    /**
     * Hydrate a point tuple in this spatial value's complete context.
     *
     * @param array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null} $coordinates Point tuple
     */
    final protected function createPointFromCoordinates(array $coordinates): ImmutablePointInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPointFromIndexedArray(
            $coordinates,
            new SpatialContext($this->getSpatialReference(), $this->getFamily(), $this->getDimension())
        );
    }

    /**
     * Hydrate polygon rings in this spatial value's context.
     *
     * @param array<array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}|ImmutablePointInterface>|ImmutableLineStringInterface> $rings Polygon rings
     */
    final protected function createPolygonFromCoordinates(array $rings): ImmutablePolygonInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPolygonFromIndexedArray(
            $rings,
            new SpatialContext($this->getSpatialReference(), $this->getFamily(), $this->getDimension())
        );
    }
}
