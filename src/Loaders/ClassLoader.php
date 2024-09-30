<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Contracts\ResolverInterface;
use Hobosoft\MegaLoader\Traits\LoaderTraits;
use Hobosoft\MegaLoader\Type;
use Hobosoft\MegaLoader\Utils;

/**
 * @method resolve(string $name, mixed $type)
 */
class ClassLoader implements LoaderInterface
{
    use LoaderTraits;

    public function load(string $name): bool
    {
        print("ClassLoader loading file: $name\n");
        return ($located = $this->locatorResolver->locate($name, Type::T_CLASS)) && Utils::includeFile($located);
    }
}
