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
use function Ehngha\Lib\Hls\generate_hls_tag_attributes;

final class MediaTag implements TagInterface
{

    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void
    {
        $attributes = generate_hls_tag_attributes($tagValue);
        if (!isset($attributes["URI"])) {
            return;
        }
        $rendition = new Playlist(master: $collection->getMaster());
        $rendition->setUrl($attributes["URI"]);
        unset($attributes["URI"]);
        $rendition->attributes = $attributes;
        $collection->addRendition($rendition);
    }

    public function execute(Fragment|Playlist $entity): void
    {
    }

    public function reset(): void
    {
    }

    public function getName(): string|array
    {
        return "EXT-X-MEDIA";
    }

}
