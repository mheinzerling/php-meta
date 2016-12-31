<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

class ClassPHPTypeTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $class = AClass::absolute(AClass::class);

        $type = new ClassPHPType($class);
        static::assertEquals($class, $type->getClass());
        static::assertFalse($type->isOptional());

        $oType = $type->toOptional();
        static::assertEquals($class, $oType->getClass());
        static::assertTrue($oType->isOptional());
        static::assertFalse($type->isOptional());
        static::assertNotSame($type, $oType);
    }

}
