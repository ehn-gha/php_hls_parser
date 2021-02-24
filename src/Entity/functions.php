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

use Ehngha\Lib\Hls\Exception\InvalidEntityException;
use JsonException;

/**
 * Serialize the given entity
 * @param Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection $entity
 * @return string
 * @throws JsonException
 */
function serialize(Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection $entity): string
{
    return Serializer::serialize($entity);
}

/**
 * Restore the given entity from its serialized representation
 * @param string|array $entity
 * @return Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection|null
 * @throws InvalidEntityException
 */
function unserialize(string|array $entity): Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection|null
{
    return Serializer::unserialize($entity);
}

/**
 * Check if the given entity is considered live
 * @param Master|Playlist|Fragment $entity
 * @return bool
 */
function is_live(Master|Playlist|Fragment $entity): bool
{
    return ($entity->attributes[AttributeEnum::LIVE] ?? null) === true;
}
