<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

use mheinzerling\commons\FileUtils;
use mheinzerling\commons\StringUtils;

class AClass
{
    /**
     * @var ANamespace
     */
    private $namespace;

    /**
     * @var string
     */
    private $simpleName;

    public static function absolute(string $fullyQualifiedName): AClass
    {
        $className = new AClass();
        $className->simpleName = FileUtils::basename($fullyQualifiedName);
        $absoluteWithoutRoot = !StringUtils::startsWith($fullyQualifiedName, ANamespace::DELIMITER);
        if ($absoluteWithoutRoot) {
            $fullyQualifiedName = ANamespace::DELIMITER . $fullyQualifiedName;
        }
        $className->namespace = ANamespace::absolute(substr($fullyQualifiedName, 0, -strlen($className->simpleName)));
        return $className;
    }

    public static function resolve(ANamespace $namespace, string $simpleClassName): AClass
    {
        $className = new AClass();
        $className->simpleName = $simpleClassName;
        $className->namespace = $namespace;
        return $className;
    }

    public function getNamespace(): ANamespace
    {
        return $this->namespace;
    }

    public function simple(): string
    {
        return $this->simpleName;
    }

    public function import(): string
    {
        if ($this->namespace->isRoot())
            return $this->simpleName;
        else
            return $this->getNamespace()->qualified() . ANamespace::DELIMITER . $this->simpleName;
    }

    public function fullyQualified(): string
    {
        if ($this->namespace->isRoot())
            return $this->getNamespace()->fullyQualified() . $this->simpleName;
        else
            return $this->getNamespace()->fullyQualified() . ANamespace::DELIMITER . $this->simpleName;
    }
}