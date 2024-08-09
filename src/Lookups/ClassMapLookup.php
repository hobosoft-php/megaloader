<?php

namespace Hobosoft\MegaLoader\Loaders;

class ClassMapLookup extends \Hobosoft\MegaLoader\Loaders\AbstractLoader
{
    public function lookupClass(string $className): ?string
    {
        $this->logger->info(__METHOD__);
        return null;
    }
}
