<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
