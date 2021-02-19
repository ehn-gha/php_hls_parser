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
use function array_merge;
use function Ehngha\Lib\Copy\copy;
use function Ehngha\Lib\Hls\Url\resolve_url;
use function explode;

final class Playlist implements JsonSerializable
{

    public string $url = "";
    public ?Encryption $encryption = null;
    public ?Master $master = null;

    /**
     * Playlist constructor.
     * @param string $id
     *   Channel id. This id will be overwritten if a master is given
     * @param string|null $source
     *   Cached source
     * @param array $attributes
     *   Playlist attributes
     * @param Master|null $master
     *   A master attached to the playlist
     */
    public function __construct(
        public string $id = "",
        public ?string $source = null,
        public array $attributes = [],
        ?Master $master = null
    )
    {
        if (null !== $master) {
            $this->setMaster($master);
        }
    }

    public function setUrl(string $url): void
    {
        $this->url = (null !== $this->master) ? resolve_url($url, $this->master->url) : $url;
    }

    public function setEncryption(Encryption $encryption): void
    {
        /** @var $encryption Encryption **/
        $encryption = copy($encryption);
        $encryption->url = resolve_url($encryption->url, $this->url);

        $this->encryption = $encryption;
    }

    public function setMaster(Master $master): void
    {
        $this->master = $master;

        $this->id = $master->id;
        $this->attributes = array_merge($master->attributes, $this->attributes);
    }

    public function compare(Playlist $playlist): int
    {
        foreach (["BANDWIDTH", "AVERAGE-BANDWIDTH"] as $attribute) {
            if (null !== $values = self::getInCommon($this, $playlist, $attribute)) {
                return (int)$values[0] <=> (int)$values[1];
            }
        }

        if (null !== $values = self::getInCommon($this, $playlist, "RESOLUTION")) {
            [$current, $comp] = $values;
            [$currentX, $currentY] = explode('x', $current);
            [$compX, $compY] = explode('x', $comp);

            return ((int)$currentX * (int)$currentY) <=> ((int)$compX * (int)$compY);
        }

        return -1;
    }

    public function __clone(): void
    {
        $this->master = copy($this->master);
        $this->encryption = copy($this->encryption);
    }

    public function jsonSerialize(): array
    {
        return [
            Playlist::class,
            $this->id,
            $this->url,
            $this->source,
            $this->attributes,
            $this->master,
            $this->encryption
        ];
    }

    private static function getInCommon(Playlist $playlist, Playlist $playlistComp, string $attribute): ?array
    {
        if (isset($playlist->attributes[$attribute]) && isset($playlistComp->attributes[$attribute])) {
            return [$playlist->attributes[$attribute], $playlistComp->attributes[$attribute]];
        }

        return null;
    }

}
