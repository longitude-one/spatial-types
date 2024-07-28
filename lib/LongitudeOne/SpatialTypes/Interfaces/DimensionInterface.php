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

namespace LongitudeOne\SpatialTypes\Interfaces;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;

/**
 * Dimension interface.
 *
 * This interface is used to define the dimension of the spatial interfaces.
 * This interface is essentially used internally in constructors to simplify the code.
 */
interface DimensionInterface
{
    /**
     * DimensionInterface constructor.
     *
     * @param DimensionEnum $dimension dimension
     */
    public function __construct(DimensionEnum $dimension);

    /**
     * Is the moment present? Is the M dimension present?
     */
    public function hasM(): bool;

    /**
     * Is the elevation present? Is the Z dimension present?
     */
    public function hasZ(): bool;
}
