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

final class SequenceTag implements TagInterface
{

    private int $sequence = 0;
    private int $discontinuitySequence = 0;
    private bool $discontinuity = false;

    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void
    {
        switch ($tag) {
            case "EXT-X-MEDIA-SEQUENCE":
                $this->sequence = (int)$tagValue;
                break;
            case "EXT-X-DISCONTINUITY-SEQUENCE":
                $this->discontinuitySequence = (int)$tagValue;
                break;
            case "EXT-X-DISCONTINUITY":
                $this->discontinuity = true;
        }
    }

    public function execute(Fragment|Playlist $entity): void
    {
        if (!$entity instanceof Fragment) {
            return;
        }
        if (!isset($entity->attributes[AttributeEnum::DISCONTINUITY_SEQUENCE]) && !isset($entity->attributes[AttributeEnum::SEQUENCE])) {
            if ($this->discontinuity) {
                $entity->attributes[AttributeEnum::DISCONTINUITY_SEQUENCE] = ++$this->discontinuitySequence;
                $this->discontinuity = false;
            } else {
                $entity->attributes[AttributeEnum::SEQUENCE] = ++$this->sequence;
            }
        }
    }

    public function reset(): void
    {
        $this->discontinuitySequence = 0;
        $this->discontinuity = false;
        $this->sequence = 0;
    }

    public function getName(): string|array
    {
        return ["EXT-X-MEDIA-SEQUENCE", "EXT-X-DISCONTINUITY-SEQUENCE", "EXT-X-DISCONTINUITY"];
    }

}
