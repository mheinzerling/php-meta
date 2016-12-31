<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

class AClassTest extends \PHPUnit_Framework_TestCase
{
    public function testAbsolute()
    {
        $class = AClass::absolute(AClass::class);
        static::assertEquals("AClass", $class->simple());
        static::assertEquals("mheinzerling\\meta\\language\\AClass", $class->import());
        static::assertEquals("\\mheinzerling\\meta\\language\\AClass", $class->fullyQualified());

        $class = AClass::absolute(\DateTime::class);
        static::assertEquals("DateTime", $class->simple());
        static::assertEquals("DateTime", $class->import());
        static::assertEquals("\\DateTime", $class->fullyQualified());
    }

    public function testResolve()
    {
        $class = AClass::resolve(ANamespace::absolute("\\mheinzerling\\meta\\language"), "AClass");
        static::assertEquals(AClass::absolute(AClass::class), $class);

        $class = AClass::resolve(ANamespace::absolute("\\"), "DateTime");
        static::assertEquals(AClass::absolute(\DateTime::class), $class);
    }
}
