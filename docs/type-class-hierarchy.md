# Type class hierarchy

The following tree describes class inheritance in `Types`, rather than the
file layout. `{Dimension2, Dimension3m, Dimension3z, Dimension4zm}` represents
one branch for each listed coordinate dimension and `{Geometry, Geography}` one
concrete class for each spatial model.

```text
AbstractSpatialType
├── AbstractPoint
│   └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\Point
├── AbstractPolygon
│   ├── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\Polygon
│   └── AbstractTriangle
│       └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\Triangle
├── AbstractMultiPolygon
│   └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiPolygon
├── AbstractCollection
│   ├── Dimension2\AbstractCollection
│   │   ├── Dimension2\Geometry\GeometryCollection
│   │   └── Dimension2\Geography\GeographyCollection
│   └── Dimension{3m,3z,4zm}\{Geometry\GeometryCollection,Geography\GeographyCollection}
├── Collection\AbstractPointCollection
│   ├── AbstractLineString
│   │   └── Dimension{2,3m,3z,4zm}\AbstractLineString
│   │       └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\LineString
│   └── AbstractMultiPoint
│       └── Dimension{2,3m,3z,4zm}\AbstractMultiPoint
│           └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiPoint
└── Collection\AbstractLineStringCollection
    └── AbstractMultiLineString
        └── Dimension{2,3m,3z,4zm}\AbstractMultiLineString
            └── Dimension{2,3m,3z,4zm}\{Geometry,Geography}\MultiLineString
```