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

namespace LongitudeOne\SpatialTypes\Tests\Old\Types\Geometry;

use LongitudeOne\SpatialTypes\Tests\Old\Helper\LineStringHelperTrait;
use LongitudeOne\SpatialTypes\Tests\Old\Helper\PointHelperTrait;
use LongitudeOne\SpatialTypes\Types\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * LineString object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class LineStringTest extends TestCase
{
    use LineStringHelperTrait;
    use PointHelperTrait;

    /**
     * Test an empty line string.
     */
    public function testEmptyLineString(): void
    {
        $lineString = $this->createEmptyLineString();

        static::assertEmpty($lineString->getPoints());
    }

    /**
     * Test isClosed method.
     *
     * @see https://github.com/longitude-one/doctrine-spatial/issues/88
     */
    public function testIsClosedIssue88(): void
    {
        $lineString = new LineString([
            new Point(0, 0),
            new Point(1, 0),
            new Point(1, 1),
            new Point(0, 1),
            new Point(0, 0),
        ]);

        static::assertTrue($lineString->isClosed());

        $lineString = new LineString([
            new Point(0, 0),
            new Point(1, 0),
            new Point(1, 1),
            new Point(0, 1),
        ]);

        static::assertFalse($lineString->isClosed());

        $lineString = new LineString([]);
        static::assertFalse($lineString->isClosed());

        $lineString = new LineString([
            new Point(0, 0),
        ]);
        static::assertFalse($lineString->isClosed());
    }

    /**
     * Test to convert line string to json.
     */
    public function testJson(): void
    {
        $expected = '{"type":"LineString","coordinates":[[0,0],[1,0],[1,1],[0,1],[0,0]],"srid":null}';
        $lineString = $this->createRingLineString();
        static::assertEquals($expected, json_encode($lineString));

        $expected = '{"type":"LineString","coordinates":[[0,0],[1,0],[1,1],[0,1],[0,0]],"srid":4326}';
        $lineString->setSrid(4326);
        static::assertEquals($expected, json_encode($lineString));
    }

    /**
     * Test to get last point.
     */
    public function testLineStringFromArraysGetLastPoint(): void
    {
        $expected = static::createPointE();
        $lineString = $this->createStraightLineString();
        $actual = $lineString->getPoint(-1);

        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get all points of a line string.
     */
    public function testLineStringFromArraysGetPoints(): void
    {
        $expected = [
            static::createPointOrigin(),
            static::createPointB(),
            static::createPointE(),
        ];
        $lineString = $this->createStraightLineString();
        $actual = $lineString->getPoints();

        static::assertCount(3, $actual);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get second point of a linestring.
     */
    public function testLineStringFromArraysGetSinglePoint(): void
    {
        $expected = static::createPointB();
        $lineString = $this->createStraightLineString();
        $actual = $lineString->getPoint(1);

        static::assertEquals($expected, $actual);
    }

    /**
     * Test to verify that a line is closed.
     */
    public function testLineStringFromArraysIsClosed(): void
    {
        $lineString = $this->createRingLineString();

        static::assertTrue($lineString->isClosed());
    }

    /**
     * Test to verify that a line is opened.
     */
    public function testLineStringFromArraysIsOpen(): void
    {
        $lineString = $this->createStraightLineString();

        static::assertFalse($lineString->isClosed());
    }

    /**
     * Test to convert line to array.
     */
    public function testLineStringFromObjectsToArray(): void
    {
        $expected = [
            ['0', '0'],
            ['2', '2'],
            ['5', '5'],
        ];
        $lineString = $this->createStraightLineString();

        static::assertCount(3, $lineString->getPoints());
        static::assertEquals($expected, $lineString->toArray());
    }
}
