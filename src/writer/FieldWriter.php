<?php
declare(strict_types = 1);

namespace mheinzerling\meta\writer;

use mheinzerling\meta\language\AClass;
use mheinzerling\meta\language\ClassPHPType;
use mheinzerling\meta\language\PHPType;
use mheinzerling\meta\language\Primitive;
use mheinzerling\meta\language\PrimitivePHPType;
use mheinzerling\meta\language\Visibility;

class FieldWriter
{
    /**
     * @var ClassWriter
     */
    private $classWriter;
    /**
     * @var string
     */
    private $name;
    /**
     * @var Visibility
     */
    private $visibility;
    /**
     * @var PHPType
     */
    private $type;
    /**
     * @var bool
     */
    private $static = false;
    /**
     * @var mixed
     */
    private $initial = null;

    public function __construct(ClassWriter $classWriter, string $name)
    {
        $this->classWriter = $classWriter;
        $this->name = $name;
        $this->visibility = Visibility::PRIVATE ();
        $this->type = new PrimitivePHPType(Primitive::MIXED());
    }

    public function create(): string
    {
        $result = "    /**\n";
        $result .= "     * @var " . TypeConverter::toPHPDoc($this->type, $this->classWriter) . "\n";
        $result .= "     */\n";
        $result .= "    " . $this->visibility->value();
        if ($this->static) $result .= " static";
        $result .= " $" . $this->name;
        if ($this->initial != null) $result .= " = " . TypeConverter::toValue($this->type, $this->initial);
        $result .= ";\n";
        return $result;
    }

    public function public (): FieldWriter
    {
        $this->visibility = Visibility::PUBLIC ();
        return $this;
    }

    public function protected (): FieldWriter
    {
        $this->visibility = Visibility::PROTECTED ();
        return $this;
    }

    public function private (): FieldWriter
    {
        $this->visibility = Visibility::PRIVATE ();
        return $this;
    }

    public function static (bool $static = true): FieldWriter
    {
        $this->static = $static;
        return $this;
    }

    public function type(PHPType $type): FieldWriter
    {
        $this->type = $type;
        return $this;
    }

    public function primitive(Primitive $primitive): FieldWriter
    {
        return $this->type(new PrimitivePHPType($primitive));
    }

    public function class(AClass $class): FieldWriter
    {
        return $this->type(new ClassPHPType($class));
    }

    public function initial($initial): void
    {
        $this->initial = $initial;
    }
}