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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\DefaultSpatialFactoryFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Validator\DimensionValidation;
use LongitudeOne\SpatialTypes\Validator\FamilyValidation;
use LongitudeOne\SpatialTypes\Validator\SpatialReferenceValidation;

/**
 * Abstract Spatial Type class.
 *
 * @internal This class is internal. It is used to create geometry or geography objects.
 */
abstract class AbstractSpatialType implements SpatialInterface
{
    /** Spatial reference system associated with this spatial value. */
    protected SpatialReference $spatialReference;

    /**
     * Return the spatial-reference identity.
     */
    public function getSpatialReference(): SpatialReference
    {
        return $this->spatialReference;
    }

    /**
     * SRID getter.
     */
    public function getSrid(): int
    {
        return $this->spatialReference->srid();
    }

    /**
     * Does this object (or point of this object) have an M coordinate?
     */
    public function hasM(): bool
    {
        return $this->getDimension()->hasM();
    }

    /**
     * Does this object have the same dimension as the other object?
     *
     * @param SpatialInterface $spatial the other object
     */
    public function hasSameDimension(SpatialInterface $spatial): bool
    {
        return !(($this->hasM() ^ $spatial->hasM()) || ($this->hasZ() ^ $spatial->hasZ()));
    }

    /**
     * Does this object (or point of this object) have a Z coordinate?
     */
    public function hasZ(): bool
    {
        return $this->getDimension()->hasZ();
    }

    /**
     * Define elements for the JSON serialization.
     *
     * @return array{type: string, coordinates: (float|int)[]|(float|int)[][]|(float|int)[][][]|(float|int)[][][][]|SpatialInterface[], srid: ?int}
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType()->value,
            'coordinates' => $this->toArray(),
            'srid' => $this->getSrid(),
        ];
    }

    /**
     * Return a copy declared with the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $spatial = clone $this;
        $spatial->spatialReference = $reference;

        return $spatial;
    }

    /**
     * Return a copy of this spatial object with the given Spatial Reference Identifier (SRID).
     *
     * Aggregate spatial types override this method to copy their contained
     * elements with the same SRID.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Require a member to use the same coordinate dimension.
     *
     * @param SpatialInterface $spatial Member to validate
     * @param string           $message Exception message
     */
    final protected function assertSameDimension(SpatialInterface $spatial, string $message): void
    {
        DimensionValidation::assertSame($this->getDimension(), $spatial, $message);
    }

    /**
     * Require a member to belong to the same spatial family.
     *
     * @param SpatialInterface $spatial Member to validate
     * @param string           $message Exception message
     */
    final protected function assertSameFamily(SpatialInterface $spatial, string $message): void
    {
        FamilyValidation::assertSame($this->getFamily(), $spatial, $message);
    }

    /**
     * Require a member to belong to the same spatial reference system.
     *
     * ISO/IEC 13249-3 requires every member of a geometry collection to have
     * the collection's spatial reference system. SRID 0 is therefore a real,
     * albeit unnamed, reference and is not a wildcard.
     *
     * @param SpatialInterface $spatial Member to validate
     * @param string           $member  Member type for the error message
     */
    final protected function assertSameSpatialReference(SpatialInterface $spatial, string $member): void
    {
        SpatialReferenceValidation::assertSame($this->spatialReference, $spatial, $member);
    }

    /**
     * Hydrate a line-string tuple collection in this spatial value's context.
     *
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface> $coordinates Point tuples
     */
    final protected function createLineStringFromCoordinates(array $coordinates): LineStringInterface
    {
        return DefaultSpatialFactoryFactory::create()->createLineStringFromIndexedArray(
            $coordinates,
            new SpatialContext($this->getSpatialReference(), $this->getFamily(), $this->getDimension())
        );
    }

    /**
     * Hydrate a point tuple in this spatial value's complete context.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $coordinates Point tuple
     */
    final protected function createPointFromCoordinates(array $coordinates): PointInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPointFromIndexedArray(
            $coordinates,
            new SpatialContext($this->getSpatialReference(), $this->getFamily(), $this->getDimension())
        );
    }

    /**
     * Hydrate polygon rings in this spatial value's context.
     *
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface>|LineStringInterface> $rings Polygon rings
     */
    final protected function createPolygonFromCoordinates(array $rings): PolygonInterface
    {
        return DefaultSpatialFactoryFactory::create()->createPolygonFromIndexedArray(
            $rings,
            new SpatialContext($this->getSpatialReference(), $this->getFamily(), $this->getDimension())
        );
    }

    /**
     * Initialize the reference system from the typed API or its legacy SRID adapter.
     *
     * @param int|SpatialReference $reference Typed reference or legacy SRID
     */
    final protected function initializeSpatialReference(int|SpatialReference $reference): void
    {
        $this->spatialReference = $reference instanceof SpatialReference
            ? $reference
            : SpatialReference::fromSrid($reference);
    }

    /**
     * Dimension getter.
     */
    abstract protected function getDimension(): CoordinateDimensionEnum;

    /**
     * Family getter.
     *
     * @return SpatialModelEnum the family of the object (Geometry, Geography)
     */
    abstract public function getFamily(): SpatialModelEnum;

    /**
     * Type getter.
     */
    abstract public function getType(): GeometryTypeEnum;

    /**
     * Does this spatial object correspond to the empty set?
     */
    abstract public function isEmpty(): bool;

    /**
     * Convert any spatial object to its array representation.
     *
     * @return (float|int)[]|(float|int)[][]|(float|int)[][][]|(float|int)[][][][]|SpatialInterface[]
     */
    abstract public function toArray(): array;
}
