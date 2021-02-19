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

namespace Ehngha\Lib\Hls\Entity;

use Closure;
use Generator;
use JsonSerializable;
use function array_map;
use function is_int;
use function usort;

final class PlaylistCollection implements JsonSerializable
{

    /**
     * @var array<int, Playlist>
     */
    private array $playlists = [];
    /**
     * @var array<int, Playlist>
     */
    private array $renditions = [];
    private ?int $current = null;
    private int $size = 0;

    public function __construct(private Master $master)
    {}

    public function getMaster(): Master
    {
        return $this->master;
    }

    public function getPlaylistIterator(): Generator
    {
        foreach ($this->playlists as $playlist) {
            yield $playlist;
        }
    }

    public function getRenditionIterator(): Generator
    {
        foreach ($this->renditions as $rendition) {
            yield $rendition;
        }
    }

    public function getPlaylist(string|int $index): Playlist|Closure|null
    {
        return self::fetch($index, $this->playlists);
    }

    public function getRendition(string|int $index): Playlist|Closure|null
    {
        return self::fetch($index, $this->renditions);
    }

    public function addPlaylist(Playlist $playlist): void
    {
        $playlist->setMaster($this->master);
        $this->playlists[] = $playlist;
        usort($this->playlists, function(Playlist $playlistI, Playlist $playlistJ): int {
            return $playlistJ->compare($playlistI);
        });
        ++$this->size;
    }

    public function addRendition(Playlist $playlist): void
    {
        $playlist->setMaster($this->master);
        $this->renditions[] = $playlist;
    }

    public function getBestQuality(): Playlist
    {
        $this->current = 0;

        return $this->playlists[$this->current];
    }

    public function getLowestQuality(): Playlist
    {
        $this->current = $this->size - 1;

        return $this->playlists[$this->current];
    }

    public function upQuality(): Playlist
    {
        return (0 === $this->current) ? $this->playlists[0] : $this->playlists[--$this->current];
    }

    public function lowerQuality(): Playlist
    {
        return ($this->current === ($this->size - 1)) ? $this->playlists[$this->current] : $this->playlists[++$this->current];
    }

    public function jsonSerialize(): array
    {
        return [
            PlaylistCollection::class,
            self::clearPlaylists($this->playlists),
            self::clearPlaylists($this->renditions),
            $this->current,
            $this->size,
            $this->master
        ];
    }

    public static function makeFromJson(array $json): PlaylistCollection
    {
        $collection = new PlaylistCollection(unserialize($json[5]));
        self::restorePlaylist($json[1], $collection->playlists, $collection->master);
        self::restorePlaylist($json[2], $collection->renditions, $collection->master);
        $collection->current = $json[3];
        $collection->size = $json[4];

        return $collection;
    }

    private static function clearPlaylists(array $playlists): array
    {
        return array_map(function(Playlist $playlist): Playlist {
            $playlist = \Ehngha\Lib\Copy\copy($playlist);
            $playlist->master = null;

            return $playlist;
        }, $playlists);
    }

    private static function restorePlaylist(array $playlists, array& $container, Master $master): void
    {
        foreach ($playlists as $playlist) {
            $playlist = unserialize($playlist);
            $playlist->master = $master;
            $container[] = $playlist;
        }
    }

    /**
     * @param string|int $index
     * @param array<int, Playlist> $collection
     * @return Playlist|Closure|null
     */
    private static function fetch(string|int $index, array $collection): Playlist|Closure|null
    {
        if (is_int($index)) {
            return $collection[$index] ?? null;
        }

        return function(mixed $value) use ($index, $collection): ?Playlist {
            foreach ($collection as $playlist) {
                if ( ($playlist->attributes[$index] ?? null) === $value) {
                    return $playlist;
                }
            }

            return null;
        };
    }

}
