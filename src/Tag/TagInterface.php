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

interface TagInterface
{

    /**
     * Handle the tag value
     * @param string $tag
     * @param string $tagValue
     * @param PlaylistCollection|FragmentCollection $collection
     */
    public function handle(string $tag, string $tagValue, PlaylistCollection|FragmentCollection $collection): void;

    /**
     * Execute the tag on the generated entity
     * @param Playlist|Fragment $entity
     */
    public function execute(Playlist|Fragment $entity): void;

    /**
     * Reset the tag status.
     * Will be called after each entity parsing
     */
    public function reset(): void;

    /**
     * Declare the tag name
     * @return string|array<int, string>
     */
    public function getName(): string|array;

}
