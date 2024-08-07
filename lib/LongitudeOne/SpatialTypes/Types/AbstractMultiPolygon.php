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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Factory\FactoryPolygon;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;

/**
 * Multi polygon abstract class.
 */
abstract class AbstractMultiPolygon extends AbstractSpatialType implements MultiPolygonInterface
{
    /**
     * @var PolygonInterface[] Polygons
     */
    private array $polygons = [];

    /**
     * AbstractPolygon constructor.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[][]|LineStringInterface[]|PointInterface[][]|PolygonInterface)[] $polygons polygons
     * @param null|int                                                                                                                                                                       $srid     Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the polygon dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the polygon SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $polygons, ?int $srid = null)
    {
        $this->preConstruct();
        $this->setSrid($srid);
        $this->addPolygons($polygons);
    }

    /**
     * Get the line strings of the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[][]|LineStringInterface[]|PointInterface[][]|PolygonInterface $polygon polygon
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the polygon
     */
    public function addPolygon(array|PolygonInterface $polygon): static
    {
        if (is_array($polygon)) {
            $polygon = FactoryPolygon::fromIndexedArray($polygon, $this->getSrid(), $this->getFamily(), $this->getDimension());
        }

        if (!empty($polygon->getSrid()) && !empty($this->getSrid()) && $polygon->getSrid() !== $this->getSrid()) {
            throw new InvalidSridException('The polygon SRID is not compatible with the SRID of the current multipolygon.');
        }

        if ($polygon->getFamily() !== $this->getFamily()) {
            throw new InvalidFamilyException('The polygon family is not compatible with the family of the current multipolygon.');
        }

        if (!$polygon->hasSameDimension($this)) {
            throw new InvalidDimensionException('The polygon is not compatible with the dimension of the current multipolygon.');
        }

        $this->polygons[] = $polygon;

        return $this;
    }

    /**
     * Add polygons to the multipolygon instance.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[][]|LineStringInterface[]|PointInterface[][]|PolygonInterface)[] $polygons polygons
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the polygons
     */
    public function addPolygons(array $polygons): static
    {
        foreach ($polygons as $polygon) {
            $this->addPolygon($polygon);
        }

        return $this;
    }

    /**
     * Get the elements of this polygon.
     *
     * @return PolygonInterface[]
     */
    public function getElements(): array
    {
        return $this->getPolygons();
    }

    /**
     * Get the ring of the polygon.
     *
     * @param int $index Index of the ring
     *
     * @throws OutOfBoundsException when the multipolygon is empty
     */
    public function getPolygon(int $index): PolygonInterface
    {
        if (empty($this->getPolygons())) {
            throw new OutOfBoundsException('The current collection of polygons is empty.');
        }

        $index = $index % count($this->getPolygons());

        if ($index < 0) {
            $index = count($this->getPolygons()) + $index;
        }

        return $this->getPolygons()[$index];
    }

    /**
     * Get the rings of the spatial collection.
     *
     * @return PolygonInterface[]
     *
     * @throws InvalidValueException when at least one of the line strings is not a ring
     */
    public function getPolygons(): array
    {
        return $this->polygons;
    }

    /**
     * Is MultiPolygon empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->getPolygons());
    }

    /**
     * Return an array representation of the polygon.
     *
     * @return (\DateTimeInterface|float|int)[][][][]
     */
    public function toArray(): array
    {
        $polygons = $this->getPolygons();

        return array_map(
            static fn (PolygonInterface $polygon) => $polygon->toArray(),
            $polygons
        );
    }
}
