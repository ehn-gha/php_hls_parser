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

use JsonSerializable;
use function Ehngha\Lib\Hls\Url\resolve_url;

final class Fragment implements JsonSerializable
{

    public string $url = "";
    public ?Encryption $encryption = null;

    /**
     * Fragment constructor.
     * @param Playlist|null $playlist
     *   Playlist attached to the fragment
     * @param string $source
     *   Fragment source.
     *   The source is NEVER kept after the serialization process
     * @param array $attributes
     *   Attributes describing the fragment
     */
    public function __construct(
        public ?Playlist $playlist,
        public string $source = "",
        public array $attributes = []
    )
    {
        if (null !== $this->playlist) {
            $this->encryption = $this->playlist->encryption;
        }
    }

    public function setUrl(string $url): void
    {
        $this->url = (null !== $this->playlist) ? resolve_url($url, $this->playlist->url) : $url;
    }

    public function setEncryption(Encryption $encryption): void
    {
        /** @var $encryption Encryption **/
        $encryption = \Ehngha\Lib\Copy\copy($encryption);
        if (null !== $this->playlist) {
            $encryption->url = resolve_url($encryption->url, $this->playlist->url);
        }

        $this->encryption = $encryption;
    }

    public function __clone(): void
    {
        $this->playlist = \Ehngha\Lib\Copy\copy($this->playlist);
        $this->encryption = \Ehngha\Lib\Copy\copy($this->encryption);
    }

    public function jsonSerialize()
    {
        return [
            Fragment::class,
            $this->playlist,
            $this->url,
            $this->attributes,
            $this->encryption
        ];
    }

}
