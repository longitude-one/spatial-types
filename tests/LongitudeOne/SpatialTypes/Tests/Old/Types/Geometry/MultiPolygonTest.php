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
use LongitudeOne\SpatialTypes\Types\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Geometry\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Polygon object tests.
 *
 * @group php
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 *
 * @coversNothing
 */
class MultiPolygonTest extends TestCase
{
    /**
     * @throws InvalidValueException this exception should happen
     */
    public function testAddInvalidPolygon(): void
    {
        $polygon = new Polygon([[[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]]]);
        $multiPolygon = new MultiPolygon([$polygon]);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('The array must contain only objects implementing LineStringInterface or array of PointInterface or "array of array of coordinates".');
        $multiPolygon->addPolygon(['foo']);
    }

    /**
     * Test an empty polygon.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testEmptyMultiPolygon(): void
    {
        $multiPolygon = new MultiPolygon([]);

        static::assertEmpty($multiPolygon->getPolygons());
    }

    /**
     * Test to convert multipolygon to Json.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testJson(): void
    {
        $expected = '{"type":"MultiPolygon","coordinates":[[[[0,0],[10,0],[10,10],[0,10],[0,0]]],[[[5,5],[7,5],[7,7],[5,7],[5,5]]]],"srid":null}';
        $polygons = [
            [
                [
                    [0, 0],
                    [10, 0],
                    [10, 10],
                    [0, 10],
                    [0, 0],
                ],
            ],
            [
                [
                    [5, 5],
                    [7, 5],
                    [7, 7],
                    [5, 7],
                    [5, 5],
                ],
            ],
        ];
        $multiPolygon = new MultiPolygon($polygons);

        static::assertSame($expected, json_encode($multiPolygon));

        $expected = '{"type":"MultiPolygon","coordinates":[[[[0,0],[10,0],[10,10],[0,10],[0,0]]],[[[5,5],[7,5],[7,7],[5,7],[5,5]]]],"srid":4326}';
        $multiPolygon->setSrid(4326);
        static::assertSame($expected, json_encode($multiPolygon));
    }

    /**
     * Test to get last polygon from a multipolygon created from a lot of objects.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPolygonFromObjectsGetLastPolygon(): void
    {
        $firstPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    ]
                ),
            ]
        );
        $lastPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(5, 5),
                        new Point(7, 5),
                        new Point(7, 7),
                        new Point(5, 7),
                        new Point(5, 5),
                    ]
                ),
            ]
        );
        $multiPolygon = new MultiPolygon([$firstPolygon, $lastPolygon]);

        static::assertEquals($lastPolygon, $multiPolygon->getPolygon(-1));
    }

    /**
     * Test to get first polygon from a multipolygon created from a lot of objects.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPolygonFromObjectsGetSinglePolygon(): void
    {
        $firstPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    ]
                ),
            ]
        );
        $lastPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(5, 5),
                        new Point(7, 5),
                        new Point(7, 7),
                        new Point(5, 7),
                        new Point(5, 5),
                    ]
                ),
            ]
        );
        $multiPolygon = new MultiPolygon([$firstPolygon, $lastPolygon]);

        static::assertEquals($firstPolygon, $multiPolygon->getPolygon(0));
    }

    /**
     * Test getPolygons method.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiPolygonAddPolygon(): void
    {
        $expected = [
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(1, 1),
                            new Point(2, 1),
                            new Point(2, 2),
                            new Point(1, 2),
                            new Point(1, 1),
                        ]
                    ),
                ]
            ),
        ];

        $polygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    ]
                ),
            ]
        );

        $multiPolygon = new MultiPolygon([$polygon]);

        $multiPolygon->addPolygon(
            [
                [
                    new Point(5, 5),
                    new Point(7, 5),
                    new Point(7, 7),
                    new Point(5, 7),
                    new Point(5, 5),
                ],
            ]
        );

        $polygonObject = new Polygon(
            [
                new LineString(
                    [
                        new Point(1, 1),
                        new Point(2, 1),
                        new Point(2, 2),
                        new Point(1, 2),
                        new Point(1, 1),
                    ]
                ),
            ]
        );

        $multiPolygon->addPolygon($polygonObject);

        static::assertEquals($expected, $multiPolygon->getPolygons());
    }

    /**
     * Test getPolygons method.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiPolygonFromArraysGetPolygons(): void
    {
        $expected = [
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5),
                        ]
                    ),
                ]
            ),
        ];

        $polygons = [
            [
                [
                    [0, 0],
                    [10, 0],
                    [10, 10],
                    [0, 10],
                    [0, 0],
                ],
            ],
            [
                [
                    [5, 5],
                    [7, 5],
                    [7, 7],
                    [5, 7],
                    [5, 5],
                ],
            ],
        ];

        $multiPolygon = new MultiPolygon($polygons);

        static::assertEquals($expected, $multiPolygon->getPolygons());
    }

    /**
     * Test to convert multipolygon created from objects to array.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiPolygonFromObjectsToArray(): void
    {
        $expected = [
            [
                [
                    [0, 0],
                    [10, 0],
                    [10, 10],
                    [0, 10],
                    [0, 0],
                ],
            ],
            [
                [
                    [5, 5],
                    [7, 5],
                    [7, 7],
                    [5, 7],
                    [5, 5],
                ],
            ],
        ];

        $polygons = [
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5),
                        ]
                    ),
                ]
            ),
        ];

        $multiPolygon = new MultiPolygon($polygons);

        static::assertEquals($expected, $multiPolygon->toArray());
    }
}
