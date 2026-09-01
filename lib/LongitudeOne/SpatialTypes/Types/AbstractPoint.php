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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\GeoParser\Exception\RangeException as GeoParserRangeException;
use LongitudeOne\GeoParser\Exception\UnexpectedValueException;
use LongitudeOne\GeoParser\Parser;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\RangeException;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract point object for POINT spatial types.
 *
 * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
 * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
 *
 * @internal this class provides common behaviour for geometry and geography points
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

    /** Whether this point corresponds to the empty set. */
    private bool $empty = false;

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

        if ($this->isEmpty() || $point->isEmpty()) {
            return $this->isEmpty() && $point->isEmpty();
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
     * Return the normalized coordinates of this point.
     */
    public function getCoordinates(): ?Coordinates
    {
        if ($this->isEmpty()) {
            return null;
        }

        return new Coordinates(
            $this->getDimension(),
            $this->x,
            $this->y,
            $this->hasZ() ? $this->getZ() : null,
            $this->hasM() ? $this->getM() : null
        );
    }

    /**
     * Latitude getter.
     */
    public function getLatitude(): float|int|null
    {
        return $this->getY();
    }

    /**
     * Longitude getter.
     */
    public function getLongitude(): float|int|null
    {
        return $this->getX();
    }

    /**
     * X getter. (Longitude getter).
     */
    public function getX(): float|int|null
    {
        return $this->isEmpty() ? null : $this->x;
    }

    /**
     * Y getter. Latitude getter.
     */
    public function getY(): float|int|null
    {
        return $this->isEmpty() ? null : $this->y;
    }

    /**
     * Does this point correspond to the empty set?
     */
    public function isEmpty(): bool
    {
        return $this->empty;
    }

    /**
     * Return a copy of this point with the supplied normalized coordinates.
     *
     * The coordinate dimension must match the concrete point type. The point's
     * family and Spatial Reference Identifier (SRID) are preserved.
     *
     * @param Coordinates $coordinates replacement coordinates
     *
     * @throws InvalidDimensionException when the coordinate dimension differs from this point's dimension
     */
    public function withCoordinates(Coordinates $coordinates): static
    {
        if ($this->getDimension() !== $coordinates->dimension) {
            throw new InvalidDimensionException(sprintf('The %s coordinates are incompatible with the %s point dimension.', $coordinates->dimension->value, $this->getDimension()->value));
        }

        $point = clone $this;
        $point->initializeX($coordinates->x);
        $point->initializeY($coordinates->y);

        if ($coordinates->dimension->hasZ()) {
            $point->initializeZ($coordinates->getZ());
        }

        if ($coordinates->dimension->hasM()) {
            $point->initializeM($coordinates->getM());
        }

        $point->empty = false;

        return $point;
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
            $messages = [
                GeoParserRangeException::LATITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LATITUDE, $coordinate),
                GeoParserRangeException::LONGITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LONGITUDE, $coordinate),
                GeoParserRangeException::MINUTES_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_MINUTE, $coordinate),
                GeoParserRangeException::SECONDS_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_SECOND, $coordinate),
            ];
            $message = $messages[$e->getCode()] ?? $e->getMessage();

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
     * Determine whether all coordinates describe an empty point.
     *
     * A point is empty only when every ordinate in its coordinate layout is
     * null. Supplying only some coordinates would produce an invalid point.
     *
     * @param (null|float|int|string) ...$coordinates Point ordinates
     *
     * @throws InvalidValueException when only a subset of ordinates is null
     */
    final protected function hasOnlyNullCoordinates(float|int|string|null ...$coordinates): bool
    {
        $nullCount = count(array_filter($coordinates, static fn (float|int|string|null $coordinate): bool => null === $coordinate));
        if (count($coordinates) === $nullCount) {
            $this->empty = true;

            return true;
        }

        if (0 !== $nullCount) {
            throw new InvalidValueException('All point coordinates must be provided, or all must be null for an empty point.');
        }

        return false;
    }

    /**
     * Latitude fluent setter.
     *
     * @param float|int|string $latitude the new latitude of point
     *
     * @throws InvalidValueException when latitude is not valid
     */
    protected function initializeLatitude(float|int|string $latitude): static
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
    protected function initializeLongitude(float|int|string $longitude): static
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
     * Set the M coordinate on a point that supports a measure ordinate.
     *
     * @param float|int $m M coordinate or measure
     *
     * @throws BadMethodCallException when the point has no M ordinate
     */
    protected function initializeM(float|int $m): static
    {
        // @codeCoverageIgnoreStart
        throw new BadMethodCallException(sprintf('The M ordinate "%s" cannot be assigned to a point with the %s dimension.', $m, $this->getDimension()->value));
        // @codeCoverageIgnoreEnd
    }

    /**
     * X setter. (Latitude setter).
     *
     * @param float|int|string $x the new X
     *
     * @throws InvalidValueException when x is not valid
     */
    protected function initializeX(float|int|string $x): static
    {
        if ($this->getFamily()->requiresGeographicCoordinateRanges()) {
            return $this->initializeLongitude($x);
        }

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
    protected function initializeY(float|int|string $y): static
    {
        if ($this->getFamily()->requiresGeographicCoordinateRanges()) {
            return $this->initializeLatitude($y);
        }

        $this->y = $this->setCartesianCoordinate($y);

        return $this;
    }

    /**
     * Set the Z coordinate on a point that supports an elevation ordinate.
     *
     * @param float|int $z Z coordinate or elevation
     *
     * @throws BadMethodCallException when the point has no Z ordinate
     */
    protected function initializeZ(float|int $z): static
    {
        // @codeCoverageIgnoreStart
        throw new BadMethodCallException(sprintf('The Z ordinate "%s" cannot be assigned to a point with the %s dimension.', $z, $this->getDimension()->value));
        // @codeCoverageIgnoreEnd
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

    /**
     * Convert point into an array of coordinates.
     * SRID is NOT exported.
     *
     * The array contains coordinate values only; it does not contain SpatialInterface instances.
     */
    abstract public function toArray(): array;
}
