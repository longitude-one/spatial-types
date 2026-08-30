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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the public metadata contract of every concrete spatial type.
 *
 * @internal
 *
 * @coversNothing
 */
class ConcreteTypeContractTest extends TestCase
{
    /**
     * @param object $spatial configured concrete class instance
     *
     * @throws \LogicException when a configured concrete class violates its interface contract
     */
    private static function asLineString(object $spatial): LineStringInterface
    {
        if (!$spatial instanceof LineStringInterface) {
            throw new \LogicException('Configured line-string class does not implement LineStringInterface.');
        }

        return $spatial;
    }

    /**
     * @param object $spatial configured concrete class instance
     *
     * @throws \LogicException when a configured concrete class violates its interface contract
     */
    private static function asPoint(object $spatial): PointInterface
    {
        if (!$spatial instanceof PointInterface) {
            throw new \LogicException('Configured point class does not implement PointInterface.');
        }

        return $spatial;
    }

    /**
     * @param object $spatial configured concrete class instance
     *
     * @throws \LogicException when a configured concrete class violates its interface contract
     */
    private static function asPolygon(object $spatial): PolygonInterface
    {
        if (!$spatial instanceof PolygonInterface) {
            throw new \LogicException('Configured polygon class does not implement PolygonInterface.');
        }

        return $spatial;
    }

    /**
     * @param object $spatial configured concrete class instance
     *
     * @throws \LogicException when a configured concrete class violates its interface contract
     */
    private static function asSpatial(object $spatial): SpatialInterface
    {
        if (!$spatial instanceof SpatialInterface) {
            throw new \LogicException('Configured spatial class does not implement SpatialInterface.');
        }

        return $spatial;
    }

    /**
     * @param string           $dimension        dimension namespace suffix
     * @param SpatialModelEnum $family           spatial family
     * @param (float|int)[]    $coordinates      point coordinates in the declared layout
     * @param (float|int)[]    $otherCoordinates another point in the declared layout
     *
     * @return array<array{0: SpatialInterface, 1: TypeEnum, 2: array<mixed>}>
     */
    private static function createSpatialTypes(string $dimension, SpatialModelEnum $family, array $coordinates, array $otherCoordinates): array
    {
        $familyNamespace = SpatialModelEnum::GEOMETRY === $family ? 'Geometry' : 'Geography';
        $collectionClass = sprintf('LongitudeOne\SpatialTypes\Types\%s\%s\%sCollection', $dimension, $familyNamespace, $familyNamespace);
        $namespace = sprintf('LongitudeOne\SpatialTypes\Types\%s\%s\\', $dimension, $familyNamespace);
        $pointClass = $namespace.'Point';
        $lineStringClass = $namespace.'LineString';
        $multiPointClass = $namespace.'MultiPoint';
        $multiLineStringClass = $namespace.'MultiLineString';
        $polygonClass = $namespace.'Polygon';
        $multiPolygonClass = $namespace.'MultiPolygon';

        $pointArguments = [...$coordinates, 4326];
        $otherPointArguments = [...$otherCoordinates, 4326];
        $thirdCoordinates = $coordinates;
        ++$thirdCoordinates[0];
        $point = self::asPoint(new $pointClass(...$pointArguments));
        $otherPoint = self::asPoint(new $pointClass(...$otherPointArguments));
        $thirdPoint = self::asPoint(new $pointClass(...[...$thirdCoordinates, 4326]));
        $lineString = self::asLineString(new $lineStringClass([$point, $otherPoint], 4326));
        $ring = self::asLineString(new $lineStringClass([$point, $otherPoint, $thirdPoint, $point], 4326));
        $multiPoint = self::asSpatial(new $multiPointClass([$point, $otherPoint], 4326));
        $multiLineString = self::asSpatial(new $multiLineStringClass([$lineString], 4326));
        $polygon = self::asPolygon(new $polygonClass([$ring], 4326));
        $multiPolygon = self::asSpatial(new $multiPolygonClass([$polygon], 4326));
        $collection = self::asSpatial(new $collectionClass(4326, [$point]));

        return [
            [$point, TypeEnum::POINT, $coordinates],
            [$lineString, TypeEnum::LINESTRING, [$coordinates, $otherCoordinates]],
            [$multiPoint, TypeEnum::MULTIPOINT, [$coordinates, $otherCoordinates]],
            [$multiLineString, TypeEnum::MULTILINESTRING, [[$coordinates, $otherCoordinates]]],
            [$polygon, TypeEnum::POLYGON, [[$coordinates, $otherCoordinates, $thirdCoordinates, $coordinates]]],
            [$multiPolygon, TypeEnum::MULTIPOLYGON, [[[$coordinates, $otherCoordinates, $thirdCoordinates, $coordinates]]]],
            [$collection, TypeEnum::COLLECTION, [$coordinates]],
        ];
    }

    /**
     * @return array<string, array{0: bool, 1: bool, 2: (float|int)[], 3: (float|int)[]}>
     */
    private static function layouts(): array
    {
        return [
            'Dimension2' => [false, false, [1, 2], [4, 5]],
            'Dimension3m' => [true, false, [1, 2, 3], [4, 5, 6]],
            'Dimension3z' => [false, true, [1, 2, 3], [4, 5, 6]],
            'Dimension4zm' => [true, true, [1, 2, 3, 4], [5, 6, 7, 8]],
        ];
    }

    /**
     * Each concrete class must describe both its shape and its coordinate layout.
     *
     * @param SpatialModelEnum $family           spatial family
     * @param bool             $hasM             whether the dimension includes an M ordinate
     * @param bool             $hasZ             whether the dimension includes a Z ordinate
     * @param string           $dimension        dimension namespace suffix
     * @param (float|int)[]    $coordinates      point coordinates in the declared layout
     * @param (float|int)[]    $otherCoordinates another point in the declared layout
     */
    #[DataProvider('provideConcreteSpatialTypes')]
    public function testConcreteTypesExposeTheirPublicContract(
        SpatialModelEnum $family,
        bool $hasM,
        bool $hasZ,
        string $dimension,
        array $coordinates,
        array $otherCoordinates
    ): void {
        $spatialTypes = self::createSpatialTypes($dimension, $family, $coordinates, $otherCoordinates);

        foreach ($spatialTypes as [$spatial, $type, $expectedCoordinates]) {
            static::assertSame($family, $spatial->getFamily());
            static::assertSame($type, $spatial->getType());
            static::assertSame($hasM, $spatial->hasM());
            static::assertSame($hasZ, $spatial->hasZ());
            static::assertSame($expectedCoordinates, $spatial->toArray());
            static::assertSame([
                'type' => $type->value,
                'coordinates' => $expectedCoordinates,
                'srid' => 4326,
            ], $spatial->jsonSerialize());
        }
    }

    /**
     * @return \Generator<string, array{0: SpatialModelEnum, 1: bool, 2: bool, 3: string, 4: (float|int)[], 5: (float|int)[]}, null, void>
     */
    public static function provideConcreteSpatialTypes(): \Generator
    {
        foreach (self::layouts() as $dimension => [$hasM, $hasZ, $coordinates, $otherCoordinates]) {
            foreach ([SpatialModelEnum::GEOMETRY, SpatialModelEnum::GEOGRAPHY] as $family) {
                yield sprintf('%s %s', $dimension, $family->value) => [
                    $family,
                    $hasM,
                    $hasZ,
                    $dimension,
                    $coordinates,
                    $otherCoordinates,
                ];
            }
        }
    }
}
