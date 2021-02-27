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
use Ehngha\Lib\Hls\Exception\LogicException;
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

    public function testAddGetGetIteratorCount(): void
    {
        $master = new Master("foo", "foo://bar.foo/");
        $playlistFoo = new Playlist(attributes: ["BANDWIDTH" => 120]);
        $playlistFoo->setUrl("moz/poz");
        $playlistBar = new Playlist(attributes: ["BANDWIDTH" => 1200]);
        $playlistBar->setUrl("baz/poz");
        $playlistMoz = new Playlist(attributes: ["BANDWIDTH" => 520]);
        $playlistMoz->setUrl("doz/poz");
        $collection = new PlaylistCollection($master);
        $this->assertSame(0, $collection->countPlaylists());
        $collection->addPlaylist($playlistFoo);
        $collection->addPlaylist($playlistBar);
        $collection->addPlaylist($playlistMoz);
        $this->assertSame(3, $collection->countPlaylists());
        $renditionFoo = new Playlist(attributes: ["DEFAULT" => "1"]);
        $renditionFoo->setUrl("foo://bar.foo");
        $renditionBar = new Playlist();
        $renditionBar->setUrl("bar://bar.foo");
        $this->assertSame(0, $collection->countRenditions());
        $collection->addRendition($renditionFoo);
        $collection->addRendition($renditionBar);
        $this->assertSame(2, $collection->countRenditions());

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

        $this->assertSame($playlistBar, $collection->removePlaylist(0));
        $this->assertSame($renditionFoo, $collection->removeRendition(0));

        $this->assertNull($collection->getPlaylist(0));
        $this->assertNull($collection->getRendition(0));
    }

    public function testExtractPlaylists(): void
    {
        $playlistFoo = new Playlist();
        $playlistBar = new Playlist();
        $playlistMoz = new Playlist();
        $collection = new PlaylistCollection(new Master());
        foreach ([$playlistFoo, $playlistBar, $playlistMoz] as $playlist) {
            $collection->addPlaylist($playlist);
        }

        $extracted = $collection->extractPlaylists(0, 2);

        $this->assertSame($playlistFoo, $extracted->getPlaylist(0));
        $this->assertSame($playlistMoz, $extracted->getPlaylist(1));

        $this->assertSame(2, $extracted->countPlaylists());
    }

    public function testExtractPlaylistsWhenIndexInvalid(): void
    {
        $this->expectException(LogicException::class);

        $collection = new PlaylistCollection(new Master());
        $collection->extractPlaylists(0);
    }

    public function testExtractRenditions(): void
    {
        $renditionFoo = new Playlist();
        $renditionBar = new Playlist();
        $renditionMoz = new Playlist();
        $collection = new PlaylistCollection(new Master());
        foreach ([$renditionFoo, $renditionBar, $renditionMoz] as $rendition) {
            $collection->addRendition($rendition);
        }

        $extracted = $collection->extractRenditions(0, 2);
        $this->assertSame($renditionFoo, $extracted->getRendition(0));
        $this->assertSame($renditionMoz, $extracted->getRendition(1));

        $this->assertSame(2, $extracted->countRenditions());
    }

    public function testExtractRenditionsWhenIndexInvalid(): void
    {
        $this->expectException(LogicException::class);

        $collection = new PlaylistCollection(new Master());
        $collection->extractRenditions(0);
    }

    public function testMerge(): void
    {
        $master = new Master();
        $collectionFoo = new PlaylistCollection($master);
        $collectionFoo->addPlaylist(new Playlist());
        $collectionFoo->addRendition(new Playlist());
        $collectionBar = new PlaylistCollection($master);
        $collectionBar->addPlaylist(new Playlist());
        $collectionBar->addRendition(new Playlist());

        $merged = $collectionFoo->merge($collectionBar);

        $this->assertSame(2, $merged->countPlaylists());
        $this->assertSame(2, $merged->countRenditions());
    }

    public function testMergeWhenMasterNotCompatible(): void
    {
        $this->expectException(LogicException::class);

        $collectionFoo = new PlaylistCollection(new Master("foo"));
        $collectionBar = new PlaylistCollection(new Master("bar"));

        $collectionFoo->merge($collectionBar);
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
