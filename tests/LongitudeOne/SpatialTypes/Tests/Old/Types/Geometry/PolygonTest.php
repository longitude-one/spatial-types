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

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Tests\Old\Helper\LineStringHelperTrait;
use LongitudeOne\SpatialTypes\Tests\Old\Helper\PolygonHelperTrait;
use LongitudeOne\SpatialTypes\Types\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Geometry\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Polygon object tests.
 *
 * @internal
 *
 * @coversNothing
 */
class PolygonTest extends TestCase
{
    use LineStringHelperTrait;
    use PolygonHelperTrait;

    /**
     * Test an empty polygon.
     */
    public function testEmptyPolygon(): void
    {
        $polygon = $this->createEmptyPolygon();

        static::assertEmpty($polygon->getRings());
    }

    /**
     * Test to export json.
     */
    public function testJson(): void
    {
        $expected = '{"type":"Polygon","coordinates":[[[0,0],[10,0],[10,10],[0,10],[0,0]],[[5,5],[7,5],[7,7],[5,7],[5,5]]],"srid":null}';
        $polygon = $this->createHoleyPolygon();
        static::assertEquals($expected, json_encode($polygon));

        $expected = '{"type":"Polygon","coordinates":[[[0,0],[10,0],[10,10],[0,10],[0,0]],[[5,5],[7,5],[7,7],[5,7],[5,5]]],"srid":4326}';
        $polygon->setSrid(4326);
        static::assertEquals($expected, json_encode($polygon));
    }

    /**
     * Test Polygon with open ring.
     */
    public function testOpenPolygonRing(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('The line string is not a ring.');

        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
            ]),
        ];

        new Polygon($rings);
    }

    /**
     * Test to get last ring.
     */
    public function testRingPolygonFromObjectsGetLastRing(): void
    {
        $ringA = $this->createRingLineString();
        $ringB = $this->createNodeLineString();
        $polygon = $this->createEmptyPolygon();

        try {
            $polygon->addRing($ringA);
            $polygon->addRing($ringB);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to add ring to polygon: %s', $e->getMessage()));
        }

        static::assertEquals($ringB, $polygon->getRing(-1));
    }

    /**
     * Test to get the first ring.
     */
    public function testRingPolygonFromObjectsGetSingleRing(): void
    {
        $ringA = $this->createRingLineString();
        $ringB = $this->createNodeLineString();
        $polygon = $this->createEmptyPolygon();

        try {
            $polygon->addRing($ringA);
            $polygon->addRing($ringB);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to add ring to polygon: %s', $e->getMessage()));
        }

        static::assertEquals($ringA, $polygon->getRing(0));
    }

    /**
     * Test a solid polygon from array add rings.
     */
    public function testSolidPolygonFromArrayAddRings(): void
    {
        $expected = [$this->createRingLineString(), $this->createNodeLineString()];
        $ring = [
            [
                [0, 0],
                [1, 0],
                [1, 1],
                [0, 1],
                [0, 0],
            ],
        ];

        try {
            $polygon = new Polygon($ring);

            $polygon->addRing(
                [
                    [0, 0],
                    [1, 0],
                    [0, 1],
                    [1, 1],
                    [0, 0],
                ]
            );

            static::assertEquals($expected, $polygon->getRings());
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to add ring to polygon: %s', $e->getMessage()));
        }
    }

    /**
     * Test a solid polygon from an array of points.
     */
    public function testSolidPolygonFromArrayOfPoints(): void
    {
        $expected = [
            [
                [0, 0],
                [1, 0],
                [1, 1],
                [0, 1],
                [0, 0],
            ],
        ];
        $rings = $this->createRingLineString();

        try {
            $polygon = new Polygon([$rings]);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create polygon from ring linestring: %s', $e->getMessage()));
        }

        static::assertEquals($expected, $polygon->toArray());
    }

    /**
     * Test a solid polygon from an array of rings.
     */
    public function testSolidPolygonFromArraysOfRings(): void
    {
        $expected = [$this->createRingLineString()];
        $rings = [
            [
                [0, 0],
                [1, 0],
                [1, 1],
                [0, 1],
                [0, 0],
            ],
        ];

        try {
            $polygon = new Polygon($rings);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create polygon from ring linestring: %s', $e->getMessage()));
        }

        static::assertEquals($expected, $polygon->getRings());
    }

    /**
     * Test solid polygon from objects to array.
     */
    public function testSolidPolygonFromObjectsToArray(): void
    {
        $expected = [
            [
                [0, 0],
                [1, 0],
                [1, 1],
                [0, 1],
                [0, 0],
            ],
        ];
        $rings = [$this->createRingLineString()];

        try {
            $polygon = new Polygon($rings);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create polygon from ring linestring: %s', $e->getMessage()));
        }

        static::assertEquals($expected, $polygon->toArray());
    }
}
