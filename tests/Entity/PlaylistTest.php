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

use Ehngha\Lib\Hls\Entity\Encryption;
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use PHPUnit\Framework\TestCase;

final class PlaylistTest extends TestCase
{

    public function testInitWithMaster(): void
    {
        $master = new Master("foo", attributes: ["foo" => "bar", "moz" => "poz"]);
        $playlist = new Playlist("bar", attributes: ["moz" => "boz"], master: $master);

        $this->assertSame("foo", $playlist->id);
        $this->assertSame(["foo" => "bar", "moz" => "boz"], $playlist->attributes);
    }

    public function testInitWithoutMaster(): void
    {
        $playlist = new Playlist("foo");

        $this->assertSame("foo", $playlist->id);
    }

    public function testSetUrlWithMaster(): void
    {
        $master = new Master("foo", "foo://bar.foo/moz/poz/");
        $playlist = new Playlist(master: $master);

        $playlist->setUrl("foo/bar?moz=poz");

        $this->assertSame("foo://bar.foo/moz/poz/foo/bar?moz=poz", $playlist->url);
    }

    public function testSetUrlWithoutMaster(): void
    {
        $playlist = new Playlist();
        $playlist->setUrl("foo://bar.foo");

        $this->assertSame($playlist->url, "foo://bar.foo");
    }

    public function testSetEncryption(): void
    {
        $encryption = new Encryption("foo/bar", "foo");
        $playlist = new Playlist();
        $playlist->setUrl("foo://bar.foo/");
        $playlist->setEncryption($encryption);

        $playlistEncryption = $playlist->encryption;
        $this->assertSame("foo://bar.foo/foo/bar", $playlistEncryption->url);
        $this->assertSame($encryption->method, $playlistEncryption->method);
    }

    public function testCompareWithBandwith(): void
    {
        $playlistFoo = new Playlist(attributes: ["BANDWIDTH" => 1200]);
        $playlistBar = new Playlist(attributes: ["BANDWIDTH" => 500]);

        $this->assertSame(1, $playlistFoo->compare($playlistBar));
        $this->assertSame(-1, $playlistBar->compare($playlistFoo));

        $playlistBar->attributes["BANDWIDTH"] = 1200;

        $this->assertSame(0, $playlistBar->compare($playlistFoo));
    }

    public function testCompareWithResolution(): void
    {
        $playlistFoo = new Playlist(attributes: ["RESOLUTION" => "1200x500"]);
        $playlistBar = new Playlist(attributes: ["RESOLUTION" => "500x200"]);

        $this->assertSame(1, $playlistFoo->compare($playlistBar));
        $this->assertSame(-1, $playlistBar->compare($playlistFoo));

        $playlistBar->attributes["RESOLUTION"] = "1200x500";

        $this->assertSame(0, $playlistBar->compare($playlistFoo));
    }

    public function testCompareWithNoAttributeInCommon(): void
    {
        $playlistFoo = new Playlist(attributes: ["RESOLUTION" => "1200x500"]);
        $playlistBar = new Playlist();

        $this->assertSame(-1, $playlistBar->compare($playlistFoo));
    }

    public function testClone(): void
    {
        $master = new Master("foo", "foo://bar.foo/moz/poz/");
        $playlist = new Playlist(master: $master);

        $clone = \Ehngha\Lib\Copy\copy($playlist);

        $this->assertEquals($playlist, $clone);
    }

}
