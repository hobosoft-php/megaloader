<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Locators\LocatorDelegator;
use Hobosoft\MegaLoader\MegaLoader;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderDelegator extends ClassLoader
{
    protected array $loaders = [];

    public function __construct(
        protected MegaLoader $parent,
        array                $loaders = [],
    )
    {
        parent::__construct($parent, $this);
        foreach ($loaders as $loader => $locators) {
            $type = constant($loader . '::TYPE');
            $this->register($loader, $locators, $type);
        }
    }

    public function register(\Closure|LoaderInterface|string $loader, \Closure|LocatorInterface|string|array $locator, string $type = ''): void
    {
        if(array_key_exists($type, $this->loaders) !== false) {
            throw new \Exception("Loader type '$type' already registered.");
        }

        $locator = match(true) {
            $locator instanceof LocatorInterface => $locator,
            $locator instanceof \Closure => $locator,
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
            ;
            $type = constant(($loader = ($loader instanceof \Closure) ? ($loader)($this->parent) : $loader) . '::TYPE');
        }

        $this->loaders[$type] = $loader;

        /*
        if(is_string($loader)) {
            $type = ($type === '') ? constant($loader . '::TYPE') : $type;
            $loader = static fn($th) => new ($loader)($th, $locator);
        }

        if(is_array($loader)) {
            if(count($loader) > 1) {
                throw new \Exception("Cannot register arrays, array is for '[ loader => [ locators... ]]' format.");
            }
            foreach($loader as $loaderClass => $locatorClasses) {
                $type = ($type === '') ? constant($loaderClass . '::TYPE') : $type;
                $loader = static fn($th) => new ($loaderClass)($th, $locatorClasses);
            }
        }

        if($loader instanceof \Closure) {
            $loader = ($loader)($this->parent);
        }

        if($loader instanceof LocatorInterface) {
            $loader = $loader;
        }*/

       // $this->loaders[$type] = $loader;
    }

    public function locate(string $name, string $type = 'class'): string|bool
    {
        if(array_key_exists($type, $this->loaders) === false) {
            throw new \Exception("Loader type '$type' does not exist.");
        }

        if($this->loaders[$type] instanceof \Closure) {
            $this->loaders[$type] = ($this->loaders[$type])($this->parent);
        }

        return $this->loaders[$type]->locate($name, $type);

        $arr = $this->loaders[$type];
        foreach($arr as $locator) {
            //if(($path = $locator->locate($name, $type)) !== false) {
            //    return $path;
            //}
        }
        return false;
    }

    public function load(string $name, string $type = 'class'): bool
    {
        if(($fn = $this->locate($name, $type)) === false) {
            return false;
        }
        if(($arr = $this->loaders[$type]->load($fn)) !== null) {
            return $arr;
        }
        return false;
    }
}
