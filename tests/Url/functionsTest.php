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

use PHPUnit\Framework\TestCase;
use function Ehngha\Lib\Hls\Url\resolve_url;

final class functionsTest extends TestCase
{

    public function testResolveUrl(): void
    {
        $uri = "foo/bar";
        $base = "foo://bar.foo/foo/bar/";
        $expected = "{$base}{$uri}";

        $this->assertSame($expected, resolve_url($uri, $base));
    }

}
