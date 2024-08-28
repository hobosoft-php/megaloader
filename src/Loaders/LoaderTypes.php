<?php

namespace Hobosoft\MegaLoader\Loaders;

enum LoaderTypes: int
{
    case CLASS = 0;
    case PLUGIN = 1;
    case MODULE = 2;
    case FUNCTION = 3;
    case PRELOADER = 4;
    case UNKNOWN = -1;

    public function asString(): string
    {
        return strtolower($this->name);
    }
}
