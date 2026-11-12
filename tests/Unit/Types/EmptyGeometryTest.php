<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
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
     * @return \Generator<string, array{0: SpatialInterface, 1: GeometryTypeEnum}>
     */
    public static function provideEmptyGeometries(): \Generator
    {
        foreach (['Dimension2', 'Dimension3m', 'Dimension3z', 'Dimension4zm'] as $dimension) {
            $namespace = \sprintf('LongitudeOne\SpatialTypes\Types\%s\Geometry\\', $dimension);

            yield $dimension.' Point' => [new ($namespace.'PointImmutable')(), GeometryTypeEnum::POINT];

            yield $dimension.' LineString' => [new ($namespace.'LineStringImmutable')([]), GeometryTypeEnum::LINESTRING];

            yield $dimension.' Polygon' => [new ($namespace.'PolygonImmutable')([]), GeometryTypeEnum::POLYGON];

            yield $dimension.' MultiPoint' => [new ($namespace.'MultiPointImmutable')([]), GeometryTypeEnum::MULTIPOINT];

            yield $dimension.' MultiLineString' => [new ($namespace.'MultiLineStringImmutable')([]), GeometryTypeEnum::MULTILINESTRING];

            yield $dimension.' MultiPolygon' => [new ($namespace.'MultiPolygonImmutable')([]), GeometryTypeEnum::MULTIPOLYGON];

            yield $dimension.' GeometryCollection' => [new ($namespace.'GeometryCollectionImmutable')(), GeometryTypeEnum::GEOMETRYCOLLECTION];
        }
    }

    /**
     * Empty Geometry values retain their concrete type and coordinate dimension.
     *
     * @param SpatialInterface $geometry Empty geometry instance
     * @param GeometryTypeEnum $type     Expected concrete geometry type
     */
    #[DataProvider('provideEmptyGeometries')]
    public function testEmptyGeometryCanBeCreated(SpatialInterface $geometry, GeometryTypeEnum $type): void
    {
        self::assertSame($type, $geometry->getType());
        self::assertSame([], $geometry->toArray());
    }
}
