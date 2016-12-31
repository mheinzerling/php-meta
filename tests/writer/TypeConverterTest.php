<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

use mheinzerling\meta\writer\ClassWriter;
use mheinzerling\meta\writer\TypeConverter;

class TypeConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPType
     */
    private $unknown;
    /**
     * @var ClassWriter
     */
    private $writer;

    protected function setUp()
    {
        $this->unknown = new class extends PHPType
        {
            public function toOptional(): PHPType
            {
                return new PrimitivePHPType(Primitive::BOOL());
            }
        };

        $this->writer = new class extends ClassWriter
        {
            public function __construct()
            {
                parent::__construct("Dummy");
            }

            public function print(AClass $class): string
            {
                return $class->simple();
            }
        };

    }

    public function testGetterPrefix()
    {
        static::assertEquals("is", TypeConverter::getterPrefix(new PrimitivePHPType(Primitive::BOOL())));
        static::assertEquals("get", TypeConverter::getterPrefix(new PrimitivePHPType(Primitive::INT())));
        static::assertEquals("get", TypeConverter::getterPrefix(new ClassPHPType(AClass::absolute(AClass::class))));

        try {
            TypeConverter::getterPrefix($this->unknown);
            static::fail("Exception expected");
        } catch (\Exception $e) {
            static::assertEquals('Unhandled type class@anonymous', substr($e->getMessage(), 0, 30));
        }
    }

    public function testToPHPDoc()
    {
        static::assertEquals("int", TypeConverter::toPHPDoc(new PrimitivePHPType(Primitive::INT()), $this->writer));
        static::assertEquals("int|null", TypeConverter::toPHPDoc((new PrimitivePHPType(Primitive::INT()))->toOptional(), $this->writer));
        static::assertEquals("AClass", TypeConverter::toPHPDoc(new ClassPHPType(AClass::absolute(AClass::class)), $this->writer));
        static::assertEquals("AClass|null", TypeConverter::toPHPDoc((new ClassPHPType(AClass::absolute(AClass::class)))->toOptional(), $this->writer));

        try {
            TypeConverter::toPHPDoc($this->unknown, $this->writer);
            static::fail("Exception expected");
        } catch (\Exception $e) {
            static::assertEquals('Unhandled type class@anonymous', substr($e->getMessage(), 0, 30));
        }
    }

    public function testToPHP()
    {
        static::assertEquals("int", TypeConverter::toPHP(new PrimitivePHPType(Primitive::INT()), $this->writer));
        static::assertEquals("?int", TypeConverter::toPHP((new PrimitivePHPType(Primitive::INT()))->toOptional(), $this->writer));
        static::assertEquals("AClass", TypeConverter::toPHP(new ClassPHPType(AClass::absolute(AClass::class)), $this->writer));
        static::assertEquals("?AClass", TypeConverter::toPHP((new ClassPHPType(AClass::absolute(AClass::class)))->toOptional(), $this->writer));

        try {
            TypeConverter::toPHP($this->unknown, $this->writer);
            static::fail("Exception expected");
        } catch (\Exception $e) {
            static::assertEquals('Unhandled type class@anonymous', substr($e->getMessage(), 0, 30));
        }
    }

    public function testValue()
    {
        static::assertEquals("null", TypeConverter::toValue(new PrimitivePHPType(Primitive::INT()), null));
        static::assertEquals("1", TypeConverter::toValue(new PrimitivePHPType(Primitive::INT()), 1));
        static::assertEquals("true", TypeConverter::toValue(new PrimitivePHPType(Primitive::BOOL()), true));
        static::assertEquals("false", TypeConverter::toValue(new PrimitivePHPType(Primitive::BOOL()), false));
        static::assertEquals("true", TypeConverter::toValue(new PrimitivePHPType(Primitive::BOOL()), 1));
        static::assertEquals("false", TypeConverter::toValue(new PrimitivePHPType(Primitive::BOOL()), 0));
        static::assertEquals("'abc'", TypeConverter::toValue(new PrimitivePHPType(Primitive::STRING()), "abc"));
        static::assertEquals("null", TypeConverter::toValue(new ClassPHPType(AClass::absolute(AClass::class)), null));

        try {
            static::assertEquals("?AClass", TypeConverter::toValue((new ClassPHPType(AClass::absolute(AClass::class))), AClass::absolute(AClass::class)));
            static::fail("Exception expected");
        } catch (\Exception $e) {

            static::assertEquals('Unsupported for objects \mheinzerling\meta\language\AClass', $e->getMessage());
        }

        try {
            TypeConverter::toValue($this->unknown, null);
            static::fail("Exception expected");
        } catch (\Exception $e) {
            static::assertEquals('Unhandled type class@anonymous', substr($e->getMessage(), 0, 30));
        }
    }
}
