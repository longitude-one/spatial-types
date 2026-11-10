# Unused exception removal

`LongitudeOne\SpatialTypes\Exception\InvalidFamilyException`,
`InvalidSridException` and `RangeException` are removed without replacement
classes or compatibility aliases. This is a breaking public API change.
The [maintainer's approval for Story #86](https://github.com/longitude-one/spatial-types/issues/86#issuecomment-6767689310)
explicitly authorizes these removals and the API, Architecture and Breaking
Change Gates, including the affected tests and migration documentation.
That later decision supersedes the original decision-only constraint in the
Story body.

## Inventory and decisions

The inventory was refreshed on 2026-11-10 against commit
`ce0c458185fb65eea48f444fbf5266a9a4e1849a`, before removal.
Names in the following table are relative to
`LongitudeOne\SpatialTypes\Exception`.

| Class | Production usage | Exception-specific tests | Named documentation references | Known external usage | Approved decision and reason |
| --- | --- | --- | --- | --- | --- |
| `InvalidFamilyException` | Definition only; no throw or catch site | `tests/Unit/Exception/InvalidFamilyExceptionTest.php` and the shared catch-contract test | None in `docs/` or `README.md` | None found within the searches below | Remove: family compatibility is an explicit consumer concern; no production contract uses this class. |
| `InvalidSridException` | Definition only; no throw or catch site | `tests/Unit/Exception/InvalidSridExceptionTest.php` and the shared catch-contract test | None in `docs/` or `README.md` | None found within the searches below | Remove: reference compatibility is an explicit consumer concern; no production contract uses this class. |
| `RangeException` | Definition only; parsing catches the distinct GeoParser class | `tests/Unit/Exception/RangeExceptionTest.php` and the shared catch-contract test | None in `docs/` or `README.md` | None found within the searches below | Remove: this class does not participate in coordinate parsing. |

The shared test is
`tests/Functional/Exception/SpatialTypeExceptionInterfaceTest.php`.
Instantiating these classes solely to assert their type or interface does not
constitute a functional production usage. The PHPStan baseline contained one
suppression per dedicated test; those obsolete entries are removed with the
tests. No quality rule is relaxed.

`lib/Internal/Types/PointBehaviorTrait.php` also contained a PHPDoc statement
that `RangeException` is never thrown. It is removed to avoid an ambiguous
reference. The deleted classes' PHPDoc described automatic family/reference
checks and range handling that their production code did not perform.

Reproduce the internal inventory on the analyzed commit:

```bash
git grep -n -E 'InvalidFamilyException|InvalidSridException|RangeException' ce0c458185fb65eea48f444fbf5266a9a4e1849a -- lib tests docs README.md quality/php-stan
git grep -n 'OUT_OF_RANGE_' ce0c458185fb65eea48f444fbf5266a9a4e1849a -- lib
```

## External-search evidence and limits

On 2026-11-10, GitHub code search was queried for each fully qualified class
name above, quoted as a literal, with a maximum of 20 returned files per
query. Each query returned only the two exception-test files from this
repository. Searches for the short names also returned unrelated classes in
other languages and namespaces; these are not usages of this API.

A local search additionally found no candidate references in the `lib/`
directories of `spatial-core`, `spatial-validator`, `spatial-writer`,
`spatial-decoder` and `doctrine-spatial`, or in `spatial-docs/docs/`.
The inspected checkout revisions were:

| Repository | HEAD during search |
| --- | --- |
| `spatial-core` | `1289582288e8954243de5ccb1f28ab7e78f4e9a4` |
| `spatial-validator` | `e16098b457520ae03cdc8355d2fddf9f4ddd899e` |
| `spatial-writer` | `7680a3bb017cef797660210a7b3eab49a94eb1c5` |
| `spatial-decoder` | `6c2a03bd39c7adecdabec6d7fbee2fef94495927` |
| `doctrine-spatial` | `1afd60c1d52ddfa7badd05246bc85f045e1d2cef` |
| `spatial-docs` | `065a24eddacb578a51eb25810bc5bfbeba12a5b7` |

These were working-tree searches; `spatial-decoder` and `doctrine-spatial`
had local changes. GitHub searches cover indexed default-branch content
visible to the connected account and are subject to indexing and result
limits. They cannot establish absence from private applications, unindexed
repositories, other branches, generated code, dynamically composed names or
previously serialized objects. No external non-usage guarantee is made.

## Migration and versioning

The approved implementation removes all three classes directly. It does not
introduce a deprecation phase, aliases or substitute exception classes.
The change is recorded with a Conventional Commits breaking-change marker;
this work does not create a release or tag. The proposed release target is
the 1.0.0 public-contract baseline; maintainer confirmation of that target
and this migration strategy is still required by Story #86 before merge.

| Consumer reference | Required action |
| --- | --- |
| Imports, type declarations, `::class` references, reflection or configured class names | Remove obsolete references before upgrading. Instantiation and inheritance of removed classes can no longer work. |
| Catch clauses for errors expected from `spatial-types` | Remove obsolete branches. Coordinate parsing continues to throw `InvalidValueException`, also catchable through `SpatialTypeExceptionInterface`. |
| Application code that throws or extends one of the removed classes | Introduce an application-owned exception and adapt its callers and catches; there is no equivalent library replacement. |
| Family or reference compatibility handling | Invoke explicit validation as needed; removing an unused exception does not introduce automatic compatibility checks. |

The remaining exception classes and `SpatialTypeExceptionInterface` keep
their existing public contracts. In particular,
`InvalidValueException::OUT_OF_RANGE_LATITUDE`, `OUT_OF_RANGE_LONGITUDE`,
`OUT_OF_RANGE_MINUTE` and `OUT_OF_RANGE_SECOND` remain active in
`PointBehaviorTrait::geoParse()`. Its `GeoParserRangeException` import names
`LongitudeOne\GeoParser\Exception\RangeException`, which remains available
as the previous exception when a parser range error is translated.
Consumers calling GeoParser directly retain its own exception contract.

`tests/Issue/Issue86ParsingExceptionTest.php` verifies the retained longitude,
latitude, minute and second diagnostics, error codes and GeoParser causes
across XY, XYZ, XYM and XYZM points. Geometry semantics, coordinate layouts,
family/reference behavior and dependency requirements are unchanged; no
normative spatial interpretation is introduced.
