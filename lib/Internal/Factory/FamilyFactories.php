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

namespace LongitudeOne\SpatialTypes\Internal\Factory;

use LongitudeOne\Core\Enum\SpatialModelEnum;

/**
 * Set of constructors for one spatial family.
 *
 * @internal this configuration object supports the internal factory pipeline
 */
final readonly class FamilyFactories
{
    /**
     * @param SpatialModelEnum           $family            spatial family
     * @param PointFactoryInterface      $pointFactory      point constructor
     * @param LineStringFactoryInterface $lineStringFactory line string constructor
     * @param PolygonFactoryInterface    $polygonFactory    polygon constructor
     */
    public function __construct(
        public SpatialModelEnum $family,
        public PointFactoryInterface $pointFactory,
        public LineStringFactoryInterface $lineStringFactory,
        public PolygonFactoryInterface $polygonFactory,
    ) {
    }
}
