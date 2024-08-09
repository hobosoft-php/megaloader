<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\MegaLoader\Contracts\ClassLoaderInterface;
use Hobosoft\MegaLoader\Loaders\AbstractLoader;

class ClassLoader extends AbstractLoader
{
    public function lookupClass(string $className): ?string
    {
        // TODO: Implement lookupClass() method.
    }

    public function loadClass(string $className): bool
    {
        if (($info = $this->lookupClass($className)) === null) {
            $this->logger->info("Lookup failed with loader ".static::class.".");
            if($this->fallback instanceof ClassLoaderInterface)
            $this->logger->debug("Lookup failed!  Trying fallback loader ".get_class($this->fallback).".");
            return ($this->fallback instanceof ClassLoaderInterface) && $this->fallback->loadClass($className);
        }
        (function(string $fn):void { require $fn; })($info);
        return true;
    }
}
