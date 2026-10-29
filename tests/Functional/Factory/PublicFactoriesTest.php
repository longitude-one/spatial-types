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

namespace LongitudeOne\SpatialTypes\Tests\Functional\Factory;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\CircularStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\CompoundCurveFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\GeographyCollectionFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\LineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\MultiLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\MultiPointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\MultiPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\PointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\PolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\PolyhedralSurfaceFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\TriangleFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use PHPUnit\Framework\TestCase;

/** Application-facing examples use only supported public factories and read contracts.
 * @internal
 *
 * @coversNothing
 */
final class PublicFactoriesTest extends TestCase
{
    /** Restore the shared mode for subsequent tests. */
    protected function tearDown(): void
    {
        SpatialFactory::immutable();
        parent::tearDown();
    }

    /** Immutable Consumers Assemble Every Supported Type And Nested Collections. */
    public function testImmutableConsumersAssembleEverySupportedTypeAndNestedCollections(): void
    {
        SpatialFactory::immutable();
        $point = PointFactory::create(0, null, 12, 4326);
        $line = LineStringFactory::create([$point], 4326);
        $circular = CircularStringFactory::create([$point], 4326);
        $compound = CompoundCurveFactory::create([$line, $circular], 4326);
        $polygon = PolygonFactory::create([$line], 4326);
        $triangle = TriangleFactory::create([$line], 4326);
        $surface = PolyhedralSurfaceFactory::create([$polygon], 4326);
        $multiPoint = MultiPointFactory::create([$point], 4326);
        $multiLine = MultiLineStringFactory::create([$line], 4326);
        $multiPolygon = MultiPolygonFactory::create([$polygon], 4326);
        $nested = GeographyCollectionFactory::create(4326, [$point, $surface]);
        $collection = GeographyCollectionFactory::create(4326, [
            $point, $line, $circular, $compound, $polygon, $triangle,
            $surface, $multiPoint, $multiLine, $multiPolygon, $nested,
        ]);

        self::assertInstanceOf(ImmutableSpatialInterface::class, $collection);
        self::assertSame([
            $point, $line, $circular, $compound, $polygon, $triangle,
            $surface, $multiPoint, $multiLine, $multiPolygon, $nested,
        ], $collection->getElements());
        self::assertSame([$point, $surface], $nested->getElements());
        self::assertSame([
            [0, null, 12],
            [[0, null, 12]],
            [[0, null, 12]],
            [[[0, null, 12]], [[0, null, 12]]],
            [[[0, null, 12]]],
            [[[0, null, 12]]],
            [[[[0, null, 12]]]],
            [[0, null, 12]],
            [[[0, null, 12]]],
            [[[[0, null, 12]]]],
            [[0, null, 12], [[[[0, null, 12]]]]],
        ], $collection->toArray());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $collection->getFamily());
        self::assertSame(4326, $collection->getSrid());
    }

    /** Mutable Consumers Assemble Every Supported Type And Nested Collections. */
    public function testMutableConsumersAssembleEverySupportedTypeAndNestedCollections(): void
    {
        SpatialFactory::mutable();
        $point = PointFactory::create(0, null, 12, 4326);
        $line = LineStringFactory::create([$point], 4326);
        $circular = CircularStringFactory::create([$point], 4326);
        $compound = CompoundCurveFactory::create([$line, $circular], 4326);
        $polygon = PolygonFactory::create([$line], 4326);
        $triangle = TriangleFactory::create([$line], 4326);
        $surface = PolyhedralSurfaceFactory::create([$polygon], 4326);
        $multiPoint = MultiPointFactory::create([$point], 4326);
        $multiLine = MultiLineStringFactory::create([$line], 4326);
        $multiPolygon = MultiPolygonFactory::create([$polygon], 4326);
        $nested = GeographyCollectionFactory::create(4326, [$point, $surface]);
        $collection = GeographyCollectionFactory::create(4326, [
            $point, $line, $circular, $compound, $polygon, $triangle,
            $surface, $multiPoint, $multiLine, $multiPolygon, $nested,
        ]);

        self::assertInstanceOf(MutableSpatialInterface::class, $collection);
        self::assertSame([
            $point, $line, $circular, $compound, $polygon, $triangle,
            $surface, $multiPoint, $multiLine, $multiPolygon, $nested,
        ], $collection->getElements());
        self::assertSame([$point, $surface], $nested->getElements());
        self::assertSame([
            [0, null, 12],
            [[0, null, 12]],
            [[0, null, 12]],
            [[[0, null, 12]], [[0, null, 12]]],
            [[[0, null, 12]]],
            [[[0, null, 12]]],
            [[[[0, null, 12]]]],
            [[0, null, 12]],
            [[[0, null, 12]]],
            [[[[0, null, 12]]]],
            [[0, null, 12], [[[[0, null, 12]]]]],
        ], $collection->toArray());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $collection->getFamily());
        self::assertSame(4326, $collection->getSrid());
    }
}
