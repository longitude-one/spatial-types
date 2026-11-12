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
 * This exception is thrown when a developer tries to get an instance point of an empty collection.
 *
 * For example, when you try to get the first point of an empty linestring, this exception is thrown.
 */
class OutOfBoundsException extends \OutOfBoundsException implements SpatialTypeExceptionInterface
{
}
