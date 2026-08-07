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

namespace LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\AbstractMultiPoint;

class MultiPoint extends AbstractMultiPoint implements MultiPointInterface
{
    /**
     * Define the family.
     */
    public function getFamily(): FamilyEnum
    {
        return FamilyEnum::GEOMETRY;
    }
}
