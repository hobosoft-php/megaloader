<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Locators\LocatorDelegator;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Utils;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractLoader implements LocatorInterface, LoaderInterface
{
    const string TYPE = 'unknown';

    public function __construct(
        protected MegaLoader                           $parent,
        private \Closure|LocatorInterface|string|array $locator,
    )
    {
    }

    public function getType(): string
    {
        return static::TYPE;
    }

    protected function getLogger(): PsrLoggerInterface
    {
        return $this->parent->getLogger();
    }

    protected function getConfig(): ConfigInterface
    {
        return $this->parent->getConfig();
    }

    public function getLocator(): LocatorInterface
    {
        return ($this->locator = match(true) {
            $this->locator instanceof LocatorInterface => $this->locator,
            $this->locator instanceof \Closure => ($this->locator)($this->parent),
            is_string($this->locator) => new ($this->locator)($this->parent),
            default => throw new \Exception("Locator var is unknown type."),
        });
    }

    public function setLocator(\Closure|LocatorInterface|string|array $loader): void
    {
        $this->locator = $loader;
    }

    public function locate(string $name): string|bool
    {
        return $this->getLocator()->locate($name);
    }

    abstract public function load(string $name): bool;
}
