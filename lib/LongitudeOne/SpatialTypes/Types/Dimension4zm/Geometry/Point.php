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

namespace LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractPoint;

class Point extends AbstractPoint implements PointInterface
{
    protected float|int $m;
    protected float|int $z;

    public function __construct(float|int|string $x, float|int|string $y, float|int $z, float|int $m, ?int $srid = null)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);
        $this->setM($m);
        $this->srid = $srid;
    }

    public function getFamily(): FamilyEnum
    {
        return FamilyEnum::GEOMETRY;
    }

    public function getM(): float|int
    {
        return $this->m;
    }

    public function getType(): TypeEnum
    {
        return TypeEnum::POINT;
    }

    public function getZ(): float|int
    {
        return $this->z;
    }

    public function setM(float|int $m): static
    {
        $this->m = $m;

        return $this;
    }

    public function setZ(float|int $z): static
    {
        $this->z = $z;

        return $this;
    }

    public function toArray(): array
    {
        return [$this->x, $this->y, $this->z, $this->m];
    }

    protected function getDimension(): DimensionEnum
    {
        return DimensionEnum::X_Y_Z_M;
    }
}
