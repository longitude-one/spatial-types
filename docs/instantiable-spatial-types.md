# Instantiable spatial types

This reference documents the public, concrete spatial value types provided by
`longitude-one/spatial-types`. It covers their namespaces, direct constructors,
observable API, and mutation semantics.

## Standards and model

The library follows the spatial-object model established by the
[OGC Simple Features Access standard](https://www.ogc.org/standards/sfa/) and
the `ST_Geometry` hierarchy of SQL/MM Spatial (ISO/IEC 13249-3). In practice,
that model supplies the instantiable `Point`, `LineString`, `Polygon`,
`Triangle`, `PolyhedralSurface`, `MultiPoint`, `MultiLineString`, `MultiPolygon`, and collection types.

### GeometryTypeEnum implementation status

Use `Implementation\SpatialTypeImplementationStatus::isFullyImplemented()` to
query library support without depending on concrete class names or namespaces.
The API accepts a `LongitudeOne\Core\Enum\GeometryTypeEnum` and returns a
boolean for instantiable types. Instantiability is defined by
`GeometryTypeEnum::isInstantiable()` in `spatial-core`; implementation status
belongs to `spatial-types`.

```php
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Implementation\SpatialTypeImplementationStatus;

SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POINT); // true
SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::TIN); // false
SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POLYHEDRALSURFACE); // true

try {
    SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::GEOMETRY);
} catch (InvalidValueException $exception) {
    // Implementation status is not applicable to non-instantiable GeometryTypeEnum::GEOMETRY.
}
```

The following matrix covers every current enum case. **Not applicable** means
that the request throws `InvalidValueException`, identifying the enum case and
its non-instantiable status. It never returns `false` for an abstract type.
**No** means that an instantiable type is not yet fully implemented.

If a future instantiable enum case has no explicit classification, the API throws
`LongitudeOne\SpatialTypes\Exception\LogicException` with the case name.
This exception extends PHP's `LogicException` and implements
`SpatialTypeExceptionInterface`; it identifies an incomplete library
classification, rather than an unsupported spatial type. A test iterates all
enum cases to ensure this exception is not raised for the current model.

| `GeometryTypeEnum` | Instantiable | Fully implemented by `spatial-types` |
| --- | --- | --- |
| `GEOMETRY` | No | Not applicable |
| `POINT` | Yes | Yes |
| `CURVE` | No | Not applicable |
| `LINESTRING` | Yes | Yes |
| `CIRCULARSTRING` | Yes | Yes |
| `COMPOUNDCURVE` | Yes | Yes |
| `SURFACE` | No | Not applicable |
| `CURVEPOLYGON` | Yes | No |
| `POLYGON` | Yes | Yes |
| `TRIANGLE` | Yes | Yes |
| `POLYHEDRALSURFACE` | Yes | Yes |
| `TIN` | Yes | No |
| `GEOMETRYCOLLECTION` | Yes | Yes |
| `MULTIPOINT` | Yes | Yes |
| `MULTICURVE` | Yes | No |
| `MULTILINESTRING` | Yes | Yes |
| `MULTISURFACE` | Yes | No |
| `MULTIPOLYGON` | Yes | Yes |
| `SOLID` | No | Not applicable |
| `BREPSOLID` | Yes | No |
| `CIRCLE` | Yes | No |
| `CLOTHOID` | Yes | No |
| `COMPOUNDSURFACE` | Yes | No |
| `ELLIPTICALCURVE` | Yes | No |
| `GEODESICSTRING` | Yes | No |
| `NURBSCURVE` | Yes | No |
| `SPIRALCURVE` | Yes | No |

Full implementation is evaluated over the layouts applicable to the geometry
type, not over every layout in the library. `POLYHEDRALSURFACE` is fully
implemented for this API: both families provide XYZ and XYZM variants. This
does not add XY or XYM variants. The other eight implemented types are
available in both families for XY, XYZ, XYM and XYZM.

This API requires `spatial-core` 1.3 or newer within the 1.x series. The
library's minimum dependency has accordingly increased from 1.2 to 1.3.
The eight instantiable types added in `spatial-core` 1.3 are explicitly
classified as not yet implemented.
No existing spatial type or supported coordinate layout changes.

`ST_SpatialRefSys` is an SQL/MM spatial-reference-system metadata type rather
than a subtype of `ST_Geometry`; it is outside this value-type hierarchy. This
library identifies a reference with `Reference\SpatialReference`, which can
carry an authority and its integer identifier, without modelling a full
reference-system definition. The supplied ISO/IEC CD 13249-3:201x(E), section
8.5, names the polyhedral surface type `ST_PolyhdrlSurface`.

The `Geography` family is a library-level counterpart to the `Geometry` family;
it is not a separate `ST_Geography` branch in the SQL/MM hierarchy.

The library distinguishes two coordinate families:

- **Geometry** uses Cartesian `X, Y` coordinates. Numeric values are not range
  limited by this library.
- **Geography** uses geodetic `longitude, latitude` coordinates, still ordered
  as `X, Y`. Numeric longitude/latitude ranges are not checked automatically.

The coordinate layouts are `XY`, `XYZ`, `XYM`, and `XYZM`; their tuple order is
always `X, Y[, Z][, M]`. `Z` is elevation and `M` is a measure. In particular,
an `XYM` tuple is `[x, y, m]`, whereas an `XYZ` tuple is `[x, y, z]`.

Every spatial object has a `SpatialReference`; `getSrid()` exposes its legacy
integer identifier. Constructors without a reference use identifier `0`, as
prescribed for SQL/MM constructors without an SRID. Supplied objects retain
their own references; aggregate compatibility is not checked automatically.

See [Spatial reference systems](spatial-reference-systems.md) for reference
handling and [Spatial validation](validators.md) for explicit functional checks.

## Class catalogue

All classes in the following matrix are final, concrete and immutable. The class
name is composed from the dimension, family, and type:

```php
LongitudeOne\SpatialTypes\Types\<dimension>\<family>\<type>Immutable
```

| Dimension         | Namespace segment | Coordinates  |
| ----------------- | ----------------- | ------------ |
| 2D                | `Dimension2`      | `X, Y`       |
| 3D with elevation | `Dimension3z`     | `X, Y, Z`    |
| 3D with measure   | `Dimension3m`     | `X, Y, M`    |
| 4D                | `Dimension4zm`    | `X, Y, Z, M` |

For each dimension, both `Geometry` and `Geography` provide:

| Type                     | Geometry class                  | Geography class                   |
| ------------------------ | ------------------------------- | --------------------------------- |
| Point                    | `…\Geometry\PointImmutable`              | `…\Geography\PointImmutable`               |
| Line string              | `…\Geometry\LineStringImmutable`         | `…\Geography\LineStringImmutable`          |
| Circular string          | `…\Geometry\CircularStringImmutable` | `…\Geography\CircularStringImmutable` |
| Compound curve           | `…\Geometry\CompoundCurveImmutable` | `…\Geography\CompoundCurveImmutable` |
| Polygon                  | `…\Geometry\PolygonImmutable`            | `…\Geography\PolygonImmutable`             |
| Triangle                 | `…\Geometry\TriangleImmutable`           | `…\Geography\TriangleImmutable`            |
| Multi-point              | `…\Geometry\MultiPointImmutable`         | `…\Geography\MultiPointImmutable`          |
| Multi-line string        | `…\Geometry\MultiLineStringImmutable`    | `…\Geography\MultiLineStringImmutable`     |
| Multi-polygon            | `…\Geometry\MultiPolygonImmutable`       | `…\Geography\MultiPolygonImmutable`        |
| Heterogeneous collection | `…\Geometry\GeometryCollectionImmutable` | `…\Geography\GeographyCollectionImmutable` |

`PolyhedralSurfaceImmutable` is additionally available in `Dimension3z` and
`Dimension4zm`, in both families. Its faces require Z; XY and XYM variants are
not provided. It is a surface, not a heterogeneous collection or a solid.

For example, a four-dimensional geographic polygon is
`LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable`.

`AbstractSpatialType` and the other `Abstract*` classes are internal base
classes, not part of the instantiable API.

## Direct construction

The selected namespace determines both the coordinate dimension and family.
The optional `$srid` argument accepts `int|SpatialReference` and defaults to
the unnamed reference identified by `0` in every constructor.

### Point

```php
new PointImmutable($x, $y, int|SpatialReference $srid = 0);                 // Dimension2
new PointImmutable($x, $y, $z, int|SpatialReference $srid = 0);             // Dimension3z
new PointImmutable($x, $y, $m, int|SpatialReference $srid = 0);             // Dimension3m
new PointImmutable($x, $y, $z, $m, int|SpatialReference $srid = 0);         // Dimension4zm
```

`$x` and `$y` accept `int`, `float`, or a coordinate string accepted by the
geo-parser. `$z` and `$m` accept `int|float|null`. For Geography, `$x` means
longitude and `$y` means latitude.

Each coordinate parameter is nullable. Omitting every ordinate (or passing
`null` for every ordinate) creates an empty point. Any combination of supplied
and null ordinates is preserved. A partially supplied point is not EMPTY, and
zero is a supplied value. Null ordinates do not change the declared dimension.
This preserves the selected coordinate layout and optional SRID:

```php
$emptyPoint = new PointImmutable(srid: 4326);
assert($emptyPoint->isEmpty());
assert([] === $emptyPoint->toArray());
```

`FromIndexedArrayFactory::createPoint([])` creates the same empty point in its
requested family, dimension, and spatial-reference context. A full-length tuple
containing only nulls also creates an EMPTY point. Indexed tuples must still
contain every index declared by the requested dimension; an explicit null is
different from an absent index.

```php
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable;

$point = new PointImmutable(2.3522, 48.8566, 35, 4326); // longitude, latitude, elevation
```

```php
$partialPoint = new PointImmutable(0, null, null, 4326); // XYZ Geography
assert(!$partialPoint->isEmpty());
assert([0, null, null] === $partialPoint->toArray());
assert($partialPoint->hasZ()); // The Z axis exists even when its value is null.
```

Representing these states does not establish geometric conformity with
ISO/IEC 13249-3 or OGC requirements. Request coordinate-completeness validation
explicitly through `spatial-validator`; this library does not invoke it or
depend on it. See [Validation](validators.md).

### Mutable points

Mutable points live directly in the dimension namespace:

| Layout | Class | Constructor ordinates |
| --- | --- | --- |
| XY | `Types\Dimension2\Point` | X, Y |
| XYZ | `Types\Dimension3z\Point` | X, Y, Z |
| XYM | `Types\Dimension3m\Point` | X, Y, M |
| XYZM | `Types\Dimension4zm\Point` | X, Y, Z, M |

All ordinates default to `null`. Each constructor ends with
`int|SpatialReference $srid = 0` and
`SpatialModelEnum $family = SpatialModelEnum::GEOMETRY`.
The family is instance state: the same class supports Geometry and Geography.

```php
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Point;

$point = new Point(0, null, null, 4326);
$samePoint = $point->setX("2°30'E")
    ->setY(48.5)
    ->setZ(35)
    ->setFamily(SpatialModelEnum::GEOGRAPHY)
    ->setSpatialReference(SpatialReference::epsg(2154));
assert($samePoint === $point);
assert([2.5, 48.5, 35] === $point->toArray());
```

`MutablePointInterface` exposes `setX()`, `setY()`, `setZ()` and `setM()`;
`MutableSpatialInterface` provides `setFamily()`, `setSpatialReference()` and
`setSrid()`. Every successful setter returns the same instance as `static`.
X/Y accept `float|int|string|null` and use the constructor's geo-parser path
for strings. Z/M accept `float|int|null`, without string parsing.
Rejected technical or parsing input leaves the receiver unchanged.

Calling a Z/M setter for an axis outside the declared layout throws
`LongitudeOne\SpatialTypes\Exception\BadMethodCallException`, even for `null`.
Type and dimension remain fixed; there is no dimension setter or `with…` method.
Supported axes accept null independently: `[0, null]` is not EMPTY. All-null
ordinates produce EMPTY, with `toArray() === []` and `getCoordinates() === null`.
A previously obtained `Coordinates` value remains an immutable snapshot.

Family and declared-reference setters preserve every ordinate without coordinate
transformation. Geometric validation remains explicit in the consumer. Mutable
points cannot be inserted into immutable aggregates and are not implicitly
converted. Factories continue to create immutable types.

### Immutable point coordinates

`LongitudeOne\SpatialTypes\Value\Coordinates` is a public immutable value
object for numeric or null point coordinates. Its named constructors encode
the coordinate dimension: `Coordinates::xy()`, `Coordinates::xym()`,
`Coordinates::xyz()`, and `Coordinates::xyzm()`.

`PointInterface::getCoordinates()` returns this value object.
`ImmutablePointInterface::withCoordinates(Coordinates $coordinates)` returns a new point
of the same concrete class and SRID. The supplied coordinates must have the
same dimension as the point. Its accessors and `toArray()` retain null values
for axes in that dimension; reading or replacing an axis outside the dimension
still throws `InvalidDimensionException`. All-null replacement coordinates
produce an EMPTY point, whose `toArray()` is `[]` and `getCoordinates()` is null.

```php
use LongitudeOne\SpatialTypes\Value\Coordinates;

$higherPoint = $point->withCoordinates(Coordinates::xyz(2.3522, 48.8566, 42));
```

### Point-based types

```php
new LineStringImmutable(array $points, int|SpatialReference $srid = 0);
new MultiPointImmutable(array $points, int|SpatialReference $srid = 0);
```

`$points` may contain instances of `ImmutablePointInterface` or coordinate tuples for
the selected dimension, for example `[[0, 0], [1, 1]]` for `XY` or
`[[0, 0, 12, 3], [1, 1, 15, 4]]` for `XYZM`.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;

$lineString = new LineStringImmutable([[0, 0], [2, 1], [5, 1]], 3857);
```

### Ring-, line-, and polygon-based types

```php
new PolygonImmutable(array $rings, int|SpatialReference $srid = 0);
new TriangleImmutable(array $rings, int|SpatialReference $srid = 0);
new MultiLineStringImmutable(array $lineStrings, int|SpatialReference $srid = 0);
new MultiPolygonImmutable(array $polygons, int|SpatialReference $srid = 0);
new PolyhedralSurfaceImmutable(array $patches = [], int|SpatialReference $srid = 0);
```

- A polygon ring is an `ImmutableLineStringInterface` or an array of point tuples.
- A triangle uses the same ring representation as a polygon. Cardinality,
  closure and absence of holes are not enforced; `[]` is an empty triangle.
- A multi-line-string element is an `ImmutableLineStringInterface` or point-tuple array.
  `[]` as an element creates an empty line string in the aggregate's context.
- A multi-polygon element is an `ImmutablePolygonInterface` or an array of rings.
  Empty member polygons retain their positions.
- A polyhedral-surface patch is an `ImmutablePolygonInterface` (including a triangle)
  or an array of rings. Empty patches, disconnected faces and non-planar faces
  can be represented. `[]` creates a surface with zero patches.

Supplied member objects keep their family, layout and reference; compatibility
and geometric validity are explicit consumer concerns. Coordinate tuples are
hydrated in the receiving aggregate's family, dimension and reference.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;

$polygon = new PolygonImmutable([
    [[0, 0], [4, 0], [4, 3], [0, 0]],
], 3857);
```

`new MultiLineStringImmutable([])` has zero members, whereas
`new MultiLineStringImmutable([[]])` has one empty member. `getElements()`,
`getLineStrings()`, `getLineString()` and `toArray()` preserve this distinction.
The current `MultiLineStringImmutable::isEmpty()` checks whether it has zero members;
inspect the member arrays when the distinction matters.

`new MultiPolygonImmutable([])` has zero members and `isEmpty()` returns `true`.
`new MultiPolygonImmutable([[]])` and `new MultiPolygonImmutable([new PolygonImmutable([])])` each
contain one empty polygon and `isEmpty()` returns `false`. `getElements()`,
`getPolygons()` and `getPolygon()` expose that member, whose own `isEmpty()`
returns `true`. `toArray()` preserves the distinction as `[]` versus `[[]]`;
empty members in mixed collections retain their positions.

`withLineString($index, [])` replaces a member with an empty line string without
removing it. Replacing an empty member with non-empty coordinates preserves its
position too. Unchanged members are deeply copied with their full spatial
reference, including its authority. `withSpatialReference()` and `withSrid()`
preserve empty members while applying the requested reference to the copy.
The original value remains unchanged.

See [empty MultiLineString interoperability](empty-multilinestring-interoperability.md)
for the WKT/WKB verification and the current WKT parser limitation.

`TriangleInterface` extends `PolygonInterface`. All eight dimension/family
combinations provide a concrete `TriangleImmutable`:

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\TriangleImmutable;

$triangle = new TriangleImmutable([[[0, 0], [4, 0], [0, 4], [0, 0]]], 3857);
$empty = new TriangleImmutable([]);
```

Triangle cardinality and closure are functional validity rules and are no
longer enforced by constructors or immutable replacements.

Triangles provide the polygon accessors and immutable replacement methods.
Copies preserve the concrete triangle class. `getType()` returns `GeometryTypeEnum::TRIANGLE`; JSON uses
`Triangle` with the same nested coordinate representation as polygons.
Triangle factory entry points, SQL/MM visibility attributes and text/binary/GML
conversion routines are not currently exposed.

### Heterogeneous collections

```php
new GeometryCollectionImmutable(int|SpatialReference $srid = 0, array $elements = []);
new GeographyCollectionImmutable(int|SpatialReference $srid = 0, array $elements = []);
```

Collections accept their initial elements in their constructor. They accept any
immutable spatial type implementing `ImmutableSpatialInterface`, including nested
immutable collections. Mutable components and mutable descendants are rejected
without conversion.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;

$collection = new GeometryCollectionImmutable(3857, [new PointImmutable(0, 0, 3857)]);
```

## Observing spatial values

All concrete types implement `SpatialInterface` and `JsonSerializable`.

| Method                                            | Result                                                          |
| ------------------------------------------------- | --------------------------------------------------------------- |
| `getFamily(): SpatialModelEnum`                   | `SpatialModelEnum::GEOMETRY` or `SpatialModelEnum::GEOGRAPHY`.  |
| `getType(): GeometryTypeEnum`                     | The OGC/SQL/MM type, such as `GeometryTypeEnum::POLYGON`.       |
| `getSrid(): int`                                  | The object's SRID.                                              |
| `getSpatialReference(): SpatialReference`         | The full reference identity, including its optional authority.  |
| `hasZ(): bool` / `hasM(): bool`                   | Whether the selected coordinate layout has Z or M.              |
| `hasSameDimension(SpatialInterface $other): bool` | Whether both values use the same Z/M layout.                    |
| `isEmpty(): bool`                                 | Whether the object corresponds to the empty set.                |
| `toArray(): array`                                | Nested coordinate arrays only; it omits type, family, and SRID. |
| `jsonSerialize(): array`                          | `['type' => string, 'coordinates' => array, 'srid' => int]`.    |

There is intentionally no public `getDimension()` method. Use `hasZ()` and
`hasM()` to inspect the coordinate layout.

### Point getters

Every point provides `getX()` and `getY()`. `getLongitude()` is an alias for
`getX()`, and `getLatitude()` is an alias for `getY()`; those aliases are useful
for Geography values. `getZ()` is available only for `XYZ`/`XYZM` points and
`getM()` only for `XYM`/`XYZM` points. Calling an unavailable getter throws
`BadMethodCallException`. For an empty point, its available coordinate getters
and `getCoordinates()` return `null`; `toArray()` returns `[]`.

`equalsTo(PointInterface $other): bool` compares the concrete point class,
family, SRID, coordinate layout, and all applicable ordinates. `toArray()`
returns one tuple in the layout's order.

### Aggregate getters and predicates

| Type                                         | Element access                                               | Predicates                            |
| -------------------------------------------- | ------------------------------------------------------------ | ------------------------------------- |
| `LineStringImmutable`                                 | `getPoints()`, `getPoint($index)`, `getElements()`           | `isEmpty()`, `isLine()`, `isClosed()` |
| `MultiPointImmutable`                                 | `getPoints()`, `getPoint($index)`, `getElements()`           | `isEmpty()`, `isSimple()`             |
| `PolygonImmutable` / `TriangleImmutable`                       | `getRings()`, `getRing($index)`, `getElements()`             | `isEmpty()`                           |
| `MultiLineStringImmutable`                            | `getLineStrings()`, `getLineString($index)`, `getElements()` | `isEmpty()`                           |
| `MultiPolygonImmutable`                               | `getPolygons()`, `getPolygon($index)`, `getElements()`       | `isEmpty()`                           |
| `PolyhedralSurfaceImmutable` | `getPatches()`, `getPatch($index)`, `getElements()` | `isEmpty()` |
| `GeometryCollectionImmutable` / `GeographyCollectionImmutable` | `getElements()`                                              | `isEmpty()`, `hasElement($spatial)`   |

For point, ring, line-string, polygon, and patch single-element accessors, negative
indexes count from the end (`-1` is the last element). An index is wrapped by
the element count; accessing an empty aggregate raises `OutOfBoundsException`.

In the current implementation, `isLine()` is true for a line string with at
least two points. `isClosed()` requires a line and equal first/last points.
Functional validity checks belong to explicit consumer validation; see
[Spatial validation](validators.md).

## Immutability contract

Every spatial type with the `Immutable` suffix is immutable through the public API: construction sets its
ordinates, SRID, and aggregate membership, and no public mutator exists.
`withCoordinates(Coordinates $coordinates): static` returns a point with
replacement coordinates of the same dimension; `withSpatialReference(SpatialReference $reference): static`
returns one with the same coordinates and a new declared reference.

`LineStringImmutable`, `PolygonImmutable`, `TriangleImmutable`, and `PolyhedralSurfaceImmutable` provide
`withArrayOfCoordinates(array $coordinates): static`. These methods return a
new aggregate with replacement coordinates while preserving family, dimension,
and SRID. The line-string method accepts coordinate tuples; the polygon method
accepts arrays of rings. The receiving aggregate determines whether tuples are
XY, XYM, XYZ, or XYZM. For `PolyhedralSurfaceImmutable`, replacement coordinates are
arrays of patches and use XYZ or XYZM; `[]` returns an empty surface.

`LineStringImmutable::withPoint(int $pointIndex, Coordinates $coordinates): static`
returns a deep copy with one replacement point. For polygons,
`PolygonImmutable::withPoint(int $ringIndex, int $pointIndex, Coordinates $coordinates): static`
uses a ring index followed by a point index. Replacing either endpoint of a
ring updates both endpoints to preserve its closure. Both methods retain the
family, dimension, and SRID of the receiving aggregate.

`MultiPointImmutable::withPoint(int $pointIndex, Coordinates $coordinates): static`
returns a deep copy with one replacement point and preserves the receiving
multi-point's family, dimension, and SRID.

`PolygonImmutable::withRing(int $ringIndex, array $coordinates): static` replaces
one ring. `MultiLineStringImmutable::withLineString(int $lineStringIndex, array
$coordinates): static` replaces one line string. Both return deep copies and
derive the expected XY, XYM, XYZ, or XYZM layout from the receiving aggregate.

`MultiLineStringImmutable::withPoint(int $lineStringIndex, int $pointIndex, Coordinates
$coordinates): static` replaces one point in one line string and returns a deep
copy.

`MultiPolygonImmutable::withPoint(int $polygonIndex, int $ringIndex, int $pointIndex,
Coordinates $coordinates): static` replaces one point.
`MultiPolygonImmutable::withRing(int $polygonIndex, int $ringIndex, array
$coordinates): static` replaces one ring, and `MultiPolygonImmutable::withPolygon(int
$polygonIndex, array $coordinates): static` replaces one polygon. All three
methods return deep copies and preserve family, dimension, and SRID.
`PolyhedralSurfaceImmutable` provides `withPoint($patchIndex, $ringIndex, $pointIndex,
$coordinates)`, `withRing($patchIndex, $ringIndex, $coordinates)` and
`withPatch($patchIndex, $coordinates)` with the same deep-copy semantics.
Surface topology is not checked. Editing a shared vertex does not silently
move neighbouring faces.
Empty surfaces reject indexed access and indexed replacements.

`GeometryCollectionImmutable::withElement(int $elementIndex, ImmutableSpatialInterface
$element): static` and `GeographyCollectionImmutable::withElement(...)` replace one
element implementing `ImmutableSpatialInterface` and deeply copy the unchanged elements.

All concrete immutable types implement `withSpatialReference(SpatialReference $reference): static`.
For aggregates, it returns a deep copy whose contained values receive the
requested reference, so the result remains internally reference-consistent.
`withSrid(int $srid)` remains as a legacy integer adapter.

Constructors and replacements enforce technical input types and coordinate
layouts, without functional geometry rules or member compatibility checks.

`getPoints()`, `getRings()`, and the other plural getters return PHP arrays, so
changing the returned array does not alter the aggregate's membership. Their
contained objects are immutable too, so the retrieved object graph is safe to
share.

## Diagnostic messages

Untrusted values included in exceptions raised by this library are formatted with
`LongitudeOne\Core\Diagnostic\DiagnosticValueFormatter` (spatial-core 1.1+).
Control characters, invisible Unicode formatting characters and line separators
are escaped visibly; invalid UTF-8 bytes are escaped and each formatted value is
limited to 2,048 characters. This affects diagnostic output only, not coordinate parsing.

Previous exceptions from dependencies retain their original messages and formatting.
Exceptions constructed directly by application code retain PHP's standard constructor behavior.
The formatter does not escape messages for HTML, JSON, XML or SQL.

## Circular strings and the common curve contract

`Interfaces\CurveInterface` extends `SpatialInterface` and is the shared curve
contract implemented by `LineStringInterface`, `CircularStringInterface` and
`CompoundCurveInterface`. It provides `getStartPoint(): ?PointInterface`,
`getEndPoint(): ?PointInterface` and `isClosed(): bool`. Empty curves return
`null` endpoints and are not closed. Endpoint equality uses the existing
`PointInterface::equalsTo()` semantics, including all ordinates and their PHP
numeric types; no tolerance or coordinate normalization is introduced.

This is an approved breaking change before 1.0.0: third-party implementations
of these curve interfaces must implement the three common methods. Existing
concrete line-string closure behavior is preserved.

`Types\Dimension{2,3z,3m,4zm}\{Geometry,Geography}\CircularStringImmutable` supports
XY, XYZ, XYM and XYZM in both families. Its constructor is:

```php
public function __construct(array $points, int|SpatialReference $srid = 0);
```

Pass defining `ImmutablePointInterface` values or coordinate tuples. `[]` creates an
empty circular string. Odd cardinality, non-empty members and distinct arc
points are geometric validity rules and are not automatically checked. Stored
points are never replaced by a linear approximation.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;

$arc = new CircularStringImmutable([[0, 0], [1, 1], [2, 0]], 4326);
$circle = new CircularStringImmutable([[1, 0], [-1, 0], [1, 0]], 4326);
$empty = new CircularStringImmutable([], 4326);
$replacement = $arc->withPoint(1, Coordinates::xy(1, 2));
```

`getPoints()`, `getPoint($index)` and `getElements()` expose defining points.
Indexes wrap by the point count and negative indexes count from the end;
empty indexed access throws `OutOfBoundsException`. The ordinary spatial
methods report `CIRCULARSTRING`, coordinate dimension, family and reference.
`toArray()` contains the defining coordinate tuples.

`withPoint()` and `withArrayOfCoordinates()` preserve the curve's family,
dimension and complete reference. No functional curve validation is performed.
`withArrayOfCoordinates([])` returns an empty curve. Reference changes copy
every point without transforming coordinates.

## Compound curves

`Types\Dimension{2,3z,3m,4zm}\{Geometry,Geography}\CompoundCurveImmutable` implements
`ImmutableCompoundCurveInterface` in XY, XYZ, XYM and XYZM. Construct it with ordered
`ImmutableCurveInterface` components and an optional integer or `SpatialReference`:

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;

$curve = new CompoundCurveImmutable([
    new LineStringImmutable([[0, 0], [1, 1]], 4326),
    new CircularStringImmutable([[1, 1], [2, 2], [3, 1]], 4326),
], 4326);
$empty = new CompoundCurveImmutable([], 4326);
```

`new CompoundCurveImmutable()` creates an empty XY Geometry value with SRID 0 when
using the class imported above. Components must implement `ImmutableCurveInterface`
and expose point tuples with numeric or null ordinates. Null entries retain
their positions through array access and immutable reference copies. Family,
dimension, reference, component kind, non-empty endpoints and continuity are
not validated automatically. Components are preserved as objects without
flattening, reversal or linear approximation.

`getCurves()` and `getElements()` expose the ordered components as
`ImmutableCurveInterface[]`. `getCurve($index)` uses wrapping indexes and negative
indexes from the end; EMPTY indexed access raises `OutOfBoundsException`.
`getStartPoint()` and `getEndPoint()` expose the outer endpoints. `isClosed()`
compares those endpoints without checking intermediate continuity. Closure does not claim simplicity; no compound ring or simplicity
predicate is added. `getType()` returns `GeometryTypeEnum::COMPOUNDCURVE`.
`toArray()` contains one array of defining coordinate tuples per component;
use component accessors when interpolation identity is needed. The inherited
JSON representation uses type `CompoundCurve`, these coordinates, and SRID;
it is not a GeoJSON or interchange-format implementation.

`withSpatialReference()` deeply copies components and their points and
does not transform coordinates or validate continuity. `withSrid()` is the
legacy integer adapter. Reconstruct a compound from replacement component
curves to change its path; returned component arrays cannot mutate membership.

The normative basis is ISO/IEC CD 13249-3:201x(E), the available 2009-01-16
Committee Draft: clauses 4.2.7 and 7.4.1 (component types, continuity and empty
values), and 7.4.6–7.4.7 (endpoints, including `null` for EMPTY). The library's
point equality semantics apply to endpoint predicates; continuity is not enforced. This
feature adds no serialization, decoding, linearization or factory entry points.

## Mutable line strings and circular strings

`Types\Dimension2`, `Types\Dimension3z`, `Types\Dimension3m` and
`Types\Dimension4zm` each provide `LineString` and `CircularString`. They implement
`MutableLineStringInterface` and `MutableCircularStringInterface` respectively.
Their constructor is `__construct(array $points, int|SpatialReference $srid = 0,
SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)`. The coordinate layout and
spatial type remain fixed; the declared family is instance state.

Supply mutable point objects or indexed coordinate tuples in the selected layout.
Tuples create mutable points using the line's declared family and reference,
including the existing X/Y coordinate-string parser. Numeric and null Z/M values
follow the corresponding point constructor. Immutable point objects are rejected
without conversion. Supplied mutable points keep their identity, ordinates,
family and reference; construction and membership edits do not synchronize them.
Functional compatibility and geometric validity remain consumer concerns.

| Operation | Effect |
| --- | --- |
| `addPoint($point)` | Append the supplied mutable point |
| `insertPoint($index, $point)` | Insert before the zero-based index; the point count appends |
| `setPoint($index, $point)` | Replace an existing point |
| `removePoint($index)` | Remove an existing point and reindex the remaining points |
| `setArrayOfCoordinates($tuples)` | Replace all points with newly constructed mutable points |
| `getPoint($index)` | Return the same mutable point instance at an existing index |
| `getPoints()` / `getElements()` | Return an ordered array of the same mutable point instances |

Indexed access, replacement and removal require an existing non-negative index;
invalid addressing throws `OutOfBoundsException`. Unlike the immutable variants,
mutable paths do not wrap indices or accept negative indices. Failed membership,
coordinate or arc edits preserve the path and all referenced points. All mutation
methods return the receiver with return type `static`; there are no `with…`
methods or dimension setter.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;

$shared = new Point(0, null, 4326);
$first = new LineString([$shared], 4326);
$second = new LineString([$shared], 4326);
$first->addPoint(new Point(2, 3))->insertPoint(1, new Point(1, null));
$shared->setY(4); // Both lines now expose [0, 4] for their first point.
$first->setSrid(2154); // Updates the shared point, also observed through $second.
$first->setSrid(3857, false); // Updates only $first's declared reference.
```

`setSrid($srid, $propagate = true)`,
`setSpatialReference($reference, $propagate = true)` and
`setFamily($family, $propagate = true)` change the path's declared context.
By default they also update every contained point, including shared instances in
other paths. Passing `false` changes only the receiver. None of these operations
transforms ordinates or updates the metadata of another containing path.
Returned point arrays cannot change membership, but their mutable points remain shared.

### Circular arc indexing

General point editing is available even for EMPTY or incomplete circular paths.
`setOrigin($point)` inserts the first point into an empty circular string or
replaces only its existing first point. `appendArc($passingPoint,
$destinationPoint)` appends two points after an origin followed by zero or more
complete pairs. Without an origin or after an incomplete pair, it throws
`InvalidValueException` without mutation.

| Arc index | Origin point index | Passing point index | Destination point index |
| --- | --- | --- | --- |
| `0` | `0` | `1` | `2` |
| `1` | `2` | `3` | `4` |
| `i` | `2 * i` | `2 * i + 1` | `2 * i + 2` |

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;

$path = new CircularString([]);
$path->setOrigin(new Point(0, 0))
    ->appendArc(new Point(1, 1), new Point(2, 0))
    ->appendArc(new Point(3, -1), new Point(4, 0));
$path->replaceArc(0, new Point(1, 2), new Point(2, 1));
// Point 0 is retained; points 1 and 2 are replaced.
// Point 2 is also the origin of arc 1, whose points 3 and 4 are retained.
```

`replaceArc()` requires all three positions of the addressed arc to exist and
throws `OutOfBoundsException` otherwise. Arc helpers check only this sequence
structure. They do not validate circle geometry, coordinate completeness or
continuity, close the path, repair points, or enforce geometric minimum counts.
Null ordinates, EMPTY points and incomplete point sequences remain representable.

## Mutable compound curves

`Types\Dimension2\CompoundCurve`, `Types\Dimension3z\CompoundCurve`,
`Types\Dimension3m\CompoundCurve` and `Types\Dimension4zm\CompoundCurve`
implement `MutableCompoundCurveInterface`. Their constructor is
`__construct(array $curves = [], int|SpatialReference $srid = 0,
SpatialModelEnum $family = SpatialModelEnum::GEOMETRY)`.

Only `MutableLineStringInterface` and `MutableCircularStringInterface` components
are accepted. Coordinate arrays, immutable curves and nested compound curves are
rejected without conversion. Components retain their own declared layout,
family and reference; compatibility and geometric continuity require explicit
validation. Incomplete and empty component sequences remain representable.

| Operation | Behavior |
| --- | --- |
| `addCurve($curve)` | Append a component |
| `insertCurve($index, $curve)` | Insert before an index; the component count appends |
| `setCurve($index, $curve)` | Replace an existing component |
| `removeCurve($index)` | Remove an existing component and reindex |
| `setCurves($curves)` | Replace the entire ordered sequence atomically |
| `getCurve($index)` | Read an existing component |
| `getCurves()`, `getElements()` | Return the ordered component instances |

All indices are zero-based. Negative indices and missing components raise
`OutOfBoundsException`; indexed access does not wrap. Invalid component arrays
raise `InvalidValueException`, while incompatible typed mutation arguments raise
PHP `TypeError`. Failures preserve membership and supplied components.
Successful mutations return the receiver as `static` for chaining. No `with…`
methods or dimension setter are exposed.

```php
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;

$point = new Point(0, null, 4326);
$line = new LineString([$point], 4326);
$arc = new CircularString([[10, 0], [11, 1], [12, 0]], 4326);
$path = new CompoundCurve([$line], 4326);
$other = new CompoundCurve([$line], 4326);
$path->addCurve($arc)->insertCurve(1, $line)->removeCurve(1);

assert($path->getCurve(0) === $line);
$point->setY(2); // Both paths now expose the changed point.
$path->setSrid(2154); // Updates both curves and all their points in place.
assert($other->getCurve(0)->getSrid() === 2154);
assert($other->getSrid() === 4326);
$path->setFamily(SpatialModelEnum::GEOGRAPHY, false); // Only this compound changes.
```

Construction, insertion and replacement retain the supplied instances without
cloning. Getters expose those instances; editing a component or its points is
visible to every owner. Modifying the returned PHP membership array itself does
not change the compound.

`setSrid()`, `setSpatialReference()` and `setFamily()` accept a second boolean
`$propagate` argument, defaulting to `true`. Propagation updates existing
components and their points recursively, including instances shared by other
owners. Passing `false` updates only the receiver. No context setter recalculates
coordinates, changes the compound's spatial type or changes its layout.
`toArray()` preserves component boundaries and their defining point tuples;
circular components are never flattened or approximated.

## Mutable polygons and triangles

`Polygon` and `Triangle` are available directly in `Types\Dimension2`,
`Types\Dimension3z`, `Types\Dimension3m`, and `Types\Dimension4zm`.
Both constructors have the same signature:

```php
new Polygon(array $rings, int|SpatialReference $srid = 0,
    SpatialModelEnum $family = SpatialModelEnum::GEOMETRY);
new Triangle(array $rings, int|SpatialReference $srid = 0,
    SpatialModelEnum $family = SpatialModelEnum::GEOMETRY);
```

Each input ring is a `MutableLineStringInterface` or an array of mutable points
and coordinate tuples. Arrays create mutable line strings and points using the
surface's current layout, family and reference. Supplied spatial objects retain
their own context and identity. Immutable rings and points are rejected without
conversion, including immutable points exposed by a custom ring.

| Operation | Effect |
| --- | --- |
| `addRing($ring)` | Append a mutable line string |
| `insertRing($index, $ring)` | Insert before the addressed ring; the count appends |
| `removeRing($index)` | Remove an existing ring and reindex |
| `setRing($index, $ring)` | Replace one existing ring |
| `setPoint($ringIndex, $pointIndex, $point)` | Replace one point in an existing ring |
| `setArrayOfCoordinates($coordinates)` | Replace all rings from coordinate tuples |
| `getRing($index)`, `getRings()`, `getElements()` | Return the retained mutable ring instances |

All successful mutations return the receiver as `static`. Ring and point indices
start at zero; access, replacement and removal require an existing element.
Negative indices and indices beyond the addressed sequence are rejected with
`OutOfBoundsException`. Failed addressing or technical input checks leave the
surface and shared descendants unchanged, including a late invalid tuple during
complete coordinate replacement.

```php
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Triangle;

$ring = new LineString([[0, null], [3, 4]], 4326);
$polygon = new Polygon([$ring], 4326);
$triangle = new Triangle([$ring], 4326);
$replacement = new Point(7, null, 4326);

$samePolygon = $polygon->setPoint(0, 0, $replacement);
assert($polygon === $samePolygon);
assert($replacement === $triangle->getRing(0)->getPoint(0));
assert([[[7, null], [3, 4]]] === $triangle->toArray());

$polygon->setFamily(SpatialModelEnum::GEOGRAPHY, false); // Only the polygon.
$polygon->setSrid(2154); // Polygon, shared ring and every point.
assert(2154 === $triangle->getRing(0)->getSrid());
assert(4326 === $triangle->getSrid());
```

Construction, insertion and replacement retain supplied components without
cloning; getters expose the same instances. Changes to a shared ring or point
are visible in every surface containing it. Coordinate replacement creates new
descendants and leaves the previous rings and their other owners unchanged.

`setSrid()`, `setSpatialReference()` and `setFamily()` each accept a second
`bool $propagate = true` parameter. By default they update all rings and points
recursively; `false` updates only the receiver. No ordinates are recalculated.
The instance's spatial type and coordinate layout remain fixed. Mutable surfaces
have no `with…` methods or dimension setter; immutable counterparts keep their
copy-producing operations and reject mutable components.

Open or incomplete rings, EMPTY rings, null ordinates and triangles with extra
points or rings remain representable. Editing preserves the supplied order and
does not close rings, synchronize endpoints, enforce orientation or validate
geometry. ISO/IEC CD 13249-3:201x(E), clauses 8.3.1 and 8.4.1, define the polygon
and triangle models; representing a value does not establish conformity with
those geometric definitions. Request functional checks through
[spatial-validator](validators.md) explicitly.
