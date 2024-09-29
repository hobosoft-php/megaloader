<?php

namespace Hobosoft\MegaLoader\Traits;

use Hobosoft\MegaLoader\Contracts\ResolvingLocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MiniConfig;
use Hobosoft\MegaLoader\MiniLogger;

trait LocatorTraits
{
    public function __construct(
        protected MiniConfig $config,
        protected MiniLogger $logger,
    )
    {
        $this->logger->info(" *** Constructing Locator class: ".get_called_class()."");
    }
}
