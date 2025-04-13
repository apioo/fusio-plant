<?php

namespace App\Tests;

use App\Exception\PortResolveException;
use App\Service\Project\PortNumberResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PortNumberResolverTest extends TestCase
{
    #[DataProvider('portResolverProvider')]
    public function testResolve(int $id, int $index, int $expect)
    {
        self::assertSame($expect, (new PortNumberResolver())->resolve($id, $index));
    }

    public static function portResolverProvider(): array
    {
        return [
            [1, 1, 9011],
            [9, 1, 9091],
            [10, 1, 9101],
            [99, 1, 9991],
            [100, 1, 11001],
            [999, 1, 19991],
            [1000, 1, 20001],
            [5000, 1, 60001],
        ];
    }

    #[DataProvider('portResolverFailureProvider')]
    public function testResolveFailure(int $id, int $index, int $expect)
    {
        $this->expectException(PortResolveException::class);

        self::assertSame($expect, (new PortNumberResolver())->resolve($id, $index));
    }

    public static function portResolverFailureProvider(): array
    {
        return [
            [1, 10, 9011], // max 10 services per project
            [6000, 1, 60001], // out of range
        ];
    }
}
