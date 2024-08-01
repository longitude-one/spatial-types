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
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Geometry\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * MultiPoint object tests.
 *
 * @internal
 *
 * @coversNothing
 */
class MultiPointTest extends TestCase
{
    /**
     * Test MultiPoint bad parameter.
     *
     * @throws SpatialTypeExceptionInterface This should happen because of selected value
     */
    public function testBadMultiPointConstructor(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('The point is missing.');

        new MultiPoint([1, 2, 3, 4]);
    }

    /**
     * Test empty multipoint.
     *
     * @throws SpatialTypeExceptionInterface This should not happen because of selected value
     */
    public function testEmptyMultiPoint(): void
    {
        $multiPoint = new MultiPoint([]);

        static::assertEmpty($multiPoint->getPoints());
    }

    /**
     * Test to add point to multipoint.
     *
     * @throws SpatialTypeExceptionInterface this should not happen
     */
    public function testMultiPointAddPoints(): void
    {
        $expected = [
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ];
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
            ]
        );

        $multiPoint
            ->addPoint([2, 2])
            ->addPoint([3, 3])
        ;

        $actual = $multiPoint->getPoints();

        static::assertCount(4, $actual);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get last point from multipoint.
     *
     * @throws SpatialTypeExceptionInterface This should not happen because of selected value
     */
    public function testMultiPointFromArraysGetLastPoint(): void
    {
        $expected = new Point(3, 3);
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $multiPoint->getPoint(-1);

        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get points from multipoint.
     *
     * @throws SpatialTypeExceptionInterface This should not happen because of selected value
     */
    public function testMultiPointFromArraysGetPoints(): void
    {
        $expected = [
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ];
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $multiPoint->getPoints();

        static::assertCount(4, $actual);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get the first point from multipoint.
     *
     * @throws SpatialTypeExceptionInterface This should not happen because of selected value
     */
    public function testMultiPointFromArraysGetSinglePoint(): void
    {
        $expected = new Point(1, 1);
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $multiPoint->getPoint(1);

        static::assertEquals($expected, $actual);
    }

    /**
     * Test to convert multipoint to array.
     *
     * @throws SpatialTypeExceptionInterface This should not happen because of selected value
     */
    public function testMultiPointFromObjectsToArray(): void
    {
        $expected = [
            [0, 0],
            [1, 1],
            [2, 2],
            [3, 3],
        ];
        $multiPoint = new MultiPoint([
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ]);

        static::assertCount(4, $multiPoint->getPoints());
        static::assertEquals($expected, $multiPoint->toArray());
    }
}
