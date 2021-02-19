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

use Ehngha\Lib\Hls\Entity\Encryption;
use Ehngha\Lib\Hls\Entity\Fragment;
use Ehngha\Lib\Hls\Entity\FragmentCollection;
use Ehngha\Lib\Hls\Entity\Playlist;
use Ehngha\Lib\Hls\Entity\PlaylistCollection;
use function Ehngha\Lib\Hls\generate_hls_tag_attributes;

final class KeyTag implements TagInterface
{

    private ?Encryption $encryption = null;

    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void
    {
        $this->encryption = null;
        $attribute = generate_hls_tag_attributes($tagValue);
        if (!isset($attribute["URI"])) {
            return;
        }
        $this->encryption = new Encryption($attribute["URI"], $attribute["METHOD"] ?? "AES-128", $attribute["IV"] ?? "");
    }

    public function execute(Fragment|Playlist $entity): void
    {
        if (null !== $this->encryption) {
            $entity->setEncryption($this->encryption);
        }
    }

    public function reset(): void
    {
        $this->encryption = null;
    }

    public function getName(): string|array
    {
        return ["EXT-X-KEY", "EXT-X-SESSION-KEY"];
    }

}
