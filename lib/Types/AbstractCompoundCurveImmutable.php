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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/**
 * Ordered curve storage.
 *
 * ISO/IEC CD 13249-3:201x(E), clauses 4.2.7 and 7.4.1.
 *
 * @internal
 */
abstract class AbstractCompoundCurveImmutable extends AbstractSpatialTypeImmutable implements ImmutableCompoundCurveInterface
{
    /** @var ImmutableCurveInterface[] */
    protected array $curves = [];

    /**
     * @param ImmutableCurveInterface[] $curves Ordered line-string or circular-string components
     * @param int|SpatialReference      $srid   Spatial reference
     */
    final public function __construct(array $curves = [], int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        foreach ($curves as $curve) {
            if (!$curve instanceof ImmutableCurveInterface) {
                throw new InvalidValueException('A compound curve requires ImmutableCurveInterface components.');
            }
            $this->addCurve($curve);
        }
    }

    /**
     * Return a component using the collection index convention.
     *
     * @param int $index Requested index
     */
    public function getCurve(int $index): ImmutableCurveInterface
    {
        if ([] === $this->curves) {
            throw new OutOfBoundsException('The current collection of curves is empty.');
        }
        $index %= count($this->curves);

        return $this->curves[$index < 0 ? count($this->curves) + $index : $index];
    }

    /** @return ImmutableCurveInterface[] */
    public function getCurves(): array
    {
        return $this->curves;
    }

    /** @return ImmutableCurveInterface[] */
    public function getElements(): array
    {
        return $this->getCurves();
    }

    /** Return the final component's endpoint, or null for EMPTY. */
    public function getEndPoint(): ?ImmutablePointInterface
    {
        return $this->isEmpty() ? null : $this->getCurve(-1)->getEndPoint();
    }

    /** Return the first component's start point, or null for EMPTY. */
    public function getStartPoint(): ?ImmutablePointInterface
    {
        return $this->isEmpty() ? null : $this->getCurve(0)->getStartPoint();
    }

    /** Return the compound-curve type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::COMPOUNDCURVE;
    }

    /** Whether the non-empty curve has equal endpoints. */
    public function isClosed(): bool
    {
        $start = $this->getStartPoint();
        $end = $this->getEndPoint();

        return null !== $start && null !== $end && $start->equalsTo($end);
    }

    /** Whether there are no components. */
    public function isEmpty(): bool
    {
        return [] === $this->curves;
    }

    /** @return (null|float|int)[][][] */
    public function toArray(): array
    {
        return array_map(
            fn (ImmutableCurveInterface $curve): array => $this->componentCoordinates($curve),
            $this->curves
        );
    }

    /**
     * Copy all components into the supplied reference without transformation.
     *
     * @param SpatialReference $reference New declared reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        return new static(array_map(
            static fn (ImmutableCurveInterface $curve): ImmutableCurveInterface => $curve->withSpatialReference($reference),
            $this->curves
        ), $reference);
    }

    /**
     * Append a typed component without altering its interpolation.
     *
     * @param ImmutableCurveInterface $curve Component to append
     */
    private function addCurve(ImmutableCurveInterface $curve): void
    {
        $this->componentCoordinates($curve);
        ImmutableComponentGuard::check($curve);
        $this->curves[] = $curve;
    }

    /**
     * Read the point tuples of a supported component without losing ordinates.
     *
     * @param ImmutableCurveInterface $curve Component whose coordinates are requested
     *
     * @return (null|float|int)[][]
     */
    private function componentCoordinates(ImmutableCurveInterface $curve): array
    {
        $coordinates = [];
        foreach ($curve->toArray() as $point) {
            if (!is_array($point)) {
                throw new InvalidValueException('A compound curve component must expose point coordinate tuples.');
            }
            $tuple = [];
            foreach ($point as $ordinate) {
                if (null !== $ordinate && !is_int($ordinate) && !is_float($ordinate)) {
                    throw new InvalidValueException('A compound curve component must expose numeric or null ordinates.');
                }
                $tuple[] = $ordinate;
            }
            $coordinates[] = $tuple;
        }

        return $coordinates;
    }
}
