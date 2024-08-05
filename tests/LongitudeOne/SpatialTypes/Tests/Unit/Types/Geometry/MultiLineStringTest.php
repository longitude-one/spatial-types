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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Geometry;

use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Trait\LineStringTrait
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Geometry\MultiLineString
 */
class MultiLineStringTest extends TestCase
{
    /**
     * Test that a geographic line string cannot be added in a geometric multi line string.
     */
    public function testAddGeographicLineStringInGeometricMultiLineString(): void
    {
        $lineString = new GeographicLineString([], 4326);
        $multiLineString = new MultiLineString([], 4326);
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessage('The line string family is not compatible with the family of the current multilinestring.');
        $multiLineString->addLineString($lineString);
    }

    /**
     * Test the constructor with array of cartesian coordinates.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithCartesianCoordinates(): void
    {
        $multiLineString = new MultiLineString([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertCount(1, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2], [3, 4], [3, 6], [1, 2]]], $multiLineString->toArray());

        $multiLineString->addLineString([[3, 4], [997, 997], [992, 811], [3, 4]]);
        static::assertCount(2, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2], [3, 4], [3, 6], [1, 2]], [[3, 4], [997, 997], [992, 811], [3, 4]]], $multiLineString->toArray());

        $multiLineString->addLineStrings([[[1, 2]], [[1, 2]]]);
        static::assertCount(4, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2], [3, 4], [3, 6], [1, 2]], [[3, 4], [997, 997], [992, 811], [3, 4]], [[1, 2]], [[1, 2]]], $multiLineString->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $lineString = new LineString([new Point('40W', '40S', 4326), new Point('45W', '45N', 4326)], 4326);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[-40, -40], [-45, 45]], $lineString->toArray());

        $lineString->addPoint(new Point('40W', '40S', 4326));
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isLine());
        static::assertTrue($lineString->isClosed());
        static::assertTrue($lineString->isRing());
        static::assertEquals([[-40, -40], [-45, 45], [-40, -40]], $lineString->toArray());
    }

    /**
     * Test the constructor with points and addRing method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithPoints(): void
    {
        $multiLineString = new MultiLineString([new LineString([new Point(1, 2), new Point(2, 4), new Point(3, 6), new Point(1, 2)])], 4326);
        static::assertCount(1, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2], [2, 4], [3, 6], [1, 2]]], $multiLineString->toArray());

        $multiLineString->addLineString(new LineString([new Point(3, 4), new Point(7, 7), new Point(2, 11), new Point(3, 4)]));
        static::assertCount(2, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2], [2, 4], [3, 6], [1, 2]], [[3, 4], [7, 7], [2, 11], [3, 4]]], $multiLineString->toArray());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessage('The point SRID is not compatible with the SRID of this current spatial collection.');
        $multiLineString->addLineString(new LineString([[0, 0], [1, 1], [1, 0], [0, 0]], 4327));
    }

    /**
     * Test the empty constructor.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testEmptyConstructor(): void
    {
        $multiLineString = new MultiLineString([]);
        static::assertEmpty($multiLineString->getLineStrings());
        static::assertEmpty($multiLineString->getElements());
        static::assertEquals([], $multiLineString->toArray());
        static::assertEquals([], $multiLineString->getLineStrings());

        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage('The current collection of lineStrings is empty.');
        $multiLineString->getLineString(0);
    }

    /**
     * Test the element getter.
     */
    public function testGetElements(): void
    {
        $multiLineString = new MultiLineString([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertSame($multiLineString->getElements(), $multiLineString->getLineStrings());
    }

    /**
     * Test the getRing method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testGetRing(): void
    {
        $lineA = [[1, 2], [2, 4], [3, 6], [1, 2]];
        $lineB = [[3, 4], [7, 7], [2, 11], [3, 4]];
        $multiLineString = new MultiLineString([$lineA, $lineB]);
        static::assertCount(2, $multiLineString->getLineStrings());
        static::assertSame($lineA, $multiLineString->getLineString(0)->toArray());
        static::assertSame($lineB, $multiLineString->getLineString(1)->toArray());
        static::assertSame($lineA, $multiLineString->getLineString(-2)->toArray());
        static::assertSame($lineB, $multiLineString->getLineString(-1)->toArray());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $multiLineString = new MultiLineString([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertSame('{"type":"MultiLineString","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":null}', json_encode($multiLineString));
        $multiLineString = new MultiLineString([[[1, 2], [3, 4], [3, 6], [1, 2]]], 4326);
        static::assertSame('{"type":"MultiLineString","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":4326}', json_encode($multiLineString));
    }
}
