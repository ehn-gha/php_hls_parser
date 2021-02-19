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

namespace Ehngha\Lib\Hls\Tag;

use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;

final class VersionTag implements TagInterface
{

    private ?int $version = null;

    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void
    {
        $this->version = (int)$tagValue;
    }

    public function execute(Fragment|Playlist $entity): void
    {
        if ($entity instanceof Playlist && null !== $this->version) {
            $entity->attributes["VERSION"] = $this->version;
        }
    }

    public function reset(): void
    {
        $this->version = null;
    }

    public function getName(): string|array
    {
        return "EXT-X-VERSION";
    }

}
