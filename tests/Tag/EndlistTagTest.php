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

use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Tag\EndlistTag;
use PHPUnit\Framework\TestCase;

final class EndlistTagTest extends TestCase
{

    public function testHandleExecuteReset(): void
    {
        $playlist = new Playlist();
        $collection = new FragmentCollection($playlist);
        $tag = new EndlistTag();
        $tag->handle("EXT-X-ENDLIST", "", $collection);
        $this->assertFalse($playlist->attributes["IS_LIVE"]);
        $tag->execute(new Fragment($playlist));
        $tag->reset();
    }

    public function testGetName(): void
    {
        $this->assertSame("EXT-X-ENDLIST", (new EndlistTag())->getName());
    }

}
