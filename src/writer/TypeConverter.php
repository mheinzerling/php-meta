<?php
declare(strict_types = 1);

namespace mheinzerling\meta\writer;

use mheinzerling\meta\language\ClassPHPType;
use mheinzerling\meta\language\PHPType;
use mheinzerling\meta\language\PrimitivePHPType;

class TypeConverter
{

    public static function getterPrefix(PHPType $type): string
    {
        if ($type instanceof PrimitivePHPType && $type->isBool()) return "is";
        elseif ($type instanceof PrimitivePHPType || $type instanceof ClassPHPType) return 'get';
        throw new \Exception("Unhandled type " . get_class($type));
    }

    public static function toPHPDoc(PHPType $type, ClassWriter $classWriter): string
    {
        $result = "";
        if ($type instanceof PrimitivePHPType) {
            $result .= $type->getToken();//TODO array of Object
        } elseif ($type instanceof ClassPHPType) {
            $result .= $classWriter->print($type->getClass());
        } else {
            throw new \Exception("Unhandled type " . get_class($type));
        }
        if ($type->isOptional()) $result .= "|null";
        return $result;
    }

    public static function toPHP(PHPType $type, ClassWriter $classWriter): string
    {
        $result = '';
        if ($type->isOptional()) $result .= "?";
        if ($type instanceof PrimitivePHPType) {
            $result .= $type->getToken();
        } elseif ($type instanceof ClassPHPType) {
            $result .= $classWriter->print($type->getClass());
        } else {
            throw new \Exception("Unhandled type " . get_class($type));
        }
        return $result;
    }

    public static function toValue(PHPType $type, $value): string
    {
        if ($type instanceof PrimitivePHPType) {
            if ($value == null) return 'null';
            else if ($type->isInt()) return $value;
            elseif ($type->isBool()) return (empty($value)) ? "false" : "true";
            return "'" . $value . "'";
        } elseif ($type instanceof ClassPHPType) {
            if ($value == null) return 'null';
            throw new \Exception("Unsupported for objects " . $type->getClass()->fullyQualified());
        } else {
            throw new \Exception("Unhandled type " . get_class($type));
        }
    }
}