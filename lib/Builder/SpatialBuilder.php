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

namespace LongitudeOne\SpatialTypes\Builder;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\BuilderAdditionTrait;
use LongitudeOne\SpatialTypes\Internal\BuilderBuildTrait;
use LongitudeOne\SpatialTypes\Internal\BuilderConfigurationTrait;
use LongitudeOne\SpatialTypes\Internal\BuilderDirectCreationTrait;
use LongitudeOne\SpatialTypes\Internal\BuilderInternalsTrait;
use LongitudeOne\SpatialTypes\Internal\BuilderMoreDirectCreationTrait;
use LongitudeOne\SpatialTypes\Internal\BuilderMoreSelectionTrait;
use LongitudeOne\SpatialTypes\Internal\BuilderSelectionTrait;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Reusable, locally configured spatial creation context. */
final class SpatialBuilder
{
    use BuilderAdditionTrait;
    use BuilderBuildTrait;
    use BuilderConfigurationTrait;
    use BuilderDirectCreationTrait;
    use BuilderInternalsTrait;
    use BuilderMoreDirectCreationTrait;
    use BuilderMoreSelectionTrait;
    use BuilderSelectionTrait;

    /** @var null|'CircularString'|'Collection'|'CompoundCurve'|'LineString'|'MultiLineString'|'MultiPoint'|'MultiPolygon'|'Polygon'|'PolyhedralSurface'|'Triangle' */
    protected ?string $aggregate = null;

    /** @var list<mixed> */
    protected array $components = [];

    /**
     * Initialize the builder context.
     *
     * @param bool                    $isMutable           isMutable value
     * @param CoordinateDimensionEnum $coordinateDimension coordinateDimension value
     * @param SpatialModelEnum        $spatialFamily       spatialFamily value
     * @param SpatialReference        $reference           reference value
     */
    private function __construct(
        private bool $isMutable = false,
        private CoordinateDimensionEnum $coordinateDimension = CoordinateDimensionEnum::XY,
        private SpatialModelEnum $spatialFamily = SpatialModelEnum::GEOMETRY,
        private SpatialReference $reference = new SpatialReference(0)
    ) {
    }

    /** Start an immutable XY Geometry context with SRID 0. */
    /**
     * Create.
     */
    public static function create(): self
    {
        return new self();
    }
}
