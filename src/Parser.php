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

namespace Ehngha\Lib\Hls;

use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use Ehngha\Lib\Hls\Event\BeforeHttpCallEvent;
use Ehngha\Lib\Hls\Exception\ParserException;
use Ehngha\Lib\Hls\Tag\EndlistTag;
use Ehngha\Lib\Hls\Tag\FragInfTag;
use Ehngha\Lib\Hls\Tag\KeyTag;
use Ehngha\Lib\Hls\Tag\MediaTag;
use Ehngha\Lib\Hls\Tag\SequenceTag;
use Ehngha\Lib\Hls\Tag\StreamInfTag;
use Ehngha\Lib\Hls\Tag\TagInterface;
use Ehngha\Lib\Hls\Tag\VersionTag;
use Generator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_filter;
use function array_map;
use function array_values;
use function array_walk_recursive;
use function explode;
use function is_array;
use function substr;

final class Parser implements ParserInterface
{

    /**
     * @var array<int, array<int, TagInterface>>
     */
    private array $tags = [];

    /**
     * Parser constructor.
     * @param EventDispatcherInterface $dispatcher
     * @param HttpClientInterface $client
     * @param array<int, TagInterface> $tags
     */
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private HttpClientInterface $client,
        array $tags
    )
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
    }

    public function parseMaster(Master $master): PlaylistCollection
    {
        $collection = new PlaylistCollection($master);

        foreach ($this->parseSource($master, $collection, fn(Master $master): Playlist => new Playlist(master: $master)) as $entity) {
            $collection->addPlaylist($entity);
        }

        return $collection;
    }

    public function parsePlaylist(Playlist $playlist): FragmentCollection
    {
        $collection = new FragmentCollection($playlist);

        foreach ($this->parseSource($playlist, $collection, fn(Playlist $playlist): Fragment => new Fragment($playlist)) as $entity) {
            $collection[] = $entity;
        }

        return $collection;
    }

    public function addTag(TagInterface $tag): void
    {
        $name = $tag->getName();
        if (is_array($name)) {
            foreach ($name as $subName) {
                $this->tags[$subName][] = $tag;
            }
        } else {
            $this->tags[$name][] = $tag;
        }
    }

    private function parseSource(
        Master|Playlist $entity,
        PlaylistCollection|FragmentCollection $collection,
        callable $entityFactory): Generator
    {
        foreach ($this->fetchSource($entity) as $line) {
            if ($line[0] === '#') {
                [$tag, $value] = self::explodeTag($line);
                foreach ($this->tags[$tag] ?? [] as $tagInstance) {
                    $tagInstance->handle($tag, $value, $collection);
                }
            } else {
                /** @var $entity Fragment|Playlist **/
                $subEntity = $entityFactory($entity);
                $subEntity->setUrl($line);
                array_walk_recursive($this->tags, function(TagInterface $tag) use ($subEntity): void {
                    $tag->execute($subEntity);
                });
                yield $subEntity;
            }
        }

        array_walk_recursive($this->tags, function(TagInterface $tag): void {
            $tag->reset();
        });
    }

    private function fetchSource(Master|Playlist $entity): array
    {
        if (null === $entity->source) {
            /** @var $event BeforeHttpCallEvent **/
            $event = $this->dispatcher->dispatch(new BeforeHttpCallEvent($entity));
            try {
                $entity->source = $this->client->request($event->httpMethod, $event->entity->url, $event->httpOptions)->getContent();
            } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                throw new ParserException("Entity source at url '{$event->entity->url}' failed to be reached", 0, $e);
            }
        }

        $source = array_values(array_filter(array_map("trim", explode("\n", $entity->source))));
        if ($source[0] !== "#EXTM3U") {
            throw new ParserException("Invalid HLS format. HLS tag '#EXTM3U' is missing.");
        }

        return $source;
    }

    public static function buildParser(EventDispatcherInterface $dispatcher, HttpClientInterface $client): Parser
    {
        return new Parser($dispatcher, $client, [
            new FragInfTag(),
            new KeyTag(),
            new MediaTag(),
            new SequenceTag(),
            new StreamInfTag(),
            new VersionTag(),
            new EndlistTag()
        ]);
    }

    private static function explodeTag(string $tagLine): array
    {
        $tagLine = explode(":", $tagLine, 2);
        $tagLine[0] = substr($tagLine[0], 1);
        $tagLine[1] ??= "";

        return $tagLine;
    }

}
