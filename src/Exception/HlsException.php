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

namespace Ehngha\Lib\Hls\Exception;

use Exception;

class HlsException extends Exception {}
final class LogicException extends HlsException {}
final class ParserException extends HlsException {}
final class ResolveException extends HlsException {}
final class InvalidEntityException extends HlsException {}
