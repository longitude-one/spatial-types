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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableCircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Mutable ordered curve storage without geometric validation.
 * @internal
 */
abstract class AbstractCompoundCurve extends AbstractSpatialType implements MutableCompoundCurveInterface
{
    /** @var list<MutableCircularStringInterface|MutableLineStringInterface> */
    protected array $curves = [];

    protected SpatialModelEnum $family;

    /**
     * @param array<MutableCircularStringInterface|MutableLineStringInterface> $curves Ordered mutable components
     * @param int|SpatialReference                                             $srid   Declared reference
     * @param SpatialModelEnum                                                 $family Declared family
     */
    final public function __construct(array $curves = [], int|SpatialReference $srid = 0, SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)
    {
        $this->family = $family;
        $this->initializeSpatialReference($srid);
        $this->setCurves($curves);
    }

    /** @param MutableCircularStringInterface|MutableLineStringInterface $curve Component retained without cloning */
    public function addCurve(MutableCircularStringInterface|MutableLineStringInterface $curve): static
    {
        $this->curves[] = $curve;

        return $this;
    }

    /** @param int $index Existing zero-based component index */
    public function getCurve(int $index): MutableCircularStringInterface|MutableLineStringInterface
    {
        $this->requireCurveIndex($index);

        return $this->curves[$index];
    }

    /** @return list<MutableCircularStringInterface|MutableLineStringInterface> */
    public function getCurves(): array
    {
        return $this->curves;
    }

    /** @return list<MutableCircularStringInterface|MutableLineStringInterface> */
    public function getElements(): array
    {
        return $this->curves;
    }

    /** Return the final component's endpoint, or null. */
    public function getEndPoint(): ?MutablePointInterface
    {
        return $this->isEmpty() ? null : $this->curves[count($this->curves) - 1]->getEndPoint();
    }

    /** Return the declared family. */
    public function getFamily(): SpatialModelEnum
    {
        return $this->family;
    }

    /** Return the first component's start point, or null. */
    public function getStartPoint(): ?MutablePointInterface
    {
        return $this->isEmpty() ? null : $this->curves[0]->getStartPoint();
    }

    /** Return the fixed spatial type. */
    public function getType(): GeometryTypeEnum
    {
        return GeometryTypeEnum::COMPOUNDCURVE;
    }

    /**
     * @param int                                                       $index Zero-based insertion index, up to the component count
     * @param MutableCircularStringInterface|MutableLineStringInterface $curve Component retained without cloning
     */
    public function insertCurve(int $index, MutableCircularStringInterface|MutableLineStringInterface $curve): static
    {
        if ($index < 0 || $index > count($this->curves)) {
            throw new OutOfBoundsException(sprintf('Curve index %d is outside the insertion range.', $index));
        }
        array_splice($this->curves, $index, 0, [$curve]);

        return $this;
    }

    /** Whether the first and final endpoints are present and equal. */
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

    /** @param int $index Existing zero-based component index */
    public function removeCurve(int $index): static
    {
        $this->requireCurveIndex($index);
        array_splice($this->curves, $index, 1);

        return $this;
    }

    /**
     * @param int                                                       $index Existing zero-based component index
     * @param MutableCircularStringInterface|MutableLineStringInterface $curve Component retained without cloning
     */
    public function setCurve(int $index, MutableCircularStringInterface|MutableLineStringInterface $curve): static
    {
        $this->requireCurveIndex($index);
        array_splice($this->curves, $index, 1, [$curve]);

        return $this;
    }

    /** @param array<MutableCircularStringInterface|MutableLineStringInterface> $curves Complete ordered replacement sequence */
    public function setCurves(array $curves): static
    {
        $checked = [];
        foreach ($curves as $curve) {
            if (!$curve instanceof MutableLineStringInterface && !$curve instanceof MutableCircularStringInterface) {
                throw new InvalidValueException('A mutable compound curve requires mutable line-string or circular-string components.');
            }
            $checked[] = $curve;
        }
        $this->curves = $checked;

        return $this;
    }

    /**
     * @param SpatialModelEnum $family    Declared family
     * @param bool             $propagate Whether to update components and their points
     */
    public function setFamily(SpatialModelEnum $family, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->curves as $curve) {
                $curve->setFamily($family);
            }
        }
        $this->family = $family;

        return $this;
    }

    /**
     * @param SpatialReference $reference Declared reference
     * @param bool             $propagate Whether to update components and their points
     */
    public function setSpatialReference(SpatialReference $reference, bool $propagate = true): static
    {
        if ($propagate) {
            foreach ($this->curves as $curve) {
                $curve->setSpatialReference($reference);
            }
        }
        $this->spatialReference = $reference;

        return $this;
    }

    /**
     * @param int  $srid      Declared reference identifier
     * @param bool $propagate Whether to update components and their points
     */
    public function setSrid(int $srid, bool $propagate = true): static
    {
        return $this->setSpatialReference(SpatialReference::fromSrid($srid), $propagate);
    }

    /** @return (null|float|int)[][][] */
    public function toArray(): array
    {
        return array_map(static fn (MutableCircularStringInterface|MutableLineStringInterface $curve): array => $curve->toArray(), $this->curves);
    }

    /** @param int $index Existing zero-based component index */
    private function requireCurveIndex(int $index): void
    {
        if ($index < 0 || $index >= count($this->curves)) {
            throw new OutOfBoundsException(sprintf('Curve index %d does not address an existing curve.', $index));
        }
    }
}
