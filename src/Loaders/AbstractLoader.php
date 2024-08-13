<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractLoader implements LocatorInterface, LoaderInterface
{
    public function __construct(
        protected PsrLoggerInterface $logger,
        protected ConfigInterface    $config,
        protected array              $locators = [],
    ) {}

    public function locate(string $name): string|bool
    {
        foreach ($this->locators as $i => $locator) {
            if($locator instanceof \Closure) {
                $this->locators[$i] = $locator();
            }
            else if(is_string($locator)) {
                $this->locators[$i] = new $locator($this->logger, $this->config);
            }
            if(($filename = $this->locators[$i]->locate($name)) !== false) {
                return $filename;
            }
        }
        return false;
    }

    //abstract public function load(string $name): bool;
}
