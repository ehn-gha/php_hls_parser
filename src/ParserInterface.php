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

namespace Ehngha\Lib\Hls;

use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Master;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use Ehngha\Lib\Hls\Exception\ParserException;

interface ParserInterface
{

    /**
     * Parse the given HLS master and generate a playlist collection.
     * Adequate events MUST be dispatched
     * @param Master $master
     * @return PlaylistCollection
     * @throws ParserException
     */
    public function parseMaster(Master $master): PlaylistCollection;

    /**
     * Parse the given playlist and generate all fragments associated to it
     * Adequate events MUST be dispatched
     * @param Playlist $playlist
     * @return FragmentCollection
     * @throws ParserException
     */
    public function parsePlaylist(Playlist $playlist): FragmentCollection;

}
