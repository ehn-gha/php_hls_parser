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

final class StreamInfTag implements TagInterface
{

    private ?array $attributes = null;

    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void
    {
        $this->attributes = generate_hls_tag_attributes($tagValue);
    }

    public function execute(Fragment|Playlist $entity): void
    {
        foreach ($this->attributes ?? [] as $attribute => $value) {
            $entity->attributes[$attribute] = $value;
        }

        $this->attributes = null;
    }

    public function reset(): void
    {
        $this->attributes = null;
    }

    public function getName(): string|array
    {
        return "EXT-X-STREAM-INF";
    }

}
