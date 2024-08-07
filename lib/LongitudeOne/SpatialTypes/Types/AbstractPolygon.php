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
use LongitudeOne\SpatialTypes\Factory\FactoryLineString;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Trait\LineStringTrait;

abstract class AbstractPolygon extends AbstractSpatialType implements PolygonInterface
{
    use LineStringTrait;

    /**
     * AbstractPolygon constructor.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[]|LineStringInterface|PointInterface[])[] $rings rings of the polygon
     * @param null|int                                                                                                                                                $srid  Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the polygon dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the polygon SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $rings, ?int $srid = null)
    {
        $this->setSrid($srid);
        $this->addRings($rings);
    }

    /**
     * Get the line strings of the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[]|LineStringInterface|PointInterface[] $ring the ring to add
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition of the ring
     */
    public function addRing(array|LineStringInterface $ring): static
    {
        if (is_array($ring)) {
            $ring = FactoryLineString::fromIndexedArray($ring, $this->getSrid(), $this->getFamily(), $this->getDimension());
        }

        if (!$ring->isRing()) {
            throw new InvalidValueException('The line string is not a ring.');
        }

        if ($ring->getFamily() !== $this->getFamily()) {
            throw new InvalidFamilyException('The ring family is not compatible with the family of the current polygon.');
        }

        return $this->traitAddLineString($ring);
    }

    /**
     * Add a ring to the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[][]|LineStringInterface[]|PointInterface[][] $rings the ring to add
     *
     * @throws SpatialTypeExceptionInterface when one of the linestring is not a ring
     */
    public function addRings(array $rings): static
    {
        foreach ($rings as $ring) {
            if (!is_array($ring) && !$ring instanceof LineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing LineStringInterface or array of coordinates.');
            }

            $this->addRing($ring);
        }

        return $this;
    }

    /**
     * Get the elements of this polygon.
     *
     * @return LineStringInterface[]
     */
    public function getElements(): array
    {
        return $this->getRings();
    }

    /**
     * Get the ring of the polygon.
     *
     * @param int $index Index of the ring
     */
    public function getRing(int $index): LineStringInterface
    {
        if (empty($this->getRings())) {
            throw new OutOfBoundsException('The current collection of rings is empty.');
        }

        $index = $index % count($this->getRings());

        if ($index < 0) {
            $index = count($this->getRings()) + $index;
        }

        return $this->getRings()[$index];
    }

    /**
     * Get the rings of the spatial collection.
     *
     * @return LineStringInterface[]
     *
     * @throws InvalidValueException when at least one of the line strings is not a ring
     */
    public function getRings(): array
    {
        return $this->traitGetLineStrings();
    }

    /**
     * Return an array representation of the polygon.
     *
     * @return (\DateTimeInterface|float|int)[][][]
     */
    public function toArray(): array
    {
        $rings = $this->getRings();

        return array_map(
            static fn (LineStringInterface $ring) => $ring->toArray(),
            $rings
        );
    }
}
