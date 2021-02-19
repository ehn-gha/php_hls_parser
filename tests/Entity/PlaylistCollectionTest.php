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
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

final class PlaylistCollectionTest extends TestCase
{

    public function testGetMaster(): void
    {
        $master = new Master();
        $collection = new PlaylistCollection($master);

        $this->assertSame($master, $collection->getMaster());
    }

    public function testAddGetGetIterator(): void
    {
        $master = new Master("foo", "foo://bar.foo/");
        $playlistFoo = new Playlist(attributes: ["BANDWIDTH" => 120]);
        $playlistFoo->setUrl("moz/poz");
        $playlistBar = new Playlist(attributes: ["BANDWIDTH" => 1200]);
        $playlistBar->setUrl("baz/poz");
        $playlistMoz = new Playlist(attributes: ["BANDWIDTH" => 520]);
        $playlistMoz->setUrl("doz/poz");
        $collection = new PlaylistCollection($master);
        $collection->addPlaylist($playlistFoo);
        $collection->addPlaylist($playlistBar);
        $collection->addPlaylist($playlistMoz);
        $renditionFoo = new Playlist(attributes: ["DEFAULT" => "1"]);
        $renditionFoo->setUrl("foo://bar.foo");
        $renditionBar = new Playlist();
        $renditionBar->setUrl("bar://bar.foo");
        $collection->addRendition($renditionFoo);
        $collection->addRendition($renditionBar);

        $this->assertSame([$playlistBar, $playlistMoz, $playlistFoo], iterator_to_array($collection->getPlaylistIterator()));
        $this->assertSame([$renditionFoo, $renditionBar], iterator_to_array($collection->getRenditionIterator()));

        $this->assertEquals($playlistMoz, $collection->getPlaylist(1));
        $this->assertEquals($playlistFoo, $collection->getPlaylist("BANDWIDTH")(120));
        $this->assertSame($renditionBar, $collection->getRendition(1));
        $this->assertSame($renditionFoo, $collection->getRendition("DEFAULT")("1"));

        $this->assertNull($collection->getPlaylist(42));
        $this->assertNull($collection->getRendition(42));
        $this->assertNull($collection->getPlaylist("BANDWIDTH")(45200));
        $this->assertNull($collection->getRendition("BANDWIDTH")(45200));
    }

    public function testHandleQuality(): void
    {
        $master = new Master("foo", "foo://bar.foo/");
        $playlistFoo = new Playlist(attributes: ["BANDWIDTH" => 120]);
        $playlistFoo->setUrl("moz/poz");
        $playlistBar = new Playlist(attributes: ["BANDWIDTH" => 1200]);
        $playlistBar->setUrl("baz/poz");
        $playlistMoz = new Playlist(attributes: ["BANDWIDTH" => 520]);
        $playlistMoz->setUrl("doz/poz");
        $collection = new PlaylistCollection($master);
        $collection->addPlaylist($playlistFoo);
        $collection->addPlaylist($playlistBar);
        $collection->addPlaylist($playlistMoz);

        $this->assertSame($playlistBar, $collection->getBestQuality());
        $this->assertSame($playlistFoo, $collection->getLowestQuality());
        $this->assertSame($playlistMoz, $collection->upQuality());
        $this->assertSame($playlistBar, $collection->upQuality());
        $this->assertSame($playlistBar, $collection->upQuality());
        $this->assertSame($playlistMoz, $collection->lowerQuality());
        $this->assertSame($playlistFoo, $collection->lowerQuality());
        $this->assertSame($playlistFoo, $collection->lowerQuality());
    }

}