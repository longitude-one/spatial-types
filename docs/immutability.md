# Immutability

Spatial values are often reused in several places. Treating their coordinates
as immutable makes that reuse safe and makes a value's location explicit.

## Current contract

`Point` is immutable through the public API. Its coordinates and SRID are set
by its constructor and it has no public setter. `getCoordinates()` returns an
immutable `Value\Coordinates` value. `withCoordinates()` creates a distinct
point with replacement coordinates, while `withSrid()` creates a distinct point
with the same coordinates and a different SRID.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Value\Coordinates;

$paris = new Point(2.3522, 48.8566, 4326);
$movedParis = $paris->withCoordinates(Coordinates::xy(2.3600, 48.8566));
$parisInLambert93 = $paris->withSrid(2154);
```

The original `$paris` remains unchanged. This is particularly useful when a
point is part of an aggregate.

`LineString`, `MultiPoint`, and `Polygon` provide an equally immutable
point-replacement operation. `withPoint()` returns a deep copy: the source
aggregate, its rings, and its points remain unchanged.

```php
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPoint;

$line = new LineString([[1, 2, 10], [3, 4, 20]], 2154);
$multiPoint = new MultiPoint([[1, 2, 10], [3, 4, 20]], 2154);
$movedLine = $line->withPoint(1, Coordinates::xym(5, 6, 30));
$movedMultiPoint = $multiPoint->withPoint(0, Coordinates::xym(5, 6, 30));
```

For a polygon, the ring index comes before the point index. A replacement of
the first or final point of a ring is mirrored to the other endpoint, which
preserves its closure.

```php
$movedExterior = $polygon->withPoint(0, 0, Coordinates::xym(5, 6, 30));
$movedHole = $polygon->withPoint(1, -1, Coordinates::xym(7, 8, 40));
```

`Polygon::withLineString()` and `MultiLineString::withLineString()` replace a
complete ring or line string from coordinate tuples. They also return deep
copies and preserve the source aggregate's family, dimension, and SRID.

```php
$expandedPolygon = $polygon->withLineString(0, [
    [-1, -1], [11, -1], [-1, 11], [-1, -1],
]);
$updatedMultiLine = $multiLine->withLineString(1, [[5, 6], [7, 8]]);
```

`MultiPolygon` follows the same rule at every nesting level:
`withPoint()` selects a polygon, ring, and point; `withLineString()` selects a
polygon and ring; and `withPolygon()` selects a polygon. Every method returns a
deep copy and preserves the family, dimension, and SRID.

```php
$movedMultiPolygon = $multiPolygon->withPoint(0, 0, 0, Coordinates::xy(-1, -1));
$expandedMultiPolygon = $multiPolygon->withLineString(1, 0, [[19, -1], [31, -1], [19, 11], [19, -1]]);
$replacedMultiPolygon = $multiPolygon->withPolygon(0, [
    [[40, 0], [50, 0], [40, 10], [40, 0]],
]);
```

## Why coordinates are immutable

Consider a point used by a line string, where that line string is then used by
a multi-line string:

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;

$point = new Point(2.3522, 48.8566, 4326);
$line = new LineString([
    $point,
    new Point(2.3600, 48.8600, 4326),
], 4326);
$multiLine = new MultiLineString([$line], 4326);
```

`$point` is referenced by `$line`, and `$line` is referenced by `$multiLine`.
If a public operation such as `setX()` could change `$point`, the line and the
multi-line string would both change as an indirect side effect. A caller that
only holds `$multiLine` could therefore observe a geometry change without any
operation being made on it.

Because points are immutable, that operation does not exist. Code that needs
another location creates another point and explicitly builds the spatial value
that should contain it. This preserves validation of geography coordinate
ranges, coordinate dimensions, family, and SRID compatibility.

Changing an SRID follows the same value-object rule: `withSrid()` produces
another spatial value. It does not transform coordinates; a coordinate
transformation must first calculate new ordinates, then construct a value with
those ordinates and the target SRID.

## Coordinates value object

`Value\Coordinates` is a public, immutable value object for a normalized point
tuple. It accepts numbers only: parsing coordinate strings remains a constructor
and factory concern because it depends on the point family. The value carries
its dimension, so it cannot represent an `XY` point with a Z or M ordinate.

```php
$coordinates = Coordinates::xyzm(2.3522, 48.8566, 35, 12);
$higherCoordinates = $coordinates->withZ(42);
$higherParis = $point->withCoordinates($higherCoordinates);
```

`withCoordinates()` requires the same dimension as the receiving point. It
preserves its concrete class, family, and SRID, and still applies geography
longitude/latitude validation when creating the replacement point.

## Scope and aggregate types

The coordinate-replacement API applies to `Point`, `LineString`, `MultiPoint`,
and `Polygon`. `LineString::withPoint()` and `MultiPoint::withPoint()` select
a point; `Polygon::withPoint()` selects a ring then a point. All use the
immutable `Value\Coordinates` value, so a replacement must match the receiver's
coordinate dimension.

`Polygon::withLineString()` selects a ring and requires coordinates forming a
closed ring. `MultiLineString::withLineString()` selects one of its line
strings. Both derive the expected coordinate layout from the receiver.

`MultiPolygon::withPoint()` selects a polygon, ring, then point.
`MultiPolygon::withLineString()` selects a polygon then ring, while
`MultiPolygon::withPolygon()` replaces an entire polygon. Each operation
creates a deep copy of every polygon in the collection.

Aggregate types such as `LineString`, `Polygon`, and `MultiLineString` still
expose public membership mutators (`addPoint()`, `addRing()`, and similar
methods), and are therefore mutable. Their child points nevertheless remain
safe to share because their coordinates cannot be altered after construction.
Calling `withSrid()` on an aggregate creates a deep copy whose descendants all
receive the requested SRID.

See [Instantiable spatial types](instantiable-spatial-types.md#mutability-contract)
for the complete current mutability contract.
