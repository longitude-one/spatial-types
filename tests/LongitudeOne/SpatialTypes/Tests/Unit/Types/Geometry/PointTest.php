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

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\Geometry\Point
 */
class PointTest extends TestCase
{
    /**
     * Point instance.
     */
    private Point $point;

    /**
     * Set up the point instance.
     *
     * @throws InvalidValueException This won't be thrown in this test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->point = new Point(1, 2);
    }

    /**
     * Test the family getter.
     */
    public function testGetFamily(): void
    {
        static::assertSame(FamilyEnum::GEOMETRY, $this->point->getFamily());
    }

    /**
     * Test the latitude getter.
     */
    public function testGetLatitude(): void
    {
        static::assertSame(2, $this->point->getLatitude());
    }

    /**
     * Test the longitude getter.
     */
    public function testGetLongitude(): void
    {
        static::assertSame(1, $this->point->getLongitude());
    }

    /**
     * Test the elevation, the M getter.
     */
    public function testGetM(): void
    {
        self::expectException(BadMethodCallException::class);
        self::expectExceptionMessage('The method "LongitudeOne\SpatialTypes\Types\Geometry\Point::getM" cannot be called with a spatial object with dimensions "XY".');
        $this->point->getM();
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame(TypeEnum::POINT->value, $this->point->getType());
    }

    /**
     * Test the X getter.
     */
    public function testGetX(): void
    {
        static::assertSame(1, $this->point->getX());
    }

    /**
     * Test the Y getter.
     */
    public function testGetY(): void
    {
        static::assertSame(2, $this->point->getY());
    }

    /**
     * Test the Z getter.
     */
    public function testGetZ(): void
    {
        self::expectException(BadMethodCallException::class);
        self::expectExceptionMessage('The method "LongitudeOne\SpatialTypes\Types\Geometry\Point::getZ" cannot be called with a spatial object with dimensions "XY".');
        $this->point->getZ();
    }

    /**
     * Test the has M method.
     */
    public function testHasM(): void
    {
        static::assertFalse($this->point->hasM());
    }

    /**
     * Test the has Z method.
     */
    public function testHasZ(): void
    {
        static::assertFalse($this->point->hasZ());
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        static::assertSame([1, 2], $this->point->toArray());
    }

    /**
     * Test the toString method.
     */
    public function testToString(): void
    {
        static::assertSame('1 2', (string) $this->point);
    }
}
