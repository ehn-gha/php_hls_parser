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

namespace Ehngha\Test\Lib\Hls;

use PHPUnit\Framework\TestCase;
use function Ehngha\Lib\Hls\generate_hls_tag_attributes;

final class functionsTest extends TestCase
{

    public function testGenerateHlsTagAttributes(): void
    {
        $attributes = 'FOO=BAR,MOZ=POZxLOZ,NOZ="foo,bar"';

        $this->assertSame(["NOZ" => "foo,bar", "FOO" => "BAR", "MOZ" => "POZxLOZ"], generate_hls_tag_attributes($attributes));
    }

}
