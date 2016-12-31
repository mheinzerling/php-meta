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

    public static function absolute(string $fullyQualifiedName): ANamespace
    {
        if (!StringUtils::startsWith($fullyQualifiedName, self::DELIMITER))
            throw new \Exception("Fully qualified namespace required, starting with \\. Got: >$fullyQualifiedName<");
        $namespace = new ANamespace();
        $segments = explode(self::DELIMITER, trim($fullyQualifiedName, "\\ \t\n\r\0\x0B"));
        if (count($segments) == 1 && $segments[0] == '') $segments = [];
        $namespace->segments = $segments;
        return $namespace;
    }

    public static function root(): ANamespace
    {
        return self::absolute("\\");
    }

    public function resolve(string $simpleClassName): AClass
    {
        return AClass::resolve($this, $simpleClassName);
    }

    public function fullyQualified(): string
    {
        return self::DELIMITER . $this->qualified();
    }

    public function qualified(): string
    {
        $qualified = implode(self::DELIMITER, $this->segments);
        if ($qualified == self::DELIMITER) $qualified = "";
        return $qualified;
    }

    public function isRoot(): bool
    {
        return empty($this->segments);
    }
}