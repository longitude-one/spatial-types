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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Enum;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Enum\FamilyEnum
 */
class FamilyEnumTest extends TestCase
{
    /**
     * Check whether each family uses geodetic coordinates.
     *
     * @param FamilyEnum $family   family to check
     * @param bool       $expected expected result
     */
    #[DataProvider('provideFamilies')]
    public function testUsesGeodeticCoordinates(FamilyEnum $family, bool $expected): void
    {
        static::assertSame($expected, $family->usesGeodeticCoordinates());
    }

    /**
     * Provide every supported spatial family.
     *
     * @return \Generator<string, array{0: FamilyEnum, 1: bool}, null, void>
     */
    public static function provideFamilies(): \Generator
    {
        yield 'Geography' => [FamilyEnum::GEOGRAPHY, true];

        yield 'Geometry' => [FamilyEnum::GEOMETRY, false];
    }
}
