<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Loaders\AbstractLoader;
use Hobosoft\MegaLoader\Lookups\AbstractLookup;

class NullLoader extends AbstractLookup
{
    public function loadClass(string $className): bool
    {
        $this->logger->warning(__METHOD__.':  this class cannot really load class "'.$className.'".');
        return null;
    }
}
