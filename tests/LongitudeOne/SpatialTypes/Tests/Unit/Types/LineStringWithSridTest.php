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

use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineString as GeographyLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as GeographyPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString as GeometryLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as GeometryPoint;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractLineString
 */
class LineStringWithSridTest extends TestCase
{
    /**
     * Verify that withSrid copies the line string and every one of its points.
     *
     * @param LineStringInterface $lineString line string to copy
     */
    #[DataProvider('provideLineStrings')]
    public function testWithSridPreservesInternalSridConsistency(LineStringInterface $lineString): void
    {
        $lineStringWithSrid = $lineString->withSrid(2154);

        static::assertNotSame($lineString, $lineStringWithSrid);
        static::assertSame($lineString::class, $lineStringWithSrid::class);
        static::assertSame($lineString->toArray(), $lineStringWithSrid->toArray());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(2154, $lineStringWithSrid->getSrid());

        foreach ($lineStringWithSrid->getPoints() as $index => $point) {
            static::assertNotSame($lineString->getPoint($index), $point);
            static::assertSame(2154, $point->getSrid());
        }

        static::assertSame(4326, $lineString->getPoint(0)->getSrid());
        static::assertSame(0, $lineString->getPoint(1)->getSrid());
    }

    /**
     * @return \Generator<string, array{0: LineStringInterface}, null, void>
     */
    public static function provideLineStrings(): \Generator
    {
        yield 'geometry' => [new GeometryLineString([
            new GeometryPoint(1, 2, 4326),
            new GeometryPoint(3, 4),
        ], 4326)];

        yield 'geography' => [new GeographyLineString([
            new GeographyPoint(1, 2, 4326),
            new GeographyPoint(3, 4),
        ], 4326)];
    }
}
