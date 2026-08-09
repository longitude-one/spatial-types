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

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Factory\Internal\LineStringFactoryInterface;
use LongitudeOne\SpatialTypes\Factory\Internal\PointFactoryInterface;
use LongitudeOne\SpatialTypes\Factory\Internal\PolygonFactoryInterface;

/**
 * Set of constructors for one spatial family.
 */
final readonly class FamilyFactories
{
    /**
     * @param FamilyEnum                 $family            spatial family
     * @param PointFactoryInterface      $pointFactory      point constructor
     * @param LineStringFactoryInterface $lineStringFactory line string constructor
     * @param PolygonFactoryInterface    $polygonFactory    polygon constructor
     */
    public function __construct(
        public FamilyEnum $family,
        public PointFactoryInterface $pointFactory,
        public LineStringFactoryInterface $lineStringFactory,
        public PolygonFactoryInterface $polygonFactory
    ) {
    }
}
