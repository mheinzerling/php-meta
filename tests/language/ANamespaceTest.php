<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

class ANamespaceTest extends \PHPUnit_Framework_TestCase
{
    public function testAbsolute()
    {
        $namespace = ANamespace::absolute("\\mheinzerling\\meta\\language");
        static::assertFalse($namespace->isRoot());
        static::assertEquals("mheinzerling\\meta\\language", $namespace->qualified());
        static::assertEquals("\\mheinzerling\\meta\\language", $namespace->fullyQualified());

        $namespace = ANamespace::absolute("\\mheinzerling\\meta\\language\\");
        static::assertFalse($namespace->isRoot());
        static::assertEquals("mheinzerling\\meta\\language", $namespace->qualified());
        static::assertEquals("\\mheinzerling\\meta\\language", $namespace->fullyQualified());

        $namespace = ANamespace::absolute("\\");
        static::assertTrue($namespace->isRoot());
        static::assertEquals("", $namespace->qualified());
        static::assertEquals("\\", $namespace->fullyQualified());

        try {
            ANamespace::absolute("mheinzerling\\meta\\language");
            static::fail("Exception expected");
        } catch (\Exception $e) {
            static::assertEquals("Fully qualified namespace required, starting with \. Got: >mheinzerling\meta\language<", $e->getMessage());
        }

        try {
            ANamespace::absolute("");
            static::fail("Exception expected");
        } catch (\Exception $e) {
            static::assertEquals("Fully qualified namespace required, starting with \. Got: ><", $e->getMessage());
        }
    }

    public function testResolve()
    {
        $class = ANamespace::absolute("\\mheinzerling\\meta\\language")->resolve("AClass");
        static::assertEquals(AClass::absolute(AClass::class), $class);

        $class = ANamespace::root()->resolve("DateTime");
        static::assertEquals(AClass::absolute(\DateTime::class), $class);
    }
}
