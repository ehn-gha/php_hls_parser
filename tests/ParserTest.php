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

namespace Ehngha\Test\Lib\Hls;

use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use Ehngha\Lib\Hls\Event\BeforeHttpCallEvent;
use Ehngha\Lib\Hls\Exception\ParserException;
use Ehngha\Lib\Hls\Parser;
use Ehngha\Lib\Hls\Tag\TagInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function count;
use function file_get_contents;
use function iterator_to_array;

final class ParserTest extends TestCase
{

    public function testParseMasterAndParsePlaylist(): void
    {
        $tagFoo = $this->getMockBuilder(TagInterface::class)->getMock();
        $tagFoo->expects($this->once())->method("getName")->will($this->returnValue(["FOO", "BAR"]));
        $tagFoo
            ->expects($this->exactly(4))
            ->method("handle")
            ->withConsecutive(
                ["FOO", "", $this->isInstanceOf(PlaylistCollection::class)],
                ["BAR", "20", $this->isInstanceOf(PlaylistCollection::class)],
                ["FOO", "", $this->isInstanceOf(FragmentCollection::class)],
                ["BAR", "20", $this->isInstanceOf(FragmentCollection::class)],
            );
        $tagFoo->expects($this->exactly(8))->method("execute")->withConsecutive(
            [$this->isInstanceOf(Playlist::class)],
            [$this->isInstanceOf(Playlist::class)],
            [$this->isInstanceOf(Playlist::class)],
            [$this->isInstanceOf(Playlist::class)],
            [$this->isInstanceOf(Fragment::class)],
            [$this->isInstanceOf(Fragment::class)],
            [$this->isInstanceOf(Fragment::class)],
            [$this->isInstanceOf(Fragment::class)],
        );
        $tagFoo->expects($this->exactly(4))->method("reset");

        $tagMoz = $this->getMockBuilder(TagInterface::class)->getMock();
        $tagMoz->expects($this->once())->method("getName")->will($this->returnValue("MOZ"));
        $tagMoz
            ->expects($this->exactly(4))
            ->method("handle")
            ->withConsecutive(
                ["MOZ", "POZ", $this->isInstanceOf(PlaylistCollection::class)],
                ["MOZ", "MOZ", $this->isInstanceOf(PlaylistCollection::class)],
                ["MOZ", "POZ", $this->isInstanceOf(FragmentCollection::class)],
                ["MOZ", "MOZ", $this->isInstanceOf(FragmentCollection::class)],
            );
        $tagMoz->expects($this->exactly(4))->method("execute")->withConsecutive(
            [$this->isInstanceOf(Playlist::class)],
            [$this->isInstanceOf(Playlist::class)],
            [$this->isInstanceOf(Fragment::class)],
            [$this->isInstanceOf(Fragment::class)],
        );
        $tagMoz->expects($this->exactly(2))->method("reset");

        $master = new Master(source: file_get_contents(__DIR__ . "/Fixtures/Parser/master.m3u8"));
        $parser = new Parser(
            $this->getMockBuilder(EventDispatcherInterface::class)->getMock(),
            $this->getMockBuilder(HttpClientInterface::class)->getMock(),
            [$tagFoo, $tagMoz]
        );

        $collection = $parser->parseMaster($master);
        $this->assertSame(2, count(iterator_to_array($collection->getPlaylistIterator())));
        $playlist = new Playlist(source: file_get_contents(__DIR__ . "/Fixtures/Parser/playlist.m3u8"));
        $fragments = $parser->parsePlaylist($playlist);
        $this->assertSame(2, count($fragments));
    }

    public function testParseWhenSourceFailedToBeReached(): void
    {
        $this->expectException(ParserException::class);

        $master = new Master(url: "foo://bar.foo");
        $event = new BeforeHttpCallEvent($master);
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $dispatcher->expects($this->once())->method("dispatch")->with($event)->will($this->returnValue($event));
        $client = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $client->expects($this->once())->method("request")->with("GET", "foo://bar.foo", [])->will($this->throwException(new TransportException()));

        $parser = new Parser($dispatcher, $client, []);
        $parser->parseMaster($master);
    }

    public function testParseWhenHlsTagNotFound(): void
    {
        $this->expectException(ParserException::class);

        $entity = new Playlist(source: "foo\nbar");
        $parser = new Parser(
            $this->getMockBuilder(EventDispatcherInterface::class)->getMock(),
            $this->getMockBuilder(HttpClientInterface::class)->getMock(),
            []
        );
        $parser->parsePlaylist($entity);
    }

    public function testBuildParser(): void
    {
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $client = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $parser = Parser::buildParser($dispatcher, $client);

        $reflection = new ReflectionClass($parser);
        foreach (["dispatcher" => $dispatcher, "client" => $client] as $property => $expected) {
            /** @var $attribute ReflectionProperty **/
            $property = $reflection->getProperty($property);
            $property->setAccessible(true);
            $this->assertSame($expected, $property->getValue($parser));
        }

        $tags = $reflection->getProperty("tags");
        $tags->setAccessible(true);
        $this->assertSame(11, count($tags->getValue($parser)));
    }

}
