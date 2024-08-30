<?php

namespace Hobosoft\MegaLoader\Traits;

use Hobosoft\MegaLoader\Contracts\ResolvingLocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\MiniConfig;

trait LocatorTraits
{
    public function __construct(
        protected MiniConfig $config,
    )
    {
        print(" *** Constructing Locator class: ".get_called_class()."\n");
    }
}
