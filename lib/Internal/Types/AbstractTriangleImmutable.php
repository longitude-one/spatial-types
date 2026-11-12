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

use LongitudeOne\SpatialTypes\Interfaces\ImmutableTriangleInterface;

/**
 * Shared polygon representation for SQL/MM triangles.
 *
 * @internal
 */
abstract class AbstractTriangleImmutable extends AbstractPolygonImmutable implements ImmutableTriangleInterface
{
}
