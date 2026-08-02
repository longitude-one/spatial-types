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

namespace LongitudeOne\SpatialTypes\Tests\Old\Types\Geography;

use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPoint;
use PHPUnit\Framework\TestCase;

/**
 * MultiPoint geographic object tests.
 *
 * @internal
 *
 * @coversNothing
 */
class MultiPointTest extends TestCase
{
    /**
     * Test an empty LineString.
     */
    public function testGetType(): void
    {
        $multipoint = new MultiPoint([]);
        static::assertEquals('MultiPoint', $multipoint->getType());
    }
}
