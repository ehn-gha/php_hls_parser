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

final class Encryption implements JsonSerializable
{

    /**
     * Encryption constructor.
     * @param string $url
     *   Encryption key url
     * @param string $method
     *   Encryption method
     * @param string $iv
     *   IV
     * @param string|null $key
     *   Cached key value.
     *   This value is kept during the serialization
     */
    public function __construct(
        public string $url = "",
        public string $method = "",
        public string $iv = "",
        public ?string $key = null
    )
    {}

    public function __clone(): void {}

    public function jsonSerialize(): array
    {
        return [
            Encryption::class,
            $this->url,
            $this->method,
            $this->iv,
            $this->key
        ];
    }

}
