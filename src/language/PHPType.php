<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

abstract class PHPType
{
    /**
     * @var bool
     */
    protected $optional = false;

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function setOptional(bool $optional): void
    {
        $this->optional = $optional;
    }


    public abstract function toOptional(): PHPType;

}