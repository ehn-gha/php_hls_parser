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
use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\Playlist;
use PHPUnit\Framework\TestCase;

final class FragmentTest extends TestCase
{

    public function testInitWithPlaylistWhichContainsEncryption(): void
    {
        $encryption = new Encryption("foo://bar.foo");
        $playlist = new Playlist();
        $playlist->setEncryption($encryption);

        $fragment = new Fragment($playlist);

        $this->assertEquals($encryption, $fragment->encryption);
    }

    public function testSetUrl(): void
    {
        $playlist = new Playlist();
        $playlist->setUrl("foo://bar.foo/foo/bar/");
        $fragment = new Fragment($playlist);
        $fragment->setUrl("moz/poz/frag.frag");

        $this->assertSame("foo://bar.foo/foo/bar/moz/poz/frag.frag", $fragment->url);
    }

    public function testSetEncryption(): void
    {
        $playlist = new Playlist();
        $playlist->setUrl("foo://bar.foo/");
        $encryption = new Encryption("foo/bar");
        $fragment = new Fragment($playlist);
        $fragment->setEncryption($encryption);

        $this->assertSame("foo://bar.foo/foo/bar", $fragment->encryption->url);
        $this->assertSame("foo/bar", $encryption->url);
    }

    public function testClone(): void
    {
        $playlist = new Playlist();
        $encryption = new Encryption();

        $fragment = new Fragment($playlist);
        $fragment->encryption = $encryption;

        $copy = \Ehngha\Lib\Copy\copy($fragment);

        $this->assertEquals($playlist, $copy->playlist);
        $this->assertEquals($encryption, $copy->encryption);
    }

}
