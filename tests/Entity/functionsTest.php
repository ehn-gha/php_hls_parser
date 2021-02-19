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

namespace Ehngha\Test\Lib\Hls\Entity;

use Ehngha\Lib\Hls\Entity\Master;
use PHPUnit\Framework\TestCase;

final class functionsTest extends TestCase
{

    public function testSerializeUnserialize(): void
    {
        $master = new Master("foo", "bar");

        $serialized = \Ehngha\Lib\Hls\Entity\serialize($master);
        $this->assertEquals($master, \Ehngha\Lib\Hls\Entity\unserialize($serialized));
    }

}
