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

namespace Ehngha\Test\Lib\Hls\Tag;

use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use Ehngha\Lib\Hls\Tag\VersionTag;
use PHPUnit\Framework\TestCase;

final class VersionTagTest extends TestCase
{

    public function testHandleExecuteReset(): void
    {
        $version = "0";
        $master = new Master();
        $collection = new PlaylistCollection($master);
        $entity = new Playlist();

        $tag = new VersionTag();
        $tag->handle("EXT-X-VERSION", $version, $collection);
        $tag->execute($entity);
        $this->assertSame(0, $entity->attributes["VERSION"]);
        $tag->reset();

        $entity = new Playlist();
        $tag->execute($entity);
        $this->assertNull($entity->attributes["VERSION"] ?? null);
    }

    public function testGetName(): void
    {
        $this->assertSame("EXT-X-VERSION", (new VersionTag())->getName());
    }

}
