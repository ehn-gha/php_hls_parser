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
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use Ehngha\Lib\Hls\Entity\Serializer;
use Ehngha\Lib\Hls\Exception\InvalidEntityException;
use PHPUnit\Framework\TestCase;
use function json_encode;
use function str_repeat;
use function strlen;

final class SerializerTest extends TestCase
{

    public function testUnserializeWhenInvalidEntityGiven(): void
    {
        $this->expectException(InvalidEntityException::class);

        Serializer::unserialize(json_encode(["foo", "bar"]));
    }

    public function testSerializeUnserializeWhenMasterGiven(): void
    {
        $master = new Master("foo", "foo://bar.foo", "foobar", ["foo" => "moz"]);

        $serialized = Serializer::serialize($master);
        $unserialized = Serializer::unserialize($serialized);

        $this->assertSame($master->id, $unserialized->id);
        $this->assertSame($master->url, $unserialized->url);
        $this->assertSame("foobar", $unserialized->source);
        $this->assertSame($master->attributes, $unserialized->attributes);
    }

    public function testSerializeUnserializeWhenPlaylistGiven(): void
    {
        $encryption = new Encryption("key.key?foo=bar");
        $master = new Master("foo", "foo://bar.foo/", attributes: ["foo" => "bar"]);
        $playlist = new Playlist("foo", attributes: ["moz" => "poz"], master: $master);
        $playlist->setUrl("foo/bar/");
        $playlist->setEncryption($encryption);

        $serialized = Serializer::serialize($playlist);
        $unserialized = Serializer::unserialize($serialized);

        $this->assertSame($playlist->id, $unserialized->id);
        $this->assertSame($playlist->url, $unserialized->url);
        $this->assertSame($playlist->attributes, $unserialized->attributes);
        $this->assertSame("foo://bar.foo/foo/bar/key.key?foo=bar", $unserialized->encryption->url);
    }

    public function testSerializeUnserializeWhenEncryptionGiven(): void
    {
        $encryption = new Encryption("foo", "bar", "moz", "poz");

        $serialized = Serializer::serialize($encryption);
        $unserialized = Serializer::unserialize($serialized);

        $this->assertSame($encryption->url, $unserialized->url);
        $this->assertSame($encryption->method, $unserialized->method);
        $this->assertSame($encryption->iv, $unserialized->iv);
        $this->assertSame($encryption->key, $unserialized->key);
    }

    public function testSerializeUnserializeWhenPlaylistCollectionGiven(): void
    {
        $master = new Master("foo", "foo://bar.com/", str_repeat("foo", 100));
        $collection = new PlaylistCollection($master);
        $collection->addPlaylist(new Playlist(source: "bar/foo"));
        $collection->addPlaylist(new Playlist(source: "bar/foo"));
        $collection->addPlaylist(new Playlist(source: "bar/foo"));
        $collection->addRendition(new Playlist(source: "bar/foo"));
        $collection->addRendition(new Playlist(source: "bar/foo"));
        $collection->addRendition(new Playlist(source: "bar/foo"));

        $serialized = Serializer::serialize($collection);
        $this->assertTrue(strlen($serialized) <= 1000);

        $this->assertEquals($collection, Serializer::unserialize($serialized));
    }

    public function testSerializeUnserializeWhenFragmentGiven(): void
    {
        $playlist = new Playlist();
        $playlist->setUrl("foo://bar.foo/");
        $encryption = new Encryption("moz/poz");
        $fragment = new Fragment($playlist);
        $fragment->setEncryption($encryption);
        $fragment->setUrl("foo/bar");

        $serialized = Serializer::serialize($fragment);

        $this->assertEquals($fragment, Serializer::unserialize($serialized));
    }

    public function testSerializeUnserializeWhenFragmentCollectionGiven(): void
    {
        $playlist = new Playlist(source: str_repeat("foo", 100));
        $playlist->setUrl("foo://bar.foo/");
        $collection = new FragmentCollection($playlist);
        $fragmentFoo = new Fragment($playlist);
        $fragmentFoo->setUrl("foo/bar");
        $fragmentBar = new Fragment($playlist);
        $fragmentBar->setUrl("bar/bar");
        $collection[] = $fragmentFoo;
        $collection[] = $fragmentBar;

        $serialized = Serializer::serialize($collection);
        $this->assertTrue(strlen($serialized) <= 600);

        $this->assertEquals($collection, Serializer::unserialize($serialized));
    }

}
