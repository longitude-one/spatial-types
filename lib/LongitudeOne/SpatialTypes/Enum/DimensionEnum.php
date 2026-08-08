<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024-2026
 * Copyright Longitude One 2024-2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Enum;

/**
 * Dimension enumeration.
 *
 * This enumeration is used to define the dimensions of spatial instances.
 */
enum DimensionEnum: string
{
    // 2 dimensions
    case X_Y = 'XY';
    // 2 spatial dimensions and 1 measure dimension
    case X_Y_M = 'XYM';
    // 3 spatial dimensions
    case X_Y_Z = 'XYZ';
    // 4 dimensions (spatial and time)
    case X_Y_Z_M = 'XYZM';

    /**
     * Return the number of coordinates required by this dimension.
     */
    public function coordinateCount(): int
    {
        return match ($this) {
            self::X_Y => 2,
            self::X_Y_M, self::X_Y_Z => 3,
            self::X_Y_Z_M => 4,
        };
    }

    /**
     * Does this dimension include an M (measure) coordinate?
     */
    public function hasM(): bool
    {
        return match ($this) {
            self::X_Y_M, self::X_Y_Z_M => true,
            default => false,
        };
    }

    /**
     * Does this dimension include a Z (elevation) coordinate?
     */
    public function hasZ(): bool
    {
        return match ($this) {
            self::X_Y_Z, self::X_Y_Z_M => true,
            default => false,
        };
    }

    /**
     * Return the zero-based M coordinate index, if present.
     */
    public function mIndex(): ?int
    {
        return match ($this) {
            self::X_Y_M => 2,
            self::X_Y_Z_M => 3,
            default => null,
        };
    }

    /**
     * Return the zero-based Z coordinate index, if present.
     */
    public function zIndex(): ?int
    {
        return $this->hasZ() ? 2 : null;
    }
}
