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

}
