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

/**
 * This exception is thrown when mixing objects with non-compatible SRID.
 *
 * For example, when you try to add a Point with SRID 4326 in a MultiPoint with SRID 3857, this exception is thrown.
 * But if any of the objects has no SRID, the operation is allowed.
 * For example, you can add a Point with SRID 4326 in a MultiPoint without SRID.
 * But be careful, after an instance has been associated with another one; the library no more checks the SRID.
 * Be aware developers are responsible to check the SRID before any operation and shall not update SRID after association.
 */
class InvalidSridException extends \Exception implements SpatialTypeExceptionInterface
{
}
