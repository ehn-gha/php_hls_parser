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

namespace Ehngha\Lib\Hls;

use function array_combine;
use function array_filter;
use function array_merge;
use function preg_match_all;

/**
 * Parse and generate attributes list assigned to an hls tag
 * @param string $attributes
 * @return array<string, string>
 */
function generate_hls_tag_attributes(string $attributes): array
{
    $matches = [];
    preg_match_all('#([A-Z-]+)="([^"]+)",?|([A-Z]+)=([^,]+),?#', $attributes, $matches);

    return array_filter(array_merge(
        array_combine($matches[1] ?? [], $matches[2] ?? []),
        array_combine($matches[3] ?? [], $matches[4] ?? [])
    ));
}
