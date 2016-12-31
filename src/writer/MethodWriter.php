<?php
declare(strict_types = 1);

namespace mheinzerling\meta\writer;

use mheinzerling\meta\language\AClass;
use mheinzerling\meta\language\ClassPHPType;
use mheinzerling\meta\language\PHPType;
use mheinzerling\meta\language\Primitive;
use mheinzerling\meta\language\PrimitivePHPType;
use mheinzerling\meta\language\Visibility;

class MethodWriter
{
    const NODEFAULT = "__NODEFAULT__";
    /**
     * @var string
     */
    private $name;
    /**
     * @var ClassWriter
     */
    private $classWriter;
    /**
     * @var Visibility
     */
    private $visibility;
    /**
     * @var PHPType
     */
    private $return = null;
    /**
     * @var string
     */
    private $body = "";
    /**
     * @var array[]
     */
    private $params = [];
    /**
     * @var bool
     */
    private $static = false;

    public function __construct(ClassWriter $classWriter, string $name)
    {
        $this->name = $name;
        $this->classWriter = $classWriter;
        $this->visibility = Visibility::PRIVATE ();
    }

    public function create(): string
    {
        $result = '    ' . $this->visibility->value();
        if ($this->static) $result .= ' static';
        $result .= ' function ' . $this->name . '(';
        foreach ($this->params as $name => $p) {
            /**
             * @var $type PHPType
             * @var $default mixed
             */
            $type = $p['type'];
            $default = $p['default'];
            $result .= TypeConverter::toPHP($type, $this->classWriter) . ' $' . $name;
            if ($default != self::NODEFAULT) $result .= ' = ' . TypeConverter::toValue($type, $default);
            $result .= ', ';
        }
        if (count($this->params) > 0) $result = substr($result, 0, -2);
        $result .= ')';
        if ($this->return != null) $result .= ': ' . TypeConverter::toPHP($this->return, $this->classWriter);
        $result .= "\n";
        $result .= "    {\n";
        $result .= $this->body;
        $result .= "    }\n";
        return $result;
    }

    public function public (): MethodWriter
    {
        $this->visibility = Visibility::PUBLIC ();
        return $this;
    }

    public function protected (): MethodWriter
    {
        $this->visibility = Visibility::PROTECTED ();
        return $this;
    }


    public function private (): MethodWriter
    {
        $this->visibility = Visibility::PRIVATE ();
        return $this;
    }

    public function return (PHPType $type): MethodWriter
    {
        $this->return = $type;
        return $this;
    }

    public function returnPrimitive(Primitive $primitive): MethodWriter
    {
        return $this->return(new PrimitivePHPType($primitive));
    }

    public function void(): MethodWriter
    {
        return $this->returnPrimitive(Primitive::VOID());
    }

    public function returnClass(AClass $class): MethodWriter
    {
        return $this->return(new ClassPHPType($class));
    }


    public function param($name, PHPType $type, $default = self::NODEFAULT): MethodWriter
    {
        $this->params[$name] = ['type' => $type, 'default' => $default];
        return $this;
    }

    public function paramPrimitive($name, Primitive $primitive, $default = self::NODEFAULT): MethodWriter
    {
        return $this->param($name, new PrimitivePHPType($primitive), $default);
    }

    public function paramClass($name, AClass $class, $default = self::NODEFAULT): MethodWriter
    {
        return $this->param($name, new ClassPHPType($class), $default);
    }

    public function write(): string
    {
        return $this->classWriter->write();
    }

    public function line(string $line): MethodWriter
    {
        $this->body .= '        ' . $line . "\n";
        return $this;
    }

    public function getClassWriter(): ClassWriter
    {
        return $this->classWriter;
    }

    public function method($name): MethodWriter
    {
        return $this->classWriter->method($name);
    }

    public function static (bool $static = true): MethodWriter
    {
        $this->static = $static;
        return $this;
    }
}