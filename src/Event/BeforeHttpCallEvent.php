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

namespace Ehngha\Lib\Hls\Event;

use Ehngha\Lib\Hls\Entity\Encryption;
use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;

/**
 * Event triggered before every http calls to an entity
 */
final class BeforeHttpCallEvent
{

    /**
     * BeforeHttpCallEvent constructor.
     * @param Master|Playlist|Encryption|Fragment $entity
     * @param string $httpMethod
     * @param array $httpOptions
     */
    public function __construct(
        public Master|Playlist|Encryption|Fragment $entity,
        public string $httpMethod = "GET",
        public array $httpOptions = []
    )
    {}

}
