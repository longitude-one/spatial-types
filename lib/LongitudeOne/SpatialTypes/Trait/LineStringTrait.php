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

namespace LongitudeOne\SpatialTypes\Trait;

use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Factory\FactoryLineString;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;

trait LineStringTrait
{
    /**
     * Line strings.
     *
     * @var LineStringInterface[]
     */
    private array $lineStrings = [];

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
            $this->addRing($ring);
        }

        return $this;
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
        return $this->lineStrings;
    }

    /**
     * Add a line string to the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[]|LineStringInterface|PointInterface[] $lineString line string to add
     *
     * @throws InvalidValueException when the line string dimension is not compatible with the current dimension
     */
    protected function traitAddLineString(array|LineStringInterface $lineString): static
    {
        if (is_array($lineString)) {
            $lineString = FactoryLineString::fromIndexedArray($lineString, $this->getSrid(), $this->getFamily(), $this->getDimension());
        }

        if (!$lineString->hasSameDimension($this)) {
            throw new InvalidValueException('The line string dimension is not compatible with the dimension of the current linestring collection.');
        }

        if ($lineString->getFamily() !== $this->getFamily()) {
            throw new InvalidValueException('The line string family is not compatible with the family of the current linestring collection.');
        }

        if (!empty($lineString->getSrid()) && !empty($this->getSrid()) && $lineString->getSrid() !== $this->getSrid()) {
            throw new InvalidSridException('The point SRID is not compatible with the SRID of this current spatial collection.');
        }

        $this->lineStrings[] = $lineString;

        return $this;
    }

    /**
     * Add line strings to the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[][]|LineStringInterface[]|PointInterface[][] $lineStrings the ring to add
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition
     */
    protected function traitAddLineStrings(array $lineStrings): static
    {
        foreach ($lineStrings as $lineString) {
            $this->traitAddLineString($lineString);
        }

        return $this;
    }

    /**
     * Get the line strings of the spatial collection.
     *
     * @return LineStringInterface[]
     */
    protected function traitGetLineStrings(): array
    {
        return $this->lineStrings;
    }
}
