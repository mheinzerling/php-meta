<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

class PrimitivePHPTypeTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $type = new PrimitivePHPType(Primitive::INT());
        static::assertTrue($type->isInt());
        static::assertFalse($type->isOptional());
        static::assertEquals("int", $type->getToken());

        $oType = $type->toOptional();
        static::assertTrue($type->isInt());
        static::assertTrue($oType->isOptional());
        static::assertFalse($type->isOptional());
        static::assertNotSame($type, $oType);
    }

    public function testIs()
    {
        static::assertTrue((new PrimitivePHPType(Primitive::INT()))->isInt());
        static::assertTrue((new PrimitivePHPType(Primitive::BOOL()))->isBool());
        static::assertTrue((new PrimitivePHPType(Primitive::STRING()))->isString());
        static::assertTrue((new PrimitivePHPType(Primitive::ARRAY()))->isArray());
    }
}
