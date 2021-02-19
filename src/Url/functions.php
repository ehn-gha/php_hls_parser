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

namespace Ehngha\Lib\Hls\Url;

use Ehngha\Lib\Hls\Exception\ResolveException;

/**
 * Resolve a uri
 * @param string $url
 * @param string|null $base
 * @return string
 * @throws ResolveException
 */
function resolve_url(string $url, ?string $base = null): string
{
    return Resolver::resolve($url, $base);
}
