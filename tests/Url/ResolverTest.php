<?php
//StrictType
declare(strict_types=1);

/*
 * Hls lib
 *
 * Author Allegra Geller <allegra.gl@ehngha.com>
 * Author Nathaniel Demerest <nathaniel.demerest@ehngha.com>
 * Author Kim Jery <kim.jery@ehngha.com>
 *
 */

namespace Ehngha\Test\Lib\Hls\Url;

use Ehngha\Lib\Hls\Exception\ResolveException;
use Ehngha\Lib\Hls\Url\Resolver;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class ResolverTest extends TestCase
{

    public function testResolveWhenBaseUrlRequired(): void
    {
        $this->expectException(ResolveException::class);

        Resolver::resolve("foo/bar");
    }

    public function testResolveWhenAbsolute(): void
    {
        $uri = "foo://bar.foo";

        $this->assertSame($uri, Resolver::resolve($uri));
    }

    public function testResolveWhenNoUrlPartAfterTrim(): void
    {
        $uri = "  ";
        $base = "foo://bar.foo";

        $this->assertSame($base, Resolver::resolve($uri, $base));
    }

    public function testResolveWhenUrlIsAnchor(): void
    {
        $uri = "#foo";
        $base = "foo://bar.foo";
        $expected = "{$base}{$uri}";

        $this->assertSame($expected, Resolver::resolve($uri, $base));
    }

    public function testResolveWhenUrlIsOnlyQuery(): void
    {
        $uri = "?foo=bar";
        $base = "foo://bar.foo";
        $expected = "{$base}{$uri}";

        $this->assertSame($expected, Resolver::resolve($uri, $base));
    }

    public function testResolveWhenSchemeMissing(): void
    {
        $uri = "//foo.bar/foo/bar";
        $base = "foo://foo.com";

        $this->assertSame("foo://foo.bar/foo/bar", Resolver::resolve($uri, $base));
    }

    public function testResolveWhenUrlIsAbsolutePath(): void
    {
        $uri = "/foo/bar";
        $base = "foo://bar.foo/moz/poz";

        $this->assertSame("foo://bar.foo/foo/bar", Resolver::resolve($uri, $base));
    }

    public function testResolve(): void
    {
        $uri = "foo/bar/./../moz/poz?foo=bar&bar=foo#foo";
        $base = "foo://bar.foo/moz/poz/";

        $this->assertSame("foo://bar.foo/moz/poz/foo/moz/poz?foo=bar&bar=foo#foo", Resolver::resolve($uri, $base));
    }

    public function testResolvePathWhenEmpty(): void
    {
        $reflection = new ReflectionClass(Resolver::class);
        $method = $reflection->getMethod("resolvePath");
        $method->setAccessible(true);
        $this->assertSame("", $method->invoke(null, ""));
    }

    public function testResolvePathWhenFolder(): void
    {
        $reflection = new ReflectionClass(Resolver::class);
        $method = $reflection->getMethod("resolvePath");
        $method->setAccessible(true);
        $this->assertSame("foo/bar/", $method->invoke(null, "foo/bar/./."));
    }

}
