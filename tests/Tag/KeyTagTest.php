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
use Ehngha\Lib\Hls\Tag\KeyTag;
use PHPUnit\Framework\TestCase;

final class KeyTagTest extends TestCase
{

    public function testHandleExecuteReset(): void
    {
        $attribute = 'URI="foo://bar.foo",METHOD=FOO,IV=FOO';
        $playlist = new Playlist();
        $collection = new FragmentCollection($playlist);
        $entity = new Fragment($playlist);

        $tag = new KeyTag();
        $tag->handle("EXT-X-KEY", $attribute, $collection);
        $tag->execute($entity);

        $encryption = $entity->encryption;
        $this->assertNotNull($encryption);
        $this->assertSame("foo://bar.foo", $encryption->url);
        $this->assertSame("FOO", $encryption->method);
        $this->assertSame("FOO", $encryption->iv);
        $tag->reset();

        $entity = new Fragment($playlist);
        $tag->execute($entity);

        $this->assertNull($entity->encryption);
    }

    public function testHandleWhenNoUriFound(): void
    {
        $attribute = 'METHOD=FOO,IV=FOO';
        $playlist = new Playlist();
        $collection = new FragmentCollection($playlist);
        $entity = new Fragment($playlist);
        $tag = new KeyTag();
        $tag->handle("EXT-X-KEY", $attribute, $collection);
        $tag->execute($entity);

        $this->assertNull($entity->encryption);
    }

    public function testGetName(): void
    {
        $this->assertSame(["EXT-X-KEY", "EXT-X-SESSION-KEY"], (new KeyTag())->getName());
    }

}
