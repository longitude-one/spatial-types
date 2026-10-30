# Collection emptiness and stored members

For MultiPoint, MultiLineString, MultiPolygon and GeometryCollection,
`isEmpty()` returns true when every stored member is EMPTY. A collection with
zero members is EMPTY too. A single non-EMPTY member makes the collection
non-EMPTY, regardless of its position.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;

$empty = new PointImmutable();
$members = new MultiPointImmutable([$empty, $empty]);

$members->isEmpty();               // true
count($members->getElements());    // 2
$members->toArray();               // [[], []]
```

EMPTY classification never removes members or changes their identity, order,
coordinates or context. Inspect `getElements()` and its count for membership;
`isEmpty()` does not mean that indexed access has no stored elements.

The rule applies to XY, XYZ, XYM and XYZM, Geometry and Geography, and mutable
and immutable collections. It recurses through nested GeometryCollections.
Each non-collection member keeps its existing EMPTY contract: for example, a
represented line string with a stored EMPTY point is still a non-EMPTY line
string under that type's existing contract. This change neither validates nor
reinterprets invalid polygon boundaries or other non-collection structures.

## Mutable descendants and immutable replacements

Mutable collections inspect their members on each call. Adding, removing or
replacing a member can change the result. Editing a shared mutable child is
visible through every collection that holds it, including nested collections,
without reinsertion. No EMPTY state is cached.

Immutable `withPoint()`, `withLineString()`, `withPolygon()` and `withElement()`
operations return a replacement with the appropriate EMPTY state and preserve
the source collection. Declared-reference copies retain EMPTY members too.

## Compatibility and Story #26 R4 evidence

Alexandre explicitly approved this observable behavior change in
[Story #65](https://github.com/longitude-one/spatial-types/issues/65) on
2026-10-30. Previously a collection with stored EMPTY members returned false;
it now returns true when all members are EMPTY. Member-admission rules,
public signatures, stored cardinality and array representations are unchanged.
No new normative interpretation beyond that decision is adopted.

The following matrix supplements the
[Story #26 audit](https://github.com/longitude-one/spatial-types/issues/26)
without importing its separate draft report or claiming full conformance.

| R4 requirement | Implementation | Automated evidence | Status |
| --- | --- | --- | --- |
| Zero, single/multiple all-EMPTY and mixed membership | Immutable collection/MultiPoint/MultiLineString/MultiPolygon bases; mutable collection base in `lib/Internal/Types/` | `tests/Issue/AllEmpty{GeometryCollection,MultiPoint,MultiLineString,MultiPolygon}Test.php` | Covered across four layouts, two families and both variants |
| Membership, context, order and identity retained | Same methods inspect existing members without changing storage | The four files above and retained EMPTY-member regressions from #8–#10 | Covered |
| Mutable membership and shared/nested-child transitions | Mutable collection base evaluates current member state | `tests/Issue/MutableCollectionEmptyTransitionTest.php` | Covered without caching or reinsertion |
| Immutable replacements preserve originals; heterogeneous contexts remain representable; non-collection EMPTY unchanged | Existing replacement methods and member contracts | `tests/Issue/ImmutableCollectionEmptyReplacementTest.php` | Covered |

The braces in test paths enumerate separate files. This evidence concerns R4;
other unresolved audit findings and predicate decisions remain separate.
