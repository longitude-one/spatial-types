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

namespace LongitudeOne\SpatialTypes\Tests\Functional\Api;

use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the documented API boundary against every library symbol.
 *
 * @internal
 */
#[CoversNothing]
class ApiBoundaryTest extends TestCase
{
    /** Preserve the approved consumer-facing interfaces, types and support objects. */
    public function testConsumerFacingGroupsRemainPublic(): void
    {
        foreach ($this->inventory() as $symbol => $status) {
            $reflection = new \ReflectionClass($symbol);
            if (1 === preg_match('/\\\(?:Interfaces|Exception|Builder|Implementation|Reference|Value)\\\/', $symbol)
                || str_contains($symbol, '\Types\\') && !$reflection->isAbstract() && !$reflection->isTrait()
                && !str_ends_with($symbol, 'Guard') && !str_ends_with($symbol, 'Hydrator')) {
                static::assertSame('public', $status, $symbol);
            }
        }
    }

    /** Keep all type-specific factories public and SpatialFactory's boundary explicit. */
    public function testFactoryPublicContractsRemainDiscoverable(): void
    {
        $factories = 0;
        foreach ($this->inventory() as $symbol => $status) {
            if (1 === preg_match('/\\\Factory\\\Dimension[^\\\]+\\\(?:Geometry|Geography)\\\/', $symbol)) {
                static::assertSame('public', $status, $symbol);
                ++$factories;
            }
        }
        static::assertSame(84, $factories);
        $publicMethods = [];
        $reflection = new \ReflectionClass(SpatialFactory::class);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!str_contains((string) $method->getDocComment(), '@internal')) {
                static::assertTrue($method->isStatic(), $method->getName());
                $publicMethods[] = $method->getName();
            }
        }
        sort($publicMethods);
        static::assertSame(['immutable', 'isMutable', 'mutable'], $publicMethods);
    }

    /** Verify that additions, removals and namespace moves update the inventory. */
    public function testInventoryAccountsForEveryLibrarySymbol(): void
    {
        $directory = new \RecursiveDirectoryIterator(__DIR__.'/../../../lib');
        $symbols = [];
        foreach (new \RecursiveIteratorIterator($directory) as $file) {
            static::assertInstanceOf(\SplFileInfo::class, $file);
            if ('php' === $file->getExtension()) {
                $relative = mb_substr($file->getPathname(), mb_strlen(__DIR__.'/../../../lib/'), -4);
                $symbols[] = 'LongitudeOne\SpatialTypes\\'.str_replace('/', '\\', $relative);
            }
        }
        sort($symbols);
        static::assertSame(array_keys($this->inventory()), $symbols);
    }

    /** Verify explicit internal markers without inheriting a parent's annotation. */
    public function testInventoryMatchesSymbolMarkersAndNamespaceRules(): void
    {
        foreach ($this->inventory() as $symbol => $status) {
            $reflection = new \ReflectionClass($symbol);
            $markedInternal = str_contains((string) $reflection->getDocComment(), '@internal');
            static::assertContains($status, ['public', 'internal'], $symbol);
            static::assertSame('internal' === $status, $markedInternal, $symbol);
            if ($reflection->isAbstract() && !$reflection->isInterface() || $reflection->isTrait()
                || str_contains($symbol, '\Internal\\')) {
                static::assertSame('internal', $status, $symbol);
            }
            static::assertStringNotContainsString('\Factory\Internal\\', $symbol);
            static::assertStringNotContainsString('\Internal\Internal\\', $symbol);
            static::assertStringNotContainsString('\Factory\Hydrator\\', $symbol);
        }
    }

    /**
     * Read the authoritative, consumer-discoverable inventory.
     *
     * @return array<class-string, string>
     */
    private function inventory(): array
    {
        $contents = file_get_contents(__DIR__.'/../../../docs/api-symbols.json');
        static::assertIsString($contents);
        $inventory = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        static::assertIsArray($inventory);
        $symbols = [];
        foreach ($inventory as $symbol => $status) {
            static::assertIsString($symbol);
            static::assertIsString($status);
            if (!class_exists($symbol) && !interface_exists($symbol) && !trait_exists($symbol)) {
                static::fail('Inventory symbol cannot be autoloaded: '.$symbol);
            }
            $symbols[$symbol] = $status;
        }

        return $symbols;
    }
}
