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

use ArrayAccess;
use Countable;
use Ehngha\Lib\Hls\Exception\HlsException;
use IteratorAggregate;
use JsonSerializable;
use function array_map;
use function count;

final class FragmentCollection implements IteratorAggregate, JsonSerializable, ArrayAccess, Countable
{

    /**
     * @var array<int, Fragment>
     */
    private array $fragments = [];

    public function __construct(private Playlist $playlist)
    {}

    public function getPlaylist(): Playlist
    {
        return $this->playlist;
    }

    public function getIterator()
    {
        foreach ($this->fragments as $fragment) {
            yield $fragment;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->fragments[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->fragments[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->add($value);
    }

    public function offsetUnset($offset)
    {
        throw new HlsException("Cannot unset fragment from a fragment collection");
    }

    public function jsonSerialize(): array
    {
        return [
            FragmentCollection::class,
            $this->playlist,
            array_map(function(Fragment $fragment): Fragment {
                $fragment = \Ehngha\Lib\Copy\copy($fragment);
                $fragment->playlist = null;

                return $fragment;
            }, $this->fragments)
        ];
    }

    public function count()
    {
        return count($this->fragments);
    }

    private function add(Fragment $fragment): void
    {
        $fragment->playlist = $this->playlist;
        $this->fragments[] = $fragment;
    }

}
