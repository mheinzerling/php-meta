<?php
declare(strict_types = 1);
namespace mheinzerling\meta\language;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * @method static Primitive PRIVATE ()
 * @method static Primitive PROTECTED ()
 * @method static Primitive PUBLIC ()
 */
final class Visibility extends AbstractEnumeration
{
    const PRIVATE = 'private';
    const PROTECTED = 'protected';
    const PUBLIC = 'public';
}
