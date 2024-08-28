<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use phpDocumentor\Reflection\DocBlock\Tags\Implements_;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class LocatorDelegator extends AbstractLocator
{
    public function __construct(
        MegaLoader      $loader,
        private array   $locators,
    )
    {
        parent::__construct($loader);
        $this->locators = [];
        foreach ($locators as $locator) {
            $this->locators[] = match(true) {
                is_string($locator) => static fn($pa) => new ($locator)($pa),
                $locator instanceof \Closure => $locator,
                $locator instanceof LocatorInterface => $locator,
                default => throw new \Exception("Invalid locator data type '{$locator}'."),
            };
        }
    }

    public function locate(string $name, string $type = 'class'): string|bool
    {
        foreach ($this->locators as &$locator) {
            if($locator instanceof \Closure) {
                $locator = ($locator)($this->loader);
            }
            if (($path = $locator->locate($name, $type)) !== false) {
                return $path;
            }
        }
        return false;

        if(isset($this->locators[$type]) === false) {
            throw new \Exception("No locator is available for type $type");
        }
        if(($this->locators[$type] instanceof LocatorInterface) === false) {
            $this->locators[$type] = match(true) {
                $this->locators[$type] instanceof \Closure => ($this->locators[$type])($this->loader),
                is_callable($this->locators[$type]) => call_user_func($this->locators[$type], $this->loader),
                is_string($this->locators[$type]) => new ($this->locators[$type])($this->loader),
                default => throw new \Exception("Locator for '$type' is not the correct variable type."),
            };
        }
        return $this->locators[$type]->locate($name);
    }
}