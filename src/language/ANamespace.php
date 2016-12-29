<?php
declare(strict_types = 1);

namespace mheinzerling\meta\language;

use mheinzerling\commons\StringUtils;

class ANamespace
{
    const DELIMITER = "\\";

    /**
     * @var string[]
     */
    private $segments;

    public static function absolute($fullyQualifiedName): ANamespace
    {
        if (!StringUtils::startsWith($fullyQualifiedName, self::DELIMITER)) throw new \Exception("Fully qualified name space required. (Starts with \\)");
        $namespace = new ANamespace();
        $segments = explode(self::DELIMITER, trim($fullyQualifiedName, "\\ \t\n\r\0\x0B"));
        if (count($segments) == 1 && $segments[0] == '') $segments = [];
        $namespace->segments = $segments;
        return $namespace;
    }

    public function resolve($simpleClassName): AClass
    {
        return AClass::resolve($this, $simpleClassName);
    }

    public function fullyQualified(): string
    {
        return self::DELIMITER . $this->qualified();
    }

    public function qualified(): string
    {
        return implode(self::DELIMITER, $this->segments);
    }

    public function isRoot(): bool
    {
        return empty($this->segments);
    }
}