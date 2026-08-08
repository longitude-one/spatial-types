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

use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Enum\TypeEnum
 */
class TypeEnumTest extends TestCase
{
    /**
     * Check the structure described by every spatial type.
     *
     * @param TypeEnum      $type                  type to check
     * @param bool          $expectedIsMulti       expected multi-type status
     * @param null|TypeEnum $expectedComponentType expected homogeneous component type
     * @param null|int      $expectedDimension     expected topological dimension
     */
    #[DataProvider('provideTypes')]
    public function testStructure(TypeEnum $type, bool $expectedIsMulti, ?TypeEnum $expectedComponentType, ?int $expectedDimension): void
    {
        static::assertSame($expectedIsMulti, $type->isMulti());
        static::assertSame($expectedComponentType, $type->componentType());
        static::assertSame($expectedDimension, $type->topologicalDimension());
    }

    /**
     * Provide every supported spatial type and its structure.
     *
     * @return \Generator<string, array{0: TypeEnum, 1: bool, 2: null|TypeEnum, 3: null|int}, null, void>
     */
    public static function provideTypes(): \Generator
    {
        yield 'Collection' => [TypeEnum::COLLECTION, false, null, null];

        yield 'LineString' => [TypeEnum::LINESTRING, false, TypeEnum::POINT, 1];

        yield 'MultiLineString' => [TypeEnum::MULTILINESTRING, true, TypeEnum::LINESTRING, 1];

        yield 'MultiPoint' => [TypeEnum::MULTIPOINT, true, TypeEnum::POINT, 0];

        yield 'MultiPolygon' => [TypeEnum::MULTIPOLYGON, true, TypeEnum::POLYGON, 2];

        yield 'Point' => [TypeEnum::POINT, false, null, 0];

        yield 'Polygon' => [TypeEnum::POLYGON, false, TypeEnum::LINESTRING, 2];
    }
}
