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
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface> $coordinates Point tuples
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
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $coordinates Point tuple
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
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface>|ImmutableLineStringInterface> $rings Polygon rings
     */
    final protected function createPolygonFromCoordinates(array $rings): ImmutablePolygonInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPolygonFromIndexedArray(
            $rings,
            new SpatialContext($this->getSpatialReference(), $this->getFamily(), $this->getDimension())
        );
    }
}
