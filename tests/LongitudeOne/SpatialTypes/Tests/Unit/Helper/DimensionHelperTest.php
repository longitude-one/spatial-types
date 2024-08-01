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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Helper;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Helper\DimensionHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Helper\DimensionHelper
 */
class DimensionHelperTest extends TestCase
{
    /**
     * Dimension provider.
     *
     * @return \Generator<string, array{0: DimensionEnum, 1: bool, 2: bool, 3: bool, 4: bool, 5: string}, null, void>
     */
    public static function dimensionProvider(): \Generator
    {
        yield 'XY' => [DimensionEnum::X_Y, true, true, false, false, 'XY'];

        yield 'XYM' => [DimensionEnum::X_Y_M, true, true, false, true, 'XYM'];

        yield 'XYZ' => [DimensionEnum::X_Y_Z, true, true, true, false, 'XYZ'];

        yield 'XYZM' => [DimensionEnum::X_Y_Z_M, true, true, true, true, 'XYZM'];
    }

    /**
     * Test the dimension helper with each value of the enumeration.
     *
     * @param DimensionEnum $dimensionEnum Dimension enumeration
     * @param bool          $x             X dimension present
     * @param bool          $y             Y dimension present
     * @param bool          $z             Z dimension present
     * @param bool          $m             M dimension present
     * @param string        $expectedValue The expected value returned by the getDimension method
     */
    #[DataProvider('dimensionProvider')]
    public function testDimensionHelper(DimensionEnum $dimensionEnum, bool $x, bool $y, bool $z, bool $m, string $expectedValue): void
    {
        $dimensionHelper = new DimensionHelper($dimensionEnum);
        static::assertSame($x, $dimensionHelper->hasX());
        static::assertSame($y, $dimensionHelper->hasY());
        static::assertSame($z, $dimensionHelper->hasZ());
        static::assertSame($m, $dimensionHelper->hasM());
        static::assertSame($expectedValue, $dimensionHelper->getDimension());
    }
}
