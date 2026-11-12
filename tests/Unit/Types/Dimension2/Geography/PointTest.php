<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
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
        self::assertSame(45.1, new PointImmutable(-1.1, '45.1N')->getY());
    }

    /**
     * Test longitude construction.
     */
    public function testConstructsLongitude(): void
    {
        self::assertSame(-1.1, new PointImmutable('1.1W', 45.1)->getX());
    }

    /**
     * Test the family getter.
     */
    public function testGetFamily(): void
    {
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $this->point->getFamily());
    }

    /**
     * Test the latitude getter.
     */
    public function testGetLatitude(): void
    {
        self::assertSame(45.1, $this->point->getLatitude());
    }

    /**
     * Test the longitude getter.
     */
    public function testGetLongitude(): void
    {
        self::assertSame(-1.1, $this->point->getLongitude());
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
        self::assertSame(GeometryTypeEnum::POINT, $this->point->getType());
    }

    /**
     * Test the X getter.
     */
    public function testGetX(): void
    {
        self::assertSame(-1.1, $this->point->getX());
    }

    /**
     * Test the Y getter.
     */
    public function testGetY(): void
    {
        self::assertSame(45.1, $this->point->getY());
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
        self::assertFalse($this->point->hasM());
    }

    /**
     * Test the has Z method.
     */
    public function testHasZ(): void
    {
        self::assertFalse($this->point->hasZ());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        self::assertSame('{"type":"Point","coordinates":[-1.1,45.1],"srid":0}', json_encode($this->point));
        $this->point = new PointImmutable(-1.1, 45.1, 4326);
        self::assertSame('{"type":"Point","coordinates":[-1.1,45.1],"srid":4326}', json_encode($this->point));
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        self::assertSame([-1.1, 45.1], $this->point->toArray());
    }
}
