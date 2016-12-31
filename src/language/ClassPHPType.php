<?php
declare(strict_types = 1);
namespace mheinzerling\meta\language;


class ClassPHPType extends PHPType
{
    /**
     * @var AClass
     */
    private $class;

    public function __construct(AClass $class)
    {
        $this->class = $class;
    }

    /** @noinspection PhpDocSignatureInspection */
    /**
     *
     * @return ClassPHPType
     */
    public function toOptional(): PHPType
    {
        $type = new ClassPHPType($this->class);
        $type->setOptional(true);
        return $type;
    }

    public function getClass(): AClass
    {
        return $this->class;
    }
}