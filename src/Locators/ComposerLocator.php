<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Composer\Composer;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Contracts\ResolverInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Traits\LocatorTraits;
use Hobosoft\MegaLoader\Traits\ResolverTraits;
use Hobosoft\MegaLoader\Type;

class ComposerLocator implements LocatorInterface, ResolverInterface
{
    use LocatorTraits, ResolverTraits;

    private Composer $composer;
    private array $autoload = [];

    public function locate(string $name): array|string|bool
    {
        if(isset($this->composer) === false) {
            $this->add(Type::T_CLASS, MapLocator::class);
            $this->add(Type::T_CLASS, Psr4Locator::class);
            $this->add(Type::T_CLASS, Psr0Locator::class);
            $this->composer = $this->loader->getComposer();
            $this->autoload = $this->composer->loadAutoload(ROOTPATH);
        }
        return $this->resolve($name, Type::T_CLASS);
    }
}
