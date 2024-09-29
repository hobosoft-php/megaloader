<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;

abstract class AbstractLocator implements LocatorInterface
{
    public function __construct(
        protected MegaLoader $loader,
        protected string     $configSection = MegaLoader::CONFIG_SECTION,
    )
    {
    }

    public function setConfigSection(string $section): void
    {
        $this->configSection = $section;
    }

    abstract public function locate(string $name): array|string|bool;
}
