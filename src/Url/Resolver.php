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
use function array_pop;
use function explode;
use function implode;
use function parse_url;
use function str_starts_with;
use function strlen;
use function strpos;
use function substr;
use function trim;
use const PHP_URL_PATH;
use const PHP_URL_SCHEME;

final class Resolver
{

    private const QUERY = '?';
    private const ANCHOR = '#';

    /** @codeCoverageIgnore  */
    private function __construct()
    {}

    /**
     * Try to resolve a uri from its base
     * @param string $uri
     * @param string|null $baseUri
     * @return string
     * @throws ResolveException
     */
    public static function resolve(string $uri, ?string $baseUri = null): string
    {
        $uri = trim($uri);

        if (null !== parse_url($uri, PHP_URL_SCHEME)) {
            return $uri;
        }

        if (null === $baseUri) {
            throw new ResolveException("A base url is required to resolve the given url");
        }

        if (!isset($uri[0])) {
            return $baseUri;
        }

        if ('#' === $uri[0]) {
            return self::clean($baseUri, self::ANCHOR) . $uri;
        }

        $baseUriCleaned = self::clean(self::clean($baseUri, self::ANCHOR), self::QUERY);

        if ('?' === $uri[0]) {
            return "{$baseUriCleaned}{$uri}";
        }

        if (str_starts_with($uri, "//")) {
            return preg_replace("#^([^/]*)//.*$#", "$1", $baseUriCleaned) . $uri;
        }

        $baseUriCleaned = preg_replace("#^(.*?//[^/]*)(?:/.*)?$#", "$1", $baseUriCleaned);

        if ('/' === $uri[0]) {
            return "{$baseUriCleaned}{$uri}";
        }

        $path = parse_url(substr($baseUri, strlen($baseUriCleaned)), PHP_URL_PATH);
        $path = self::resolvePath(substr($path, 0, strrpos($path, '/')) . "/{$uri}");

        return $baseUriCleaned . ("" === $path || '/' !== $path[0] ? '/' : "") . $path;
    }

    private static function resolvePath(string $path): string
    {
        if ("" === $path || '/' === $path) {
            return $path;
        }

        if ('.' === substr($path, -1)) {
            $path .= '/';
        }

        $output = [];

        foreach (explode('/', $path) as $segment) {
            if (".." === $segment) {
                array_pop($output);
            } elseif ('.' !== $segment) {
                $output[] = $segment;
            }
        }

        return implode('/', $output);
    }

    private static function clean(string $uri, string $char): string
    {
        return (false !== $pos = strpos($uri, $char)) ? substr($uri, 0, $pos) : $uri;
    }

}
