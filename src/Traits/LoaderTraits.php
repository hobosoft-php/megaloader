<?php

namespace Hobosoft\MegaLoader\Traits;

use Hobosoft\MegaLoader\Contracts\ResolvingLocatorInterface;
use Hobosoft\MegaLoader\MiniConfig;
use Hobosoft\MegaLoader\MiniLogger;
use Hobosoft\MegaLoader\Type;

trait LoaderTraits
{
    public function __construct(
        protected MiniConfig $config,
        protected MiniLogger $logger,
        protected ResolvingLocatorInterface $locatorResolver,
    )
    {
        $this->logger->info(" *** Constructing Loader class: ".get_called_class().", resolver: ".get_class($this->locatorResolver)."");
    }

    public function locate(string $name, mixed $type = null): string|bool
    {
        $this->logger->info("loaderResolver:  locate:  resolving '$name' of type '".(Type::fromMixed($type)->name)."'");
        return $this->locatorResolver->locate($name, $type);
    }

    public function load(string $name, mixed $type = null): bool
    {
        $this->logger->info("loaderResolver:  load:  resolving '$name' of type '".(Type::fromMixed($type)->name)."'");
        return $this->resolve($name, $type);
    }
}
