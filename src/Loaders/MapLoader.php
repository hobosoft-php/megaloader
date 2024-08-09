<?php

namespace Library\Classloader\Loaders;

class MapLoader extends \Library\Classloader\Loaders\AbstractLoader
{
    public function lookupClass(string $className): ?string
    {
        $this->logger->info(__METHOD__);
        return null;
    }
}
