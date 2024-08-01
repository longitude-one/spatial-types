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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
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
     * @return \Generator<string, array{0: string, 1: string, 2: string}, null, void>
     */
    public static function provideInvalidCoordinates(): \Generator
    {
        yield 'Longitude greater than 180' => ['181W', '0N', 'Out of range longitude value, longitude must be between -180 and 180, got "181W".'];

        yield 'Latitude greater than 90' => ['180W', '100N', 'Out of range latitude value, latitude must be between -90 and 90, got "100N".'];

        yield 'Minutes greater than 60' => ['79:60:55.832W', '100N', 'Out of range minute value, minute must be between 0 and 59, got "79:60:55.832W".'];

        yield 'Secondes greater than 60' => ['79:55:60.832W', '100N', 'Out of range second value, second must be between 0 and 59, got "79:55:60.832W".'];

        yield 'Invalid coordinate value' => ['180W', '85N 60', 'Invalid coordinate value, got "85N 60".'];

        yield 'Invalid array value' => ['180W 85N', '160W 85S', 'Invalid coordinate value, coordinate cannot be an array.'];

        yield 'Invalid value' => ['FOO', 'BAR', 'Invalid coordinate value, got "FOO".'];
    }

    /**
     * Test the equalsTo method of PointInterface.
     */
    public function testEqualsTo(): void
    {
        $point = new Point(1, 2);
        static::assertTrue($this->point->equalsTo($point));
        static::assertTrue($point->equalsTo($this->point));

        $point->setSrid(4326);
        static::assertFalse($this->point->equalsTo($point));
        static::assertFalse($point->equalsTo($this->point));

        $this->point->setSrid(4326);
        static::assertTrue($this->point->equalsTo($point));
        static::assertTrue($point->equalsTo($this->point));

        $point->setX(3);
        static::assertFalse($this->point->equalsTo($point));
        static::assertFalse($point->equalsTo($this->point));

        $point->setX(1);
        $point->setY(3);
        static::assertFalse($this->point->equalsTo($point));
        static::assertFalse($point->equalsTo($this->point));
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
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        static::assertSame('{"type":"Point","coordinates":[1,2],"srid":null}', json_encode($this->point));
        $this->point->setSrid(4326);
        static::assertSame('{"type":"Point","coordinates":[1,2],"srid":4326}', json_encode($this->point));
    }

    /**
     * Test that the out of range coordinates throw exceptions.
     *
     * @param string $longitude       Longitude
     * @param string $latitude        Latitude
     * @param string $expectedMessage Expected message thrown by exception
     *
     * @throws InvalidValueException It shall happen
     */
    #[DataProvider('provideInvalidCoordinates')]
    public function testOutOfRange(string $longitude, string $latitude, string $expectedMessage): void
    {
        // No exception
        new Point(181, 91);
        new Point('181', '91');

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage($expectedMessage);
        new Point($longitude, $latitude);
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        static::assertSame([1, 2], $this->point->toArray());
    }
}
