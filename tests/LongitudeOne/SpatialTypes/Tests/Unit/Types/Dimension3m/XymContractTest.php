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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3m;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as Point2D;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString as GeometricLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point as GeometricPoint;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Contract tests for XYM spatial objects.
 *
 * @internal
 *
 * @coversNothing
 */
class XymContractTest extends TestCase
{
    /**
     * Verify that XYM points preserve their coordinate order and serialize it unchanged.
     */
    public function testCoordinatesUseXymOrder(): void
    {
        $point = new GeometricPoint(1, 2, 3, 2154);

        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame([1, 2, 3], $point->toArray());
        static::assertSame('{"type":"Point","coordinates":[1,2,3],"srid":2154}', json_encode($point));
    }

    /**
     * Verify that geographic XYM points preserve their coordinate order.
     */
    public function testGeographicCoordinatesUseXymOrder(): void
    {
        $point = new GeographicPoint('1W', '2N', 3, 4326);

        static::assertSame(-1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame([-1, 2, 3], $point->toArray());
        static::assertSame('{"type":"Point","coordinates":[-1,2,3],"srid":4326}', json_encode($point));
    }

    /**
     * Verify that XYM objects reject points with an incompatible dimension, family, or SRID.
     *
     * @param class-string<\Throwable> $exception the expected exception
     * @param \Closure(): mixed        $operation operation that must fail
     */
    #[DataProvider('provideIncompatiblePointOperations')]
    public function testRejectsIncompatiblePoints(string $exception, \Closure $operation): void
    {
        self::expectException($exception);

        $operation();
    }

    /**
     * Provide incompatible point combinations for XYM line strings.
     *
     * @return \Generator<string, array{0: class-string<\Throwable>, 1: \Closure(): mixed}, null, void>
     */
    public static function provideIncompatiblePointOperations(): \Generator
    {
        yield 'dimension' => [
            InvalidDimensionException::class,
            static fn (): GeometricLineString => new GeometricLineString([new Point2D(1, 2)]),
        ];

        yield 'family' => [
            InvalidFamilyException::class,
            static fn (): GeometricLineString => new GeometricLineString([new GeographicPoint(1, 2, 3)]),
        ];

        yield 'SRID' => [
            InvalidSridException::class,
            static fn (): GeographicLineString => new GeographicLineString([new GeographicPoint(1, 2, 3, 2154)], 4326),
        ];
    }
}
