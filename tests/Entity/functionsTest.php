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

use Ehngha\Lib\Hls\Entity\AttributeEnum;
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use PHPUnit\Framework\TestCase;
use function Ehngha\Lib\Hls\Entity\is_live;

final class functionsTest extends TestCase
{

    public function testSerializeUnserialize(): void
    {
        $master = new Master("foo", "bar");

        $serialized = \Ehngha\Lib\Hls\Entity\serialize($master);
        $this->assertEquals($master, \Ehngha\Lib\Hls\Entity\unserialize($serialized));
    }

    public function testIsLive(): void
    {
        $entity = new Playlist();

        $this->assertFalse(is_live($entity));

        $entity = new Playlist(attributes: [AttributeEnum::LIVE => true]);

        $this->assertTrue(is_live($entity));
    }

}
