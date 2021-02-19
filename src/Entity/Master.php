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

final class Master implements JsonSerializable
{

    /**
     * Master constructor.
     * @param string $id
     *   Channel id or name
     * @param string $url
     *   Master url source
     * @param string|null $source
     *   Cached source
     * @param array $attributes
     *   Master attributes.
     *   This attributes will be shared among the playlists associate to this master
     */
    public function __construct(
        public string $id = "",
        public string $url = "",
        public ?string $source = null,
        public array $attributes = []
    )
    {}

    public function __clone(): void {}

    public function jsonSerialize(): array
    {
        return [
            Master::class,
            $this->id,
            $this->url,
            $this->source,
            $this->attributes
        ];
    }

}
