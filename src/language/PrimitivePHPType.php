<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

class PrimitivePHPType extends PHPType
{
    /**
     * @var Primitive
     */
    private $type;

    function __construct(Primitive $type)
    {
        $this->type = $type;
    }

    public function toOptional(): PHPType
    {
        $type = new PrimitivePHPType($this->type);
        $type->setOptional(true);
        return $type;
    }

    public function isBool(): bool
    {
        return $this->type == Primitive::BOOL();
    }

    public function isInt(): bool
    {
        return $this->type == Primitive::INT();
    }

    public function isString(): bool
    {
        return $this->type == Primitive::STRING();
    }

    public function getToken(): string
    {
        return $this->type->value();
    }

}