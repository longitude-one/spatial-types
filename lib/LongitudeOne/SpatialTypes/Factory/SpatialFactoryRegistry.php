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

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\Internal\LineStringFactoryInterface;
use LongitudeOne\SpatialTypes\Factory\Internal\PointFactoryInterface;
use LongitudeOne\SpatialTypes\Factory\Internal\PolygonFactoryInterface;

/**
 * Registry of constructors associated with spatial families.
 *
 * @internal this registry supports the internal factory pipeline
 */
final readonly class SpatialFactoryRegistry
{
    /**
     * @var array<string, FamilyFactories>
     */
    private array $familyFactories;

    /**
     * @param FamilyFactories ...$familyFactories constructors grouped by family
     */
    public function __construct(FamilyFactories ...$familyFactories)
    {
        $registeredFactories = [];
        foreach ($familyFactories as $factories) {
            if (isset($registeredFactories[$factories->family->name])) {
                throw new InvalidValueException(sprintf('Factories are already registered for the %s family.', $factories->family->value));
            }

            $registeredFactories[$factories->family->name] = $factories;
        }

        $this->familyFactories = $registeredFactories;
    }

    /**
     * Return the line string constructor for a family.
     */
    public function lineStringFactory(FamilyEnum $family): LineStringFactoryInterface
    {
        return $this->forFamily($family)->lineStringFactory;
    }

    /**
     * Return the point constructor for a family.
     */
    public function pointFactory(FamilyEnum $family): PointFactoryInterface
    {
        return $this->forFamily($family)->pointFactory;
    }

    /**
     * Return the polygon constructor for a family.
     */
    public function polygonFactory(FamilyEnum $family): PolygonFactoryInterface
    {
        return $this->forFamily($family)->polygonFactory;
    }

    /**
     * Return constructors registered for a family.
     *
     * @throws InvalidValueException when no constructor is registered for the family
     */
    private function forFamily(FamilyEnum $family): FamilyFactories
    {
        if (!isset($this->familyFactories[$family->name])) {
            throw new InvalidValueException(sprintf('No factories are registered for the %s family.', $family->value));
        }

        return $this->familyFactories[$family->name];
    }
}
