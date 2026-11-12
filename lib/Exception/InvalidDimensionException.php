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

namespace LongitudeOne\SpatialTypes\Exception;

/**
 * This exception is thrown when mixing objects with non-compatible dimensions.
 *
 * For example, when you try to add a PointM in a MultiPointZ object, this exception is thrown.
 */
class InvalidDimensionException extends \Exception implements SpatialTypeExceptionInterface
{
}
