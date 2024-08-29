<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Locators\LocatorDelegator;
use Hobosoft\MegaLoader\MegaLoader;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderDelegator extends AbstractLoader
{
    protected array $loaders = [];

    public function __construct(
        protected MegaLoader $parent,
        array                $loaders = [],
    )
    {
        parent::__construct($parent, $this);
        foreach ($loaders as $loader => $locators) {
            $this->register($loader, $locators, constant($loader . '::TYPE'));
            print(__METHOD__ . ":  Registered loader '$loader' with locators:  " . implode(', ', is_string($locators) ? [$locators] : $locators) . ".\n");
        }
    }

    public function register(\Closure|LoaderInterface|string $loader, \Closure|LocatorInterface|string|array $locator, string $type = ''): void
    {
        if(array_key_exists($type, $this->loaders) !== false) {
            throw new \Exception("Loader type '$type' already registered.");
        }

        $locator = match(true) {
            $locator instanceof LocatorInterface, $locator instanceof \Closure => $locator,
            is_string($locator) => static fn($pa) => new ($locator)($pa),
            is_array($locator) => static fn($pa) => new LocatorDelegator($pa, $locator),
        };

        $loader = match(true) {
            $loader instanceof LoaderInterface => $loader,
            $loader instanceof \Closure => ($loader)($this->parent),
            is_string($loader) => static fn($pa) => new ($loader)($pa, $locator),
            is_array($loader) => (function() use($loader): \Closure|LoaderInterface {
                return (count($loader) > 1) ?
                    throw new \Exception("Cannot register arrays with more than one loader per type.") :
                    static fn($th) => new (array_key_first($loader))($th, array_shift($loader));
            })(),
        };

        if($type === '') {
            $type = constant(($loader = ($loader instanceof \Closure) ? ($loader)($this->parent) : $loader) . '::TYPE');
        }

        $this->loaders[$type] = $loader;
    }

    public function getLoaderByType(string $type): LoaderInterface|bool
    {
        return $this->loaders[$type] ?? false;
    }

    public function replaceType(string $type, \Closure|LoaderInterface|string $newLoader): void
    {
        if(array_key_exists($type, $this->loaders) === false) {
            throw new \Exception("Loader type '$type' does not exist.");
        }
        if(is_string($newLoader) !== false) {
            $newLoader = static fn($th) => new ($newLoader)($th);
        }
        $this->loaders[$type] = $newLoader;
        print("Replaced loader for type '$type' with new loader.\n");
    }

    public function locate(string $name, string $type = 'class'): string|bool
    {
        if(array_key_exists($type, $this->loaders) === false) {
            throw new \Exception("Loader type '$type' does not exist.");
        }
        else if($this->loaders[$type] instanceof \Closure) {
            $this->loaders[$type] = ($this->loaders[$type])($this->parent);
        }

        print(__METHOD__ . ": Locating '$name' of type '$type'...");
        return $this->loaders[$type]->locate($name, $type);
    }

    public function load(string $name, string $type = 'class'): bool
    {
        if(array_key_exists($type, $this->loaders) === false) {
            throw new \Exception("Loader type '$type' does not exist.");
        }
        else if($this->loaders[$type] instanceof \Closure) {
            $this->loaders[$type] = ($this->loaders[$type])($this->parent);
        }

        print(__METHOD__ . ": Loading class '$name' of type '$type'...\n");
        return $this->loaders[$type]->load($name, $type);
    }
}
