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

use LongitudeOne\SpatialTypes\Interfaces\CircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\CompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;

/** @internal Methods of the one public SpatialBuilder class. */
trait BuilderDirectCreationTrait
{
    /** @param array<mixed> $points */
    /**
     * Create circular string.
     *
     * @param array $points points value
     *
     * @phpstan-param array<mixed> $points
     */
    public function createCircularString(array $points): CircularStringInterface
    {
        return $this->createType('CircularString', [$points], CircularStringInterface::class);
    }

    /** @param array<mixed> $elements */
    /**
     * Create collection.
     *
     * @param array $elements elements value
     *
     * @phpstan-param array<mixed> $elements
     */
    public function createCollection(array $elements = []): CollectionInterface
    {
        return $this->createType('Collection', [$elements], CollectionInterface::class);
    }

    /** @param array<mixed> $curves */
    /**
     * Create compound curve.
     *
     * @param array $curves curves value
     *
     * @phpstan-param array<mixed> $curves
     */
    public function createCompoundCurve(array $curves = []): CompoundCurveInterface
    {
        return $this->createType('CompoundCurve', [$curves], CompoundCurveInterface::class);
    }

    /** @param array<mixed> $points */
    /**
     * Create line string.
     *
     * @param array $points points value
     *
     * @phpstan-param array<mixed> $points
     */
    public function createLineString(array $points): LineStringInterface
    {
        return $this->createType('LineString', [$points], LineStringInterface::class);
    }

    /** @param array<mixed> $lineStrings */
    /**
     * Create multi line string.
     *
     * @param array $lineStrings lineStrings value
     *
     * @phpstan-param array<mixed> $lineStrings
     */
    public function createMultiLineString(array $lineStrings): MultiLineStringInterface
    {
        return $this->createType('MultiLineString', [$lineStrings], MultiLineStringInterface::class);
    }

    /** @param array<mixed> $points */
    /**
     * Create multi point.
     *
     * @param array $points points value
     *
     * @phpstan-param array<mixed> $points
     */
    public function createMultiPoint(array $points): MultiPointInterface
    {
        return $this->createType('MultiPoint', [$points], MultiPointInterface::class);
    }

    // @param array<mixed> $polygons
}
