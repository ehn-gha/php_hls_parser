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
use Ehngha\Lib\Hls\Tag\StreamInfTag;
use PHPUnit\Framework\TestCase;

final class StreamInfTagTest extends TestCase
{

    public function testExecuteApplyReset(): void
    {
        $attributes = "FOO=BAR,BAR=\"foo,bar\"";
        $tag = new StreamInfTag();
        $tag->handle("EXT-X-STREAM-INF", $attributes, new PlaylistCollection(new Master()));
        $playlist = new Playlist();
        $tag->execute($playlist);

        $this->assertSame(["BAR" => "foo,bar", "FOO" => "BAR"], $playlist->attributes);
        $tag->reset();

        $playlist = new Playlist();
        $tag->execute($playlist);

        $this->assertSame([], $playlist->attributes);
    }

    public function testGetName(): void
    {
        $this->assertSame("EXT-X-STREAM-INF", (new StreamInfTag())->getName());
    }

}
