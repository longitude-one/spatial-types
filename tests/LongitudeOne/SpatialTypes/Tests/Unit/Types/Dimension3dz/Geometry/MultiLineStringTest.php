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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3z\Geometry;

use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the three-dimensional geometric multi-line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Trait\LineStringTrait
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineString
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
        static::expectExceptionMessageIsOrContains('The line string family is not compatible with the family of the current multilinestring.');
        $multiLineString->addLineString($lineString);
    }

    /**
     * Test that a line string with a different dimension cannot be added.
     */
    public function testAddLineStringWithInvalidDimension(): void
    {
        $lineString = static::createStub(LineStringInterface::class);
        $lineString->method('hasSameDimension')->willReturn(false);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The line string dimension is not compatible with the dimension of the current linestring collection.');

        (new MultiLineString([]))->addLineString($lineString);
    }

    /**
     * Test the constructor with array of cartesian coordinates.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithCartesianCoordinates(): void
    {
        $multiLineString = new MultiLineString([[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]);
        static::assertCount(1, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]], $multiLineString->toArray());

        $multiLineString->addLineString([[3, 4, 5], [997, 997, 6], [992, 811, 7], [3, 4, 5]]);
        static::assertCount(2, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]], [[3, 4, 5], [997, 997, 6], [992, 811, 7], [3, 4, 5]]], $multiLineString->toArray());

        $multiLineString->addLineStrings([[[1, 2, 3]], [[1, 2, 3]]]);
        static::assertCount(4, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]], [[3, 4, 5], [997, 997, 6], [992, 811, 7], [3, 4, 5]], [[1, 2, 3]], [[1, 2, 3]]], $multiLineString->toArray());
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        $lineString = new LineString([new Point('40W', '40S', 0, 4326), new Point('45W', '45N', 0, 4326)], 4326);
        static::assertCount(2, $lineString->getPoints());
        static::assertFalse($lineString->isClosed());
        static::assertFalse($lineString->isRing());
        static::assertTrue($lineString->isLine());
        static::assertEquals([[-40, -40, 0], [-45, 45, 0]], $lineString->toArray());

        $lineString->addPoint(new Point('40W', '40S', 0, 4326));
        static::assertCount(3, $lineString->getPoints());
        static::assertTrue($lineString->isLine());
        static::assertTrue($lineString->isClosed());
        static::assertTrue($lineString->isRing());
        static::assertEquals([[-40, -40, 0], [-45, 45, 0], [-40, -40, 0]], $lineString->toArray());
    }

    /**
     * Test the constructor with points and addRing method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithPoints(): void
    {
        $multiLineString = new MultiLineString([new LineString([new Point(1, 2, 3), new Point(2, 4, 5), new Point(3, 6, 7), new Point(1, 2, 3)])], 4326);
        static::assertCount(1, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2, 3], [2, 4, 5], [3, 6, 7], [1, 2, 3]]], $multiLineString->toArray());

        $multiLineString->addLineString(new LineString([new Point(3, 4, 5), new Point(7, 7, 6), new Point(2, 11, 7), new Point(3, 4, 5)]));
        static::assertCount(2, $multiLineString->getLineStrings());
        static::assertEquals([[[1, 2, 3], [2, 4, 5], [3, 6, 7], [1, 2, 3]], [[3, 4, 5], [7, 7, 6], [2, 11, 7], [3, 4, 5]]], $multiLineString->toArray());

        self::expectException(InvalidSridException::class);
        self::expectExceptionMessageIsOrContains('The point SRID is not compatible with the SRID of this current spatial collection.');
        $multiLineString->addLineString(new LineString([[0, 0, 0], [1, 1, 0], [1, 0, 0], [0, 0, 0]], 4327));
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
        static::expectExceptionMessageIsOrContains('The current collection of lineStrings is empty.');
        $multiLineString->getLineString(0);
    }

    /**
     * Test the element getter.
     */
    public function testGetElements(): void
    {
        $multiLineString = new MultiLineString([[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]);
        static::assertSame($multiLineString->getElements(), $multiLineString->getLineStrings());
    }

    /**
     * Test the getRing method.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testGetRing(): void
    {
        $lineA = [[1, 2, 3], [2, 4, 5], [3, 6, 7], [1, 2, 3]];
        $lineB = [[3, 4, 5], [7, 7, 6], [2, 11, 7], [3, 4, 5]];
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
        $multiLineString = new MultiLineString([[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]]);
        static::assertSame('{"type":"MultiLineString","coordinates":[[[1,2,3],[3,4,5],[3,6,7],[1,2,3]]],"srid":0}', json_encode($multiLineString));
        $multiLineString = new MultiLineString([[[1, 2, 3], [3, 4, 5], [3, 6, 7], [1, 2, 3]]], 4326);
        static::assertSame('{"type":"MultiLineString","coordinates":[[[1,2,3],[3,4,5],[3,6,7],[1,2,3]]],"srid":4326}', json_encode($multiLineString));
    }
}
