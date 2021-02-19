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

function serialize(Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection $entity): string
{
    return Serializer::serialize($entity);
}

function unserialize(string|array $entity): Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection|null
{
    return Serializer::unserialize($entity);
}
