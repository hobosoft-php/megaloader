<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LoaderDelegator extends AbstractLoader
{
    protected array $loaders = [];

    public function registerLoader(\Closure|LoaderInterface|string $loader, string $type = 'class'): void
    {
        if(array_key_exists($type, $this->loaders) !== false) {
            throw new \Exception("Loader type '$type' already registered.");
        }
        $this->loaders[$type] = is_string($loader) ? static fn() => new $loader($this->logger, $this->config) : $loader;
    }

    public function locate(string $name, string $type = 'class'): string|bool
    {
        if(array_key_exists($type, $this->loaders) === false) {
            throw new \Exception("Loader type '$type' does not exist.");
        }
        return $this->loaders[$type]->locate($name);
    }

    public function load(string $name, string $type = 'class'): bool
    {
        if(($fn = $this->locate($name, $type)) === false) {
            return false;
        }
        if($this->loaders[$type] instanceof \Closure) {
            $this->loaders[$type] = ($this->loaders[$type])($this->logger, $this->config);
        }
        if (($arr = $this->loaders[$type]->load($fn)) !== null) {
            return $arr;
        }
        return false;
    }
}
