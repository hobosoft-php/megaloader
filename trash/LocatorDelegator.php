<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;

class LocatorDelegator extends AbstractLocator
{
    public function __construct(
        MegaLoader    $loader,
        private array $locators,
    )
    {
        parent::__construct($loader);
        $this->locators = [];
        foreach ($locators as $locator) {
            $this->locators[] = match (true) {
                is_string($locator) => static fn($pa) => new ($locator)($pa),
                $locator instanceof \Closure, $locator instanceof LocatorInterface => $locator,
                default => throw new \Exception("Invalid locator data type '{$locator}'."),
            };
        }
    }

    public function locate(string $name): array|string|bool
    {
        foreach ($this->locators as &$locator) {
            if ($locator instanceof \Closure) {
                $locator = ($locator)($this->loader);
            }
            if (($path = $locator->locate($name)) !== false) {
                return $path;
            }
        }
        return false;
    }
}