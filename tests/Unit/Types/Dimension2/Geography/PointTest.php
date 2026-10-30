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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension2\Geography;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Unit test of geographic point.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable
 */
class PointTest extends TestCase
{
    /**
     * Point instance.
     */
    private PointImmutable $point;

    /**
     * Set up the point instance.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->point = new PointImmutable('1.1W', '45.1N');
    }

    /**
     * Test latitude construction.
     */
    public function testConstructsLatitude(): void
    {
        static::assertSame(45.1, (new PointImmutable(-1.1, '45.1N'))->getY());
    }

    /**
     * Test longitude construction.
     */
    public function testConstructsLongitude(): void
    {
        static::assertSame(-1.1, (new PointImmutable('1.1W', 45.1))->getX());
    }

    /**
     * Test the family getter.
     */
    public function testGetFamily(): void
    {
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $this->point->getFamily());
    }

    /**
     * Test the latitude getter.
     */
    public function testGetLatitude(): void
    {
        static::assertSame(45.1, $this->point->getLatitude());
    }

    /**
     * Test the longitude getter.
     */
    public function testGetLongitude(): void
    {
        static::assertSame(-1.1, $this->point->getLongitude());
    }

    /**
     * Test the elevation, the M getter.
     */
    public function testGetM(): void
    {
        self::expectException(BadMethodCallException::class);
        self::expectExceptionMessageIsOrContains('The method "LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable::getM" cannot be called with a spatial object with dimensions "XY".');
        $this->point->getM();
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame(GeometryTypeEnum::POINT, $this->point->getType());
    }

    /**
     * Test the X getter.
     */
    public function testGetX(): void
    {
        static::assertSame(-1.1, $this->point->getX());
    }

    /**
     * Test the Y getter.
     */
    public function testGetY(): void
    {
        static::assertSame(45.1, $this->point->getY());
    }

    /**
     * Test the Z getter.
     */
    public function testGetZ(): void
    {
        self::expectException(BadMethodCallException::class);
        self::expectExceptionMessageIsOrContains('The method "LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable::getZ" cannot be called with a spatial object with dimensions "XY".');
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
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        static::assertSame('{"type":"Point","coordinates":[-1.1,45.1],"srid":0}', json_encode($this->point));
        $this->point = new PointImmutable(-1.1, 45.1, 4326);
        static::assertSame('{"type":"Point","coordinates":[-1.1,45.1],"srid":4326}', json_encode($this->point));
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        static::assertSame([-1.1, 45.1], $this->point->toArray());
    }
}
