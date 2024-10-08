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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\Geo\String\Exception\RangeException as GeoParserRangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Parser;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\RangeException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;

/**
 * Abstract point object for POINT spatial types.
 *
 * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
 * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
 *
 * @internal This class is internal. It is used to create geometry or geography point of spatial objects.
 */
abstract class AbstractPoint extends AbstractSpatialType implements PointInterface
{
    /**
     * The X coordinate or the longitude.
     */
    protected float|int $x;

    /**
     * The Y coordinate or the latitude.
     */
    protected float|int $y;

    /**
     * Is the current point equal to another point?
     *
     * @param PointInterface $point the point to compare
     */
    public function equalsTo(PointInterface $point): bool
    {
        if (!($point instanceof static && $point->getSrid() === $this->getSrid() && $point->getFamily() === $this->getFamily() && $point->getDimension() === $this->getDimension())) {
            return false;
        }

        if ($point->getX() !== $this->getX() || $point->getY() !== $this->getY()) {
            return false;
        }

        if ($point->hasM() && $this->hasM() && $this->getM() !== $point->getM()) {
            return false;
        }

        if ($point->hasZ() && $this->hasZ() && $this->getZ() !== $point->getZ()) {
            return false;
        }

        return true;
    }

    /**
     * Latitude getter.
     */
    public function getLatitude(): float|int
    {
        return $this->getY();
    }

    /**
     * Longitude getter.
     */
    public function getLongitude(): float|int
    {
        return $this->getX();
    }

    /**
     * X getter. (Longitude getter).
     */
    public function getX(): float|int
    {
        return $this->x;
    }

    /**
     * Y getter. Latitude getter.
     */
    public function getY(): float|int
    {
        return $this->y;
    }

    /**
     * Latitude fluent setter.
     *
     * @param float|int|string $latitude the new latitude of point
     *
     * @throws InvalidValueException when latitude is not valid
     */
    public function setLatitude(float|int|string $latitude): static
    {
        try {
            $geodesicCoordinate = $this->setGeodesicCoordinate($latitude, -90, 90);
        } catch (RangeException $e) {
            throw new InvalidValueException(sprintf(InvalidValueException::OUT_OF_RANGE_LATITUDE, $latitude), $e->getCode(), $e);
        }

        $this->y = $geodesicCoordinate;

        return $this;
    }

    /**
     * Longitude setter.
     *
     * @param float|int|string $longitude the new longitude
     *
     * @throws InvalidValueException when longitude is not valid
     */
    public function setLongitude(float|int|string $longitude): static
    {
        try {
            $geodesicCoordinate = $this->setGeodesicCoordinate($longitude, -180, 180);
        } catch (RangeException $e) {
            throw new InvalidValueException(sprintf(InvalidValueException::OUT_OF_RANGE_LONGITUDE, $longitude), $e->getCode(), $e);
        }

        $this->x = $geodesicCoordinate;

        return $this;
    }

    /**
     * X setter. (Latitude setter).
     *
     * @param float|int|string $x the new X
     *
     * @throws InvalidValueException when x is not valid
     */
    public function setX(float|int|string $x): static
    {
        $this->x = $this->setCartesianCoordinate($x);

        return $this;
    }

    /**
     * Y setter. Longitude Setter.
     *
     * @param float|int|string $y the new Y value
     *
     * @throws InvalidValueException when Y is invalid, not in valid range
     */
    public function setY(float|int|string $y): static
    {
        $this->y = $this->setCartesianCoordinate($y);

        return $this;
    }

    /**
     * Convert point into an array X, Y.
     * Latitude, longitude.
     *
     * AbstractPoint does NOT contain SpatialInterface, only floats, integers in a one-dimensional arrays.
     *
     * @return array{0 : float|int, 1 : float|int}
     */
    public function toArray(): array
    {
        return [$this->x, $this->y];
    }

    /**
     * Use the longitude-one/geo-parser to parse a coordinate.
     *
     * @param string $coordinate the coordinate to parse
     *
     * @throws InvalidValueException when coordinate is invalid
     */
    protected function geoParse(string $coordinate): float|int
    {
        try {
            $parser = new Parser($coordinate);

            $parsedCoordinate = $parser->parse();
        } catch (GeoParserRangeException $e) {
            $message = match ($e->getCode()) {
                GeoParserRangeException::LATITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LATITUDE, $coordinate),
                GeoParserRangeException::LONGITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LONGITUDE, $coordinate),
                GeoParserRangeException::MINUTES_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_MINUTE, $coordinate),
                GeoParserRangeException::SECONDS_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_SECOND, $coordinate),
                default => $e->getMessage(),
            };

            throw new InvalidValueException($message, $e->getCode(), $e);
        } catch (UnexpectedValueException $e) {
            throw new InvalidValueException(sprintf('Invalid coordinate value, got "%s".', $coordinate), $e->getCode(), $e);
        }

        if (is_array($parsedCoordinate)) {
            throw new InvalidValueException('Invalid coordinate value, coordinate cannot be an array.');
        }

        return $parsedCoordinate;
    }

    /**
     * Set a cartesian coordinate.
     * Abscissa or ordinate.
     *
     * @param float|int|string $coordinate the coordinate to set
     *
     * @throws InvalidValueException when coordinate is invalid, RangeException is never thrown
     */
    protected function setCartesianCoordinate(float|int|string $coordinate): float|int
    {
        if (is_integer($coordinate) || is_float($coordinate)) {
            // We don't check the range of the value.
            return $coordinate;
        }

        // $y is a string, let's use the geo-parser.
        return $this->geoParse($coordinate);
    }

    /**
     * Set a geodesic coordinate.
     * Latitude or longitude.
     *
     * @param float|int|string $coordinate the coordinate to set
     * @param int              $min        the minimum value
     * @param int              $max        the maximum value
     *
     * @throws InvalidValueException|RangeException when coordinate is invalid or out of range
     */
    protected function setGeodesicCoordinate(float|int|string $coordinate, int $min, int $max): float|int
    {
        if (is_integer($coordinate) || is_float($coordinate)) {
            // We check the range of the value.
            return $this->checkRange($coordinate, $min, $max);
        }

        // $y is a string, let's use the geo-parser.
        $parsedCoordinate = $this->geoParse($coordinate);

        if ($parsedCoordinate < $min || $parsedCoordinate > $max) {
            throw new RangeException(sprintf('Coordinate must be comprised between %d and %d, got "%s".', $min, $max, $coordinate));
        }

        return $parsedCoordinate;
    }

    /**
     * Check the range of a coordinate.
     *
     * @param float|int $coordinate the coordinate to check
     * @param int       $min        the minimum accepted value
     * @param int       $max        the maximum accepted value
     *
     * @return float|int $coordinate or throw a RangeException
     *
     * @throws RangeException when coordinate is out of range fixed by min and max
     */
    private function checkRange(float|int $coordinate, int $min, int $max): float|int
    {
        if ($coordinate < $min || $coordinate > $max) {
            throw new RangeException(sprintf('Coordinate must be comprised between %d and %d, got "%s".', $min, $max, $coordinate));
        }

        return $coordinate;
    }
}
