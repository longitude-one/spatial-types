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

namespace LongitudeOne\SpatialTypes\Internal;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/** @internal Methods of the one public SpatialBuilder class. */
trait BuilderInternalsTrait
{
    /**
     * Add component.
     *
     * @param SpatialInterface $component component value
     */
    private function addComponent(SpatialInterface $component): self
    {
        $candidate = [...$this->components, $component];
        $aggregate = $this->aggregate;
        if (null === $aggregate) {
            throw new InvalidValueException('Select an aggregate before adding components.');
        }
        $this->createType($aggregate, [$candidate]);
        $this->components = $candidate;

        return $this;
    }

    /**
     * Create type.
     *
     * @template T of SpatialInterface
     *
     * @param string $type      type value
     * @param array  $arguments arguments value
     * @param string $expected  expected value
     *
     * @phpstan-param array<mixed> $arguments
     * @phpstan-param class-string<T> $expected
     * @phpstan-param 'CircularString'|'Collection'|'CompoundCurve'|'LineString'|'MultiLineString'|'MultiPoint'|'MultiPolygon'|'Point'|'Polygon'|'PolyhedralSurface'|'Triangle' $type
     * @phpstan-param list<mixed> $arguments
     *
     * @phpstan-return T
     */
    private function createType(string $type, array $arguments, string $expected = SpatialInterface::class): SpatialInterface
    {
        $created = BuilderTypes::create($type, $arguments, $this->coordinateDimension, $this->spatialFamily, $this->reference, $this->isMutable);
        if (!$created instanceof $expected) {
            throw new InvalidValueException('The selected constructor returned an unexpected spatial type.');
        }

        return $created;
    }

    /**
     * Require aggregate.
     *
     * @param array $allowed allowed value
     *
     * @phpstan-param list<string> $allowed
     */
    private function requireAggregate(array $allowed): void
    {
        if (!in_array($this->aggregate, $allowed, true)) {
            throw new InvalidValueException('This addition is unavailable for the active aggregate.');
        }
    }

    /**
     * Select.
     *
     * @param string $type type value
     *
     * @phpstan-param 'CircularString'|'Collection'|'CompoundCurve'|'LineString'|'MultiLineString'|'MultiPoint'|'MultiPolygon'|'Polygon'|'PolyhedralSurface'|'Triangle' $type
     */
    private function select(string $type): self
    {
        if ('PolyhedralSurface' === $type && !in_array($this->coordinateDimension, [CoordinateDimensionEnum::XYZ, CoordinateDimensionEnum::XYZM], true)) {
            throw new InvalidDimensionException('PolyhedralSurface is supported only for XYZ and XYZM.');
        }
        $copy = clone $this;
        $copy->aggregate = $type;
        $copy->components = [];

        return $copy;
    }
}
