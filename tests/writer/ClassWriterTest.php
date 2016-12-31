<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

use mheinzerling\commons\StringUtils;
use mheinzerling\meta\writer\ClassWriter;

class ClassWriterTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $namespace = ANamespace::absolute("\\test");
        $classWriter = (new ClassWriter("TestClass"))
            ->namespace($namespace)
            ->final()
            ->extends($namespace->resolve("AbstractTestClass"));
        self::assertEquals($namespace, $classWriter->getNamespace());
        $classWriter->doc("Some documentation.")
            ->doc("")
            ->doc("Some more documentation.")
            ->doc("@SuppressWarnings(PHPMD)")
            ->const("foo", 1)
            ->const("bar", "sdffs")
            ->const("goo", true)
            ->const("hoo", false)
            ->field("aString")->private()->primitive(Primitive::STRING())->initial("FOOBAR")
            ->field("anInt")->protected()->type((new PrimitivePHPType(Primitive::INT()))->toOptional())
            ->field("aClass")->public()->static()->class(AClass::absolute(AClass::class))
            //
            ->method("__construct")->public()
            ->line("echo 'hallo';")
            //
            ->method("nothing")
            ->private()
            ->void()
            //
            ->method("max")
            ->private()
            ->static()
            ->returnPrimitive(Primitive::INT())
            ->paramPrimitive("a", Primitive::INT())
            ->paramPrimitive("b", Primitive::INT(), 0)
            ->line('return max($a, $b);')
            //
            ->method("doSomething")
            ->protected()
            ->returnClass(AClass::absolute(ANamespace::class))
            ->paramClass("class", AClass::absolute(AClass::class))
            ->paramClass("pdo", AClass::absolute(\PDO::class))
            ->line($classWriter->print(AClass::absolute(StringUtils::class)) . "::toLower(null);")
            ->line('return $class->getNamespace();');


        static::assertEquals(file_get_contents(realpath(__DIR__ . "/../..") . "/resources/class.php"), $classWriter->write());
    }

    public function testAbstractNoNs()
    {
        $classWriter = (new ClassWriter("TestClass"))
            ->abstract();

        static::assertEquals(str_replace("\r", "", "<?php
declare(strict_types = 1);

abstract class TestClass
{
}"), $classWriter->write());
    }
}
