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
use function is_string;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final class Serializer
{
    /** @codeCoverageIgnore  */
    private function __construct()
    {}

    public static function serialize(Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection $entity): string
    {
        return json_encode($entity, JSON_THROW_ON_ERROR);
    }

    public static function unserialize(string|array|null $entity):
        Master|Playlist|Encryption|PlaylistCollection|Fragment|FragmentCollection|null
    {
        if (null === $entity) {
            return null;
        }

        $entity = (is_string($entity)) ? json_decode($entity, true, flags: JSON_THROW_ON_ERROR) : $entity;

        switch ($entity[0]) {
            case Master::class:
                return self::makeMaster($entity);
            case Playlist::class:
                return self::makePlaylist($entity);
            case Encryption::class:
                return self::makeEncryption($entity);
            case PlaylistCollection::class:
                return PlaylistCollection::makeFromJson($entity);
            case Fragment::class:
                return self::makeFragment($entity);
            case FragmentCollection::class:
                return self::makeFragmentCollection($entity);
        }

        throw new InvalidEntityException("This entity '{$entity[0]}' is not valid");
    }

    private static function makeMaster(array $json): Master
    {
        return new Master($json[1], $json[2], $json[3], $json[4]);
    }

    private static function makePlaylist(array $json): Playlist
    {
        $playlist = new Playlist($json[1], $json[3], $json[4]);
        $playlist->url = $json[2];
        $playlist->master = self::unserialize($json[5] ?? null);
        $playlist->encryption = self::unserialize($json[6] ?? null);

        return $playlist;
    }

    private static function makeEncryption(array $json): Encryption
    {
        return new Encryption($json[1], $json[2], $json[3], $json[4]);
    }

    private static function makeFragment(array $json): Fragment
    {
        $fragment = new Fragment(null);
        $fragment->playlist = self::unserialize($json[1] ?? null);
        $fragment->url = $json[2];
        $fragment->attributes = $json[3];
        $fragment->encryption = self::unserialize($json[4] ?? null);

        return $fragment;
    }

    private static function makeFragmentCollection(array $json): FragmentCollection
    {
        $collection = new FragmentCollection(self::unserialize($json[1]));
        foreach ($json[2] as $fragment) {
            $collection[] = self::unserialize($fragment);
        }

        return $collection;
    }

}
