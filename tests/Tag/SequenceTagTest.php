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

use Ehngha\Lib\Hls\Entity\AttributeEnum;
use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Tag\SequenceTag;
use PHPUnit\Framework\TestCase;

final class SequenceTagTest extends TestCase
{

    public function testHandleExecuteReset(): void
    {
        $playlist = new Playlist();
        $collection = new FragmentCollection($playlist);
        $fragmentFoo = new Fragment($playlist);
        $fragmentBar = new Fragment($playlist);
        $tag = new SequenceTag();
        $tag->handle("EXT-X-MEDIA-SEQUENCE", "41", $collection);
        $tag->execute($fragmentFoo);
        $this->assertSame(41, $fragmentFoo->attributes[AttributeEnum::SEQUENCE]);
        $fragmentFoo->attributes = [];
        $tag->execute($fragmentFoo);
        $this->assertSame(42, $fragmentFoo->attributes[AttributeEnum::SEQUENCE]);
        $tag->handle("EXT-X-DISCONTINUITY-SEQUENCE", "20", $collection);
        $tag->handle("EXT-X-DISCONTINUITY", "", $collection);
        $tag->execute(new Playlist());
        $tag->execute($fragmentBar);
        $this->assertSame(20, $fragmentBar->attributes[AttributeEnum::DISCONTINUITY_SEQUENCE]);
        $tag->handle("EXT-X-DISCONTINUITY", "", $collection);
        $fragmentBar->attributes = [];
        $tag->execute($fragmentBar);
        $this->assertSame(21, $fragmentBar->attributes[AttributeEnum::DISCONTINUITY_SEQUENCE]);
        $tag->reset();
    }

    public function testGetName(): void
    {
        $tag = new SequenceTag();

        $this->assertSame(["EXT-X-MEDIA-SEQUENCE", "EXT-X-DISCONTINUITY-SEQUENCE", "EXT-X-DISCONTINUITY"], $tag->getName());
    }

}
