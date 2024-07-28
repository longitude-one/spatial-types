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

namespace LongitudeOne\SpatialTypes\Helper;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Interfaces\DimensionInterface;

/**
 * Dimension helper.
 *
 * This helper is used to simplify the code of constructors.
 */
class DimensionHelper implements DimensionInterface
{
    /**
     * DimensionHelper constructor.
     *
     * @param DimensionEnum $dimension dimension
     */
    public function __construct(private readonly DimensionEnum $dimension)
    {
    }

    /**
     * Get the dimension as string.
     */
    public function getDimension(): string
    {
        return $this->dimension->value;
    }

    /**
     * Does this instance contain an M dimension?
     */
    public function hasM(): bool
    {
        return match ($this->dimension) {
            DimensionEnum::X_Y_M, DimensionEnum::X_Y_Z_M => true,
            default => false,
        };
    }

    /**
     * TODO When PHP 8.1 won't be supported anymore, update the return type to true.
     *
     * @return true
     */
    public function hasX(): bool
    {
        return true;
    }

    /**
     * TODO When PHP 8.1 won't be supported anymore, update the return type to true.
     *
     * @return true
     */
    public function hasY(): bool
    {
        return true;
    }

    /**
     * Does this instance contain a Z dimension?
     */
    public function hasZ(): bool
    {
        return match ($this->dimension) {
            DimensionEnum::X_Y_Z, DimensionEnum::X_Y_Z_M => true,
            default => false,
        };
    }
}
