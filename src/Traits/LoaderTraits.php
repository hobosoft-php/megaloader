<?php

namespace Hobosoft\MegaLoader\Traits;

use Hobosoft\MegaLoader\Contracts\ResolvingLocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MiniConfig;
use Hobosoft\MegaLoader\Type;

trait LoaderTraits
{
    //private $locatorResolver;

    public function __construct(
        protected MiniConfig $config,
        protected ResolvingLocatorInterface $locatorResolver,
    )
    {
        print(" *** Constructing Loader class: ".get_called_class().", resolver: ".get_class($this->locatorResolver)."\n");
    }
/*
    public function getLocatorResolver(): ResolvingLocatorInterface
    {
        if(isset($this->locatorResolver) === false) {
            $this->locatorResolver = $this->loader->getLocatorResolver();
        }
        return $this->locatorResolver;
    }

    public function setLocatorResolver(ResolvingLocatorInterface $locatorResolver): void
    {
        $this->locatorResolver = $locatorResolver;
    }
*/
    public function locate(string $name, mixed $type = null): string|bool
    {
        print("loaderResolver:  locate:  resolving '$name' of type '".(Type::fromMixed($type)->name)."'\n");
        return $this->locatorResolver->locate($name, $type);
    }

    public function load(string $name, mixed $type = null): bool
    {
        print("loaderResolver:  load:  resolving '$name' of type '".(Type::fromMixed($type)->name)."'\n");
        return $this->resolve($name, $type);
    }
}
