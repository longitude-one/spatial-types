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

namespace LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geometry;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\Internal\RepresentationFactory;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection;

/** Creates Dimension4zm Geometry GeometryCollection objects in the shared mode. */
final class GeometryCollectionFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param int|SpatialReference                                     $srid     Declared reference
     * @param array<ImmutableSpatialInterface|MutableSpatialInterface> $elements Spatial components in the selected representation
     */
    public static function create(int|SpatialReference $srid = 0, array $elements = []): GeometryCollectionImmutable|SpatialCollection
    {
        return RepresentationFactory::create(
            GeometryCollectionImmutable::class,
            SpatialCollection::class,
            [$srid, $elements],
            [$elements, $srid, SpatialModelEnum::GEOMETRY]
        );
    }
}
