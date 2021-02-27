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
use Ehngha\Lib\Hls\Exception\LogicException;
use Generator;
use JsonSerializable;
use function array_map;
use function count;
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

    /**
     * Get the master assigned to the playlist collection
     * @return Master
     */
    public function getMaster(): Master
    {
        return $this->master;
    }

    /**
     * Generate an iterator for iterating over the setted playlists
     * @return Generator
     */
    public function getPlaylistIterator(): Generator
    {
        foreach ($this->playlists as $playlist) {
            yield $playlist;
        }
    }

    /**
     * Generate an iterator for iterating over the setted renditions
     * @return Generator
     */
    public function getRenditionIterator(): Generator
    {
        foreach ($this->renditions as $rendition) {
            yield $rendition;
        }
    }

    /**
     * Get a playlist by its index or a criteria
     * @param string|int $index
     * @return Playlist|Closure|null
     */
    public function getPlaylist(string|int $index): Playlist|Closure|null
    {
        return self::fetch($index, $this->playlists, true);
    }

    /**
     * Get a rendition by its index or a criteria
     * @param string|int $index
     * @return Playlist|Closure|null
     */
    public function getRendition(string|int $index): Playlist|Closure|null
    {
        return self::fetch($index, $this->renditions, false);
    }

    /**
     * Add a playlist and assign the master to it
     * @param Playlist $playlist
     */
    public function addPlaylist(Playlist $playlist): void
    {
        $playlist->setMaster($this->master);
        $this->playlists[] = $playlist;
        usort($this->playlists, function(Playlist $playlistI, Playlist $playlistJ): int {
            return $playlistJ->compare($playlistI);
        });
        ++$this->size;
    }

    /**
     * Add an rendition and assign the master to it
     * @param Playlist $playlist
     */
    public function addRendition(Playlist $playlist): void
    {
        $playlist->setMaster($this->master);
        $this->renditions[] = $playlist;
    }

    /**
     * Remove a playlist from the collection by its index
     * @param int $index
     * @return Playlist|null
     *   The removed playlist
     */
    public function removePlaylist(int $index): ?Playlist
    {
        $playlist = $this->playlists[$index] ?? null;
        unset($this->playlists[$index]);

        return $playlist;
    }

    /**
     * Remove a rendition from the collection by its index
     * @param int $index
     * @return Playlist|null
     *   The removed rendition
     */
    public function removeRendition(int $index): ?Playlist
    {
        $playlist = $this->renditions[$index];
        unset($this->renditions[$index]);

        return $playlist;
    }

    /**
     * Extract playlists from the collection by their indexes
     * Playlists are never sorted into the generated collection
     * @param int ...$playlists
     * @return PlaylistCollection
     * @throws LogicException
     */
    public function extractPlaylists(int... $playlists): PlaylistCollection
    {
        $collection = new PlaylistCollection($this->master);
        foreach ($playlists as $playlist) {
            if (!isset($this->playlists[$playlist])) {
                throw new LogicException("No playlist found at index '{$playlist}'");
            }
            $collection->playlists[] = $this->playlists[$playlist];
        }

        return $collection;
    }

    /**
     * Extract renditions from the collection by their indexes
     * @param int ...$renditions
     * @return PlaylistCollection
     * @throws LogicException
     */
    public function extractRenditions(int... $renditions): PlaylistCollection
    {
        $collection = new PlaylistCollection($this->master);
        foreach ($renditions as $rendition) {
            if (!isset($this->renditions[$rendition])) {
                throw new LogicException("No rendition found at index '{$rendition}'");
            }
            $collection->renditions[] = $this->renditions[$rendition];
        }

        return $collection;
    }

    /**
     * Merge this collection with the given one.
     * Playlists are never copied
     * @param PlaylistCollection $collection
     * @return PlaylistCollection
     * @throws LogicException
     */
    public function merge(PlaylistCollection $collection): PlaylistCollection
    {
        if ($collection->master != $this->master) {
            throw new LogicException("Given collection master is not compatible with the current collection one");
        }

        $merged = new PlaylistCollection($this->master);
        foreach (["addPlaylist" => "playlists", "addRendition" => "renditions"] as $method => $property) {
            foreach ([$this->{$property}, $collection->{$property}] as $playlists) {
                foreach ($playlists as $playlist) {
                    $merged->{$method}($playlist);
                }
            }
        }

        return $merged;
    }

    /**
     * Try to determinate the best quality and return it
     * @return Playlist
     */
    public function getBestQuality(): Playlist
    {
        $this->current = 0;

        return $this->playlists[$this->current];
    }

    /**
     * Try to determinate the lowest quality and return it
     * @return Playlist
     */
    public function getLowestQuality(): Playlist
    {
        $this->current = $this->size - 1;

        return $this->playlists[$this->current];
    }

    /**
     * Up the quality considering the current playlist lastly getted
     * @return Playlist
     */
    public function upQuality(): Playlist
    {
        return (0 === $this->current) ? $this->playlists[0] : $this->playlists[--$this->current];
    }

    /**
     * Lower the quality considering the current playlist lastly getted
     * @return Playlist
     */
    public function lowerQuality(): Playlist
    {
        return ($this->current === ($this->size - 1)) ? $this->playlists[$this->current] : $this->playlists[++$this->current];
    }

    /**
     * Count the setted playlists
     * @return int
     */
    public function countPlaylists(): int
    {
        return (0 !== $this->size) ? $this->size : ($this->size = count($this->playlists));
    }

    /**
     * Count the setted renditions
     * @return int
     */
    public function countRenditions(): int
    {
        return count($this->renditions);
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

    private function fetch(string|int $index, array $collection, bool $overwriteCurrent): Playlist|Closure|null
    {
        if (is_int($index)) {
            if ($overwriteCurrent) {
                $this->current = $index;
            }

            return $collection[$index] ?? null;
        }

        return function(mixed $value) use ($index, $collection, $overwriteCurrent): ?Playlist {
            foreach ($collection as $currentIndex => $playlist) {
                if ( ($playlist->attributes[$index] ?? null) === $value) {
                    if ($overwriteCurrent) {
                        $this->current = $currentIndex;
                    }
                    return $playlist;
                }
            }

            return null;
        };
    }

}
