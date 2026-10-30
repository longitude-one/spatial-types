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

namespace LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Point;

/** Creates Dimension3z Geography Point objects in the shared mode. */
final class PointFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param null|float|int|string $x    X ordinate
     * @param null|float|int|string $y    Y ordinate
     * @param null|float|int        $z    Z ordinate
     * @param int|SpatialReference  $srid Declared reference
     */
    public static function create(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, int|SpatialReference $srid = 0): Point|PointImmutable
    {
        return RepresentationFactory::create(
            PointImmutable::class,
            Point::class,
            [$x, $y, $z, $srid],
            [$x, $y, $z, $srid, SpatialModelEnum::GEOGRAPHY]
        );
    }
}
