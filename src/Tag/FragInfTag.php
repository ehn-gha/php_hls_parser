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

use DateTime;
use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use Exception;
use function explode;

final class FragInfTag implements TagInterface
{

    private ?array $inf = null;
    private ?int $datetime = null;

    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void
    {
        switch ($tag) {
            case "EXTINF":
                $this->inf = explode(',', $tagValue);
                break;
            case "EXT-X-PROGRAM-DATE-TIME":
                try {
                    $this->datetime = (new DateTime($tagValue))->getTimestamp();
                } catch (Exception) {
                    break;
                }
                break;
        }
    }

    public function execute(Fragment|Playlist $entity): void
    {
        if (null !== $this->inf) {
            $entity->attributes["DURATION"] = (float)$this->inf[0];
            if (isset($this->inf[1][0])) {
                $entity->attributes["TITLE"] = $this->inf[1];
            }
        }

        if (null !== $this->datetime) {
            $entity->attributes["DATETIME"] = $this->datetime;
        }
    }

    public function reset(): void
    {
        $this->inf = null;
        $this->datetime = null;
    }

    public function getName(): string|array
    {
        return ["EXTINF", "EXT-X-PROGRAM-DATE-TIME"];
    }

}
