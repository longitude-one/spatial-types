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

namespace LongitudeOne\SpatialTypes\Enum;

/**
 * Types Enum.
 *
 * This enumeration is used to define the type of spatial instance.
 */
enum TypeEnum: string
{
    /**
     * Collection type is used to create GEOMETRY_COLLECTION and GEOGRAPHY_COLLECTION.
     */
    case COLLECTION = 'Collection';

    /**
     * Geometry type is used to create GEOMETRY and GEOGRAPHY LineStrings.
     */
    case LINESTRING = 'LineString';

    /**
     * MultiLineString type is used to create GEOMETRY and GEOGRAPHY MultiLineStrings.
     */
    case MULTILINESTRING = 'MultiLineString';

    /**
     * MultiPoint type is used to create GEOMETRY and GEOGRAPHY MultiPoints.
     */
    case MULTIPOINT = 'MultiPoint';

    /**
     * MultiPolygon type is used to create GEOMETRY and GEOGRAPHY MultiPolygons.
     */
    case MULTIPOLYGON = 'MultiPolygon';

    /**
     * Point type is used to create GEOMETRY and GEOGRAPHY Points.
     */
    case POINT = 'Point';

    /**
     * Polygon type is used to create GEOMETRY and GEOGRAPHY Polygons.
     */
    case POLYGON = 'Polygon';

    /**
     * Return the homogeneous component type, if the spatial type has one.
     */
    public function componentType(): ?self
    {
        return match ($this) {
            self::LINESTRING, self::MULTIPOINT => self::POINT,
            self::POLYGON, self::MULTILINESTRING => self::LINESTRING,
            self::MULTIPOLYGON => self::POLYGON,
            self::COLLECTION, self::POINT => null,
        };
    }

    /**
     * Is this a multi-geometry type?
     */
    public function isMulti(): bool
    {
        return match ($this) {
            self::MULTILINESTRING, self::MULTIPOINT, self::MULTIPOLYGON => true,
            default => false,
        };
    }

    /**
     * Return the topological dimension, or null for a heterogeneous collection.
     */
    public function topologicalDimension(): ?int
    {
        return match ($this) {
            self::POINT, self::MULTIPOINT => 0,
            self::LINESTRING, self::MULTILINESTRING => 1,
            self::POLYGON, self::MULTIPOLYGON => 2,
            self::COLLECTION => null,
        };
    }
}
