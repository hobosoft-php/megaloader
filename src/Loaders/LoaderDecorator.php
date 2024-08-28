<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class LoaderDecorator extends ClassLoader
{
    public function __construct(
        MegaLoader                             $loader,
        \Closure|LocatorInterface|string|array $locator,
        private readonly LoaderInterface       $decoratedLoader,
    )
    {
        parent::__construct($loader, $locator);
    }

    public function __call(string $name, array $args): mixed
    {
        if(method_exists($this->decoratedLoader, $name)) {
            return call_user_func_array([$this->decoratedLoader, $name], $args);
        }
        throw new \BadMethodCallException("Call to undefined method: ".get_class($this->decoratedLoader)."::{$name}");
    }
}
