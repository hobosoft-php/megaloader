<?php

namespace Hobosoft\MegaLoader;

enum Type: int
{
    case T_NULL = 0;
    case T_CLASS = 1;
    case T_PLUGIN = 2;
    case T_MODULE = 4;
    case T_CONFIG = 8;
    case T_ASSET = 16;
    case T_TEMPLATE = 32;

    public static function fromMixed(mixed $type): ?static
    {
        if($type instanceof static) {
            return $type;
        }
        if(is_string($type)) {
            return self::fromString($type);
        }
        if(is_int($type)) {
            return match($type) {
                self::T_CLASS->value => self::T_CLASS,
                self::T_PLUGIN->value => self::T_PLUGIN,
                self::T_MODULE->value => self::T_MODULE,
                self::T_CONFIG->value => self::T_CONFIG,
                self::T_ASSET->value => self::T_ASSET,
                self::T_TEMPLATE->value => self::T_TEMPLATE,
                default => self::T_NULL,
            };
        }
        return self::T_NULL;
    }

    public static function fromString(int|string $type)
    {
        return match(strtolower($type)) {
            'class' => self::T_CLASS,
            'plugin' => self::T_PLUGIN,
            'module' => self::T_MODULE,
            'config' => self::T_CONFIG,
            'asset' => self::T_ASSET,
            'template' => self::T_TEMPLATE,
            default => self::T_NULL,
        };
    }
}
