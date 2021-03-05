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

use Ehngha\Lib\Hls\Entity\AttributeEnum;
use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;

final class TypeTag implements TagInterface
{

    private ?string $type = null;

    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void
    {
        $this->type = $tagValue;
    }

    public function execute(Fragment|Playlist $entity): void
    {
        if (null !== $this->type && $entity instanceof Playlist) {
            $entity->attributes[AttributeEnum::TYPE] = $this->type;
        }
    }

    public function reset(): void
    {
        $this->type = null;
    }

    public function getName(): string|array
    {
        return "EXT-X-PLAYLIST-TYPE";
    }

}
