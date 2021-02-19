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
use Ehngha\Lib\Hls\Tag\FragInfTag;
use PHPUnit\Framework\TestCase;

final class FragInfTagTest extends TestCase
{

    public function testHandleExecuteResestWhenEXInfTag(): void
    {
        $tag = "EXTINF";
        $value = "3.00,FOO";
        $playlist = new Playlist();
        $collection = new FragmentCollection($playlist);
        $fragment = new Fragment($playlist);

        $tagInstance = new FragInfTag();
        $tagInstance->handle($tag, $value, $collection);
        $tagInstance->execute($fragment);

        $this->assertSame(3.00, $fragment->attributes["DURATION"]);
        $this->assertSame("FOO", $fragment->attributes["TITLE"]);
        $tagInstance->reset();

        $fragment = new Fragment($playlist);
        $tagInstance->execute($fragment);

        $this->assertNull($fragment->attributes["DURATION"] ?? null);
        $this->assertNull($fragment->attributes["TITLE"] ?? null);
    }

    public function testHandleExecuteResestWhenDatetimeTag(): void
    {
        $tag = "EXT-X-PROGRAM-DATE-TIME";
        $value = "2000-05-25T04:54:21.000+00:00";
        $playlist = new Playlist();
        $collection = new FragmentCollection($playlist);
        $fragment = new Fragment($playlist);

        $tagInstance = new FragInfTag();
        $tagInstance->handle($tag, $value, $collection);
        $tagInstance->execute($fragment);

        $this->assertSame(959230461, $fragment->attributes["DATETIME"]);
        $tagInstance->reset();

        $fragment = new Fragment($playlist);
        $tagInstance->execute($fragment);

        $this->assertNull($fragment->attributes["DATETIME"] ?? null);

        $tag = "EXT-X-PROGRAM-DATE-TIME";
        $value = "05-25T04:54:21";
        $tagInstance->handle($tag, $value, $collection);
        $tagInstance->execute($fragment);

        $this->assertNull($fragment->attributes["DATETIME"] ?? null);
    }

    public function testGetName(): void
    {
        $this->assertSame(["EXTINF", "EXT-X-PROGRAM-DATE-TIME"], (new FragInfTag())->getName());
    }

}
