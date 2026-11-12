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

use Throwable;

/**
 * All exceptions throwable by longitude-one/spatial-types implement this interface.
 *
 * So developers can catch all exceptions related to spatial types with this interface.
 */
interface SpatialTypeExceptionInterface extends \Throwable
{
}
