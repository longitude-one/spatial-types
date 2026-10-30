# Spatial Types Library

PHP library providing spatial types and their geometric and geographic classes.

If you want to persist spatial data in a database,
you should use the [longitude-one/doctrine2-spatial](https://github.com/longitude-one/doctrine2-spatial) package.

## Current status

![longitude-one/spatial--types](https://img.shields.io/badge/longitude--one-spatial--types-blue)
![Stable release](https://img.shields.io/github/v/release/longitude-one/spatial-types)
![Minimum PHP Version](https://img.shields.io/packagist/php-v/longitude-one/spatial-types.svg?maxAge=3600)
[![Packagist License](https://img.shields.io/packagist/l/longitude-one/spatial-types)](https://github.com/longitude-one/spatial-types/blob/main/LICENSE)

[![Oldest PHP test](https://github.com/longitude-one/spatial-types/actions/workflows/php-oldest.yaml/badge.svg)](https://github.com/longitude-one/spatial-types/actions/workflows/php-oldest.yaml)
[![Latest PHP test](https://github.com/longitude-one/spatial-types/actions/workflows/php-latests.yaml/badge.svg)](https://github.com/longitude-one/spatial-types/actions/workflows/php-latests.yaml)
[![Downloads](https://img.shields.io/packagist/dm/longitude-one/spatial-types.svg)](https://packagist.org/packages/longitude-one/spatial-types)
[![Coverage Status](https://coveralls.io/repos/github/longitude-one/spatial-types/badge.svg?branch=main)](https://coveralls.io/github/longitude-one/spatial-types?branch=main)

## Installation

```bash
composer require longitude-one/spatial-types
```

## Usage

Concrete immutable spatial classes have an `Immutable` suffix and are final. Update
imports and constructor calls to the new names; former names have no aliases.
Read-only interfaces remain shared. See [Immutability](docs/immutability.md#read-copy-and-mutation-contracts)
for the separate immutable and mutable contracts.
[Public spatial factories](docs/public-factories.md) create either representation
through one shared mode, which defaults to immutable.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;

$point = new PointImmutable(1, 2);
echo $point->getX(); // 1
echo $point->getY(); // 2

$lineString = new LineStringImmutable([
    new PointImmutable(1, 2, 4326),
    new PointImmutable(3, 4, 4326),
    new PointImmutable(5, 6, 4326),
], 4326);
$lineString->getSrid(); // 4326
```

For the complete catalogue of concrete types, constructors, accessors, and
mutability rules, see [Instantiable spatial types](docs/instantiable-spatial-types.md).
See [Public and internal API](docs/public-api.md) for the supported API boundary
and the complete symbol inventory.

## Type class hierarchy

The following tree describes class inheritance in `Types`, rather than the
file layout. Intermediate abstract classes are hidden; only
`AbstractSpatialType` and instantiable classes are shown. `{Dimension2,
Dimension3m, Dimension3z, Dimension4zm}` represents one branch for each listed
coordinate dimension and `{Geometry, Geography}` one concrete class for each
spatial model. For the complete hierarchy, including intermediate abstract
classes, see [Type class hierarchy](docs/type-class-hierarchy.md).

```text
AbstractSpatialType
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\PointImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\LineStringImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\PolygonImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\CircularStringImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\CompoundCurveImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\TriangleImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiPointImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiLineStringImmutable
├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiPolygonImmutable
├── Dimension{3z,4zm}\{Geometry,Geography}\PolyhedralSurfaceImmutable
├── Dimension{2,3m,3z,4zm}\Geometry\GeometryCollectionImmutable
└── Dimension{2,3m,3z,4zm}\Geography\GeographyCollectionImmutable
```

### Types not yet available

`CurvePolygon`, `MultiCurve`,
`MultiSurface` and `TIN` do not yet have
instantiable classes. See [Instantiable spatial types](docs/instantiable-spatial-types.md)
for the complete coverage matrix.

## Immutability

Immutable spatial values are safe to share: their coordinates, SRID, and
aggregate membership are set at construction time and never changed through
the public API. Use `withCoordinates()` with an immutable `Value\Coordinates`
value to represent another location, or `withSpatialReference()` to associate
the same coordinates with another declared reference. Both return new objects.

`LineStringImmutable`, `PolygonImmutable`, `TriangleImmutable`, and `PolyhedralSurfaceImmutable` also provide
`withArrayOfCoordinates()` to create a new instance with replacement
coordinates. This preserves their family, dimension, and SRID without changing
the source object.

This prevents a value shared by other aggregates from silently changing them.
See [Immutability](docs/immutability.md) for the rationale, examples, and the
complete contract.

## Spatial reference system (SRS)

Every spatial object has a `Reference\SpatialReference`. Its legacy integer
projection remains available through `getSrid()`, while `getSpatialReference()`
preserves the optional authority (for example `EPSG:4326`). Constructors and
factories accept either a legacy integer or a `SpatialReference` instance.

Supplied members retain their declared references; compatibility is not
validated automatically. Use `withSpatialReference()` (or the legacy
`withSrid()`) to relabel a copy and its descendants without reprojection.

See [Spatial reference systems](docs/spatial-reference-systems.md) for reference
handling, and [Spatial validation](docs/validators.md) for the separation of
representation from explicit functional validation.
