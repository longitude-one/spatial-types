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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Empty geometry construction tests.
 *
 * ISO/IEC 13249-3, 5.1.57 permits EMPTY values for each of these types.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyGeometryTest extends TestCase
{
    /**
     * Empty Geometry values retain their concrete type and coordinate dimension.
     *
     * @param SpatialInterface $geometry Empty geometry instance
     * @param GeometryTypeEnum $type     Expected concrete geometry type
     */
    #[DataProvider('provideEmptyGeometries')]
    public function testEmptyGeometryCanBeCreated(SpatialInterface $geometry, GeometryTypeEnum $type): void
    {
        static::assertSame($type, $geometry->getType());
        static::assertTrue($geometry->isEmpty());
        static::assertSame([], $geometry->toArray());
    }

    /**
     * @return \Generator<string, array{0: SpatialInterface, 1: GeometryTypeEnum}>
     */
    public static function provideEmptyGeometries(): \Generator
    {
        foreach (['Dimension2', 'Dimension3m', 'Dimension3z', 'Dimension4zm'] as $dimension) {
            $namespace = sprintf('LongitudeOne\SpatialTypes\Types\%s\Geometry\\', $dimension);

            yield $dimension.' Point' => [new ($namespace.'Point')(), GeometryTypeEnum::POINT];

            yield $dimension.' LineString' => [new ($namespace.'LineString')([]), GeometryTypeEnum::LINESTRING];

            yield $dimension.' Polygon' => [new ($namespace.'Polygon')([]), GeometryTypeEnum::POLYGON];

            yield $dimension.' MultiPoint' => [new ($namespace.'MultiPoint')([]), GeometryTypeEnum::MULTIPOINT];

            yield $dimension.' MultiLineString' => [new ($namespace.'MultiLineString')([]), GeometryTypeEnum::MULTILINESTRING];

            yield $dimension.' MultiPolygon' => [new ($namespace.'MultiPolygon')([]), GeometryTypeEnum::MULTIPOLYGON];

            yield $dimension.' GeometryCollection' => [new ($namespace.'GeometryCollection')(), GeometryTypeEnum::GEOMETRYCOLLECTION];
        }
    }
}
