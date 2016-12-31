<?php
declare(strict_types = 1);

namespace test;

use mheinzerling\commons\StringUtils;
use mheinzerling\meta\language\AClass;
use mheinzerling\meta\language\ANamespace;

/**
 * Some documentation.
 *
 * Some more documentation.
 * @SuppressWarnings(PHPMD)
 */
final class TestClass extends AbstractTestClass
{
    const foo = 1;
    const bar = 'sdffs';
    const goo = true;
    const hoo = false;
    /**
     * @var string
     */
    private $aString = 'FOOBAR';

    /**
     * @var int|null
     */
    protected $anInt;

    /**
     * @var AClass
     */
    public static $aClass;

    public function __construct()
    {
        echo 'hallo';
    }

    private function nothing(): void
    {
    }

    private static function max(int $a, int $b): int
    {
        return max($a, $b);
    }

    protected function doSomething(AClass $class, \PDO $pdo): ANamespace
    {
        StringUtils::toLower(null);
        return $class->getNamespace();
    }
}