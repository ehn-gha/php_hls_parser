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

use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Exception\HlsException;
use PHPUnit\Framework\TestCase;
use function array_map;
use function count;
use function iterator_to_array;
use function str_repeat;

final class FragmentCollectionTest extends TestCase
{

    public function testGetPlaylist(): void
    {
        $playlist = new Playlist();
        $collection = new FragmentCollection($playlist);

        $this->assertSame($playlist, $collection->getPlaylist());
    }

    public function testGetIterator(): void
    {
        $playlist = self::generatePlaylist("foo://bar.foo/");
        $collection = new FragmentCollection($playlist);
        $fragmentFoo = new Fragment($playlist);
        $fragmentFoo->setUrl("foo/bar");
        $fragmentBar = new Fragment($playlist);
        $fragmentBar->setUrl("bar/bar");
        $collection[] = $fragmentFoo;
        $collection[] = $fragmentBar;

        $this->assertSame([$fragmentFoo, $fragmentBar], iterator_to_array($collection));
    }

    public function testOffsetExists(): void
    {
        $playlist = self::generatePlaylist("foo://bar.foo/");
        $collection = new FragmentCollection($playlist);
        $this->assertFalse(isset($collection[0]));
        $fragment = new Fragment($playlist);
        $fragment->setUrl("foo/bar");
        $collection[] = $fragment;

        $this->assertTrue(isset($collection[0]));
    }

    public function testOffsetGet(): void
    {
        $playlist = self::generatePlaylist("foo://bar.foo/");
        $collection = new FragmentCollection($playlist);
        $fragment = new Fragment($playlist);
        $fragment->setUrl("foo/bar");
        $collection[] = $fragment;

        $this->assertSame($fragment, $collection[0]);
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(HlsException::class);

        $playlist = self::generatePlaylist("foo://bar.foo/");
        $collection = new FragmentCollection($playlist);

        unset($collection[0]);
    }

    public function testSlice(): void
    {
        $collection = new FragmentCollection(new Playlist());
        for ($i = 0; $i < 50; ++$i) {
            $collection[] = new Fragment(null, "foo_{$i}");
        }
        $collection->slice(5);

        $this->assertSame(5, count($collection));

        $this->assertSame(["foo_45", "foo_46", "foo_47", "foo_48", "foo_49"], array_map(function(Fragment $fragment): string {
            return $fragment->source;
        }, iterator_to_array($collection->getIterator())));
    }

    public function testCount(): void
    {
        $playlist = self::generatePlaylist("foo://bar.foo/");
        $collection = new FragmentCollection($playlist);
        $this->assertSame(0, count($collection));
        $fragmentFoo = new Fragment($playlist);
        $fragmentFoo->setUrl("foo/bar");
        $fragmentBar = new Fragment($playlist);
        $fragmentBar->setUrl("bar/bar");
        $collection[] = $fragmentFoo;
        $collection[] = $fragmentBar;

        $this->assertSame(2, count($collection));
    }

    private static function generatePlaylist(string $url): Playlist
    {
        $playlist = new Playlist(source: str_repeat("foo", 1000));
        $playlist->setUrl($url);

        return $playlist;
    }

}
