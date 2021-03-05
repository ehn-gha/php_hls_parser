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

interface AttributeEnum
{

    /**
     * HLS version
     * @see https://tools.ietf.org/html/draft-pantos-http-live-streaming-23#section-4.3.1.2
     */
    public const VERSION = "VERSION";

    /**
     * Live entity
     */
    public const LIVE = "IS_LIVE";

    /**
     * Fragment duration in seconds
     */
    public const DURATION = "DURATION";

    /**
     * Fragment title
     */
    public const TITLE = "TITLE";

    /**
     * Fragment datetime
     * @see https://tools.ietf.org/html/draft-pantos-http-live-streaming-23#section-4.3.2.6
     */
    public const DATETIME = "DATETIME";

    /**
     * Fragment sequence
     */
    public const SEQUENCE = "SEQUENCE";

    /**
     * Fragment discontinuity sequence
     */
    public const DISCONTINUITY_SEQUENCE = "DISCONTINUITY_SEQUENCE";

    /**
     * Playlist type
     * @see https://tools.ietf.org/html/draft-pantos-http-live-streaming-23#section-4.3.3.5
     */
    public const TYPE = "PLAYLIST_TYPE";

}
