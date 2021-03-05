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
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use Ehngha\Lib\Hls\Tag\TypeTag;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class TypeTagTest extends TestCase
{

    public function testHandleExecuteReset(): void
    {
        $playlist = new Playlist();
        $tag = new TypeTag();
        $tag->handle("EXT-X-PLAYLIST-TYPE", "FOO", new PlaylistCollection(new Master()));
        $tag->execute($playlist);
        $this->assertSame("FOO", $playlist->attributes[AttributeEnum::TYPE]);
        $tag->reset();

        $reflection = new ReflectionClass($tag);
        $property = $reflection->getProperty("type");
        $property->setAccessible(true);
        $this->assertNull($property->getValue($tag));
    }

    public function testGetName(): void
    {
        $tag = new TypeTag();

        $this->assertSame("EXT-X-PLAYLIST-TYPE", $tag->getName());
    }

}
