# Type class hierarchy

The following tree describes class inheritance in `Types`, rather than the
file layout. `{Dimension2, Dimension3m, Dimension3z, Dimension4zm}` represents
one branch for each listed coordinate dimension and `{Geometry, Geography}` one
concrete class for each spatial model.

`AbstractSpatialTypeImmutable` extends the read-only `AbstractSpatialType` base.
The following hierarchy contains immutable implementation bases. Mutable points
extend `AbstractSpatialType` through `AbstractPoint`, with concrete
`Dimension{2,3m,3z,4zm}\Point` classes. `PointBehaviorTrait` shares point reads,
coordinate initialization and parsing between `AbstractPoint` and
`AbstractPointImmutable`; mutable points do not inherit copy operations.

```text
AbstractSpatialTypeImmutable
├── AbstractCompoundCurveImmutable
│   └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\CompoundCurveImmutable
├── AbstractCircularStringImmutable
│   └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\CircularStringImmutable
├── AbstractPointImmutable
│   └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\PointImmutable
├── AbstractPolygonImmutable
│   ├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\PolygonImmutable
│   └── AbstractTriangleImmutable
│       └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\TriangleImmutable
├── AbstractPolyhedralSurfaceImmutable
│   └── Dimension{3z,4zm}\{Geometry,Geography}\PolyhedralSurfaceImmutable
├── AbstractMultiPolygonImmutable
│   └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiPolygonImmutable
├── AbstractCollectionImmutable
│   ├── Dimension2\AbstractCollectionImmutable
│   │   ├── Dimension2\Geometry\GeometryCollectionImmutable
│   │   └── Dimension2\Geography\GeographyCollectionImmutable
│   └── Dimension{3m,3z,4zm}\{Geometry\GeometryCollectionImmutable,Geography\GeographyCollectionImmutable}
├── Collection\AbstractPointCollectionImmutable
│   ├── AbstractLineStringImmutable
│   │   └── Dimension{2,3m,3z,4zm}\AbstractLineStringImmutable
│   │       └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\LineStringImmutable
│   └── AbstractMultiPointImmutable
│       └── Dimension{2,3m,3z,4zm}\AbstractMultiPointImmutable
│           └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiPointImmutable
└── Collection\AbstractLineStringCollectionImmutable
    └── AbstractMultiLineStringImmutable
        └── Dimension{2,3m,3z,4zm}\AbstractMultiLineStringImmutable
            └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiLineStringImmutable
```

`LineStringInterface`, `CircularStringInterface` and `CompoundCurveInterface`
extend `CurveInterface`, which exposes nullable start/end points and closure
in addition to `SpatialInterface`. Circular strings have their own point
storage; they are not line strings and do not inherit point-set
simplicity checks. Compound curves retain ordered `ImmutableCurveInterface` components
and require curve interfaces and point tuples with numeric or null ordinates; functional compatibility
and endpoint continuity are not checked.

Mutable point-defined paths extend the read-only spatial base independently of
immutable implementations:

```text
AbstractSpatialType
└── AbstractPointSequence
    ├── AbstractLineString
    │   └── Dimension{2,3m,3z,4zm}\LineString
    └── AbstractCircularString
        └── Dimension{2,3m,3z,4zm}\CircularString
```

`PointSequenceEditingTrait` shares ordered membership editing between the two
mutable path bases. These paths contain only `MutablePointInterface` instances;
coordinate tuples create mutable points, and immutable components are rejected.
Their interfaces refine the common read methods to return mutable points and
expose type-specific mutation, including optional context propagation. The common
interfaces remain read-only, and mutable paths expose no copy operations.

Mutable compound curves use a separate base:

```text
AbstractSpatialType
└── AbstractCompoundCurve
    └── Dimension{2,3m,3z,4zm}\CompoundCurve
```

`MutableCompoundCurveInterface` refines component getters to mutable line-string
or circular-string interfaces and exposes ordered membership editing. Immutable
compound curves retain their separate immutable base and copy contracts.

Mutable polygons and triangles share ring storage and editing in a separate base:

```text
AbstractSpatialType
└── AbstractPolygon
    ├── Dimension{2,3m,3z,4zm}\Polygon
    └── Dimension{2,3m,3z,4zm}\Triangle
```

`MutablePolygonInterface` refines ring accessors to `MutableLineStringInterface`
and provides ring editing and ring-then-point replacement. `MutableTriangleInterface`
adds the distinct triangle type contract. Both concrete variants retain mutable
rings and points by reference and expose no immutable copy operations. Their
layout and spatial type are fixed; geometric validity remains explicit.


Mutable polyhedral surfaces use a separate patch-owning base:

```text
AbstractSpatialType
└── AbstractPolyhedralSurface
    └── Dimension{3z,4zm}\PolyhedralSurface
```

`MutablePolyhedralSurfaceInterface` refines patch accessors to
`MutablePolygonInterface` and exposes patch editing plus patch-then-ring-then-point
addressing. The base retains patches and their mutable descendants by reference.
Coordinate arrays create mutable polygons in the fixed XYZ or XYZM layout;
immutable surface implementations and their copy contracts remain separate.
