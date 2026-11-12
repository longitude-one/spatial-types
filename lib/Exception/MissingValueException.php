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
 * This exception is thrown when a coordinate is missing in an array.
 */
class MissingValueException extends \Exception implements SpatialTypeExceptionInterface
{
}
