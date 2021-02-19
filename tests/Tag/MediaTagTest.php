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
use Ehngha\Lib\Hls\Tag\MediaTag;
use PHPUnit\Framework\TestCase;

final class MediaTagTest extends TestCase
{

    public function testHandle(): void
    {
        $collection = new PlaylistCollection(new Master());
        $attributes = 'FOO=BAR,URI="foo://bar.foo",MOZ=POZ';
        $tag = new MediaTag();
        $tag->handle("EXT-X-MEDIA", $attributes, $collection);

        $rendition = $collection->getRendition(0);
        $this->assertNotNull($rendition);
        $this->assertSame("foo://bar.foo", $rendition->url);
        $this->assertSame(["FOO" => "BAR", "MOZ" => "POZ"], $rendition->attributes);
        $tag->execute(new Playlist());
        $tag->reset();
    }

    public function testHandleWhenNoUriFound(): void
    {
        $collection = new PlaylistCollection(new Master());
        $attributes = 'FOO=BAR,MOZ=POZ';
        $tag = new MediaTag();
        $tag->handle("EXT-X-MEDIA", $attributes, $collection);

        $this->assertNull($collection->getRendition(0));
    }

    public function testGetName(): void
    {
        $this->assertSame("EXT-X-MEDIA", (new MediaTag())->getName());
    }

}
