<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;

class LoaderDecorator extends ClassLoader
{
    public function __construct(
        MegaLoader                             $parent,
        \Closure|LocatorInterface|string|array $locator,
        protected LoaderInterface              $decoratedLoader,
    )
    {
        parent::__construct($parent, $locator);
    }

    public function setDecorated(LoaderInterface $deco): void
    {
        $this->decoratedLoader = $deco;
    }

    public function locate(string $name): string|bool
    {
        if(array_key_exists($name, $this->classMap)) {
            print(__METHOD__ . ":  Found classMap entry for '$name'.\n");
            return $this->classMap[$name];
        }
        if(($ret = $this->decoratedLoader->locate($name)) !== false) {
            print(__METHOD__ . ":  Located class for '$name' in file $ret.\n");
            return ($this->classMap[$name] = $ret);
        }
        return ($this->missingMap[$name] = false);
    }

    public function load(string $name): bool
    {
        return $this->decoratedLoader->load($name);
    }

    /*public function __call(string $name, array $args): mixed
    {
        if(method_exists($this->decoratedLoader, $name)) {
            return call_user_func_array([$this->decoratedLoader, $name], $args);
        }
        throw new \BadMethodCallException("Call to undefined method: ".get_class($this->decoratedLoader)."::{$name}");
    }*/
}
