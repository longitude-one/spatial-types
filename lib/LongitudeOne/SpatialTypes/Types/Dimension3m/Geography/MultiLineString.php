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

namespace LongitudeOne\SpatialTypes\Types\Dimension3m\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\MultiLineStringInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3m\AbstractMultiLineString;

/**
 * Geographic multi-line string using the XYM layout.
 */
class MultiLineString extends AbstractMultiLineString implements MultiLineStringInterface
{
    /**
     * Return the geography family.
     */
    public function getFamily(): SpatialModelEnum
    {
        return SpatialModelEnum::GEOGRAPHY;
    }
}
