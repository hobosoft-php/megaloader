<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Boot\Paths;
use Hobosoft\MegaLoader\Loaders\AbstractLoader;

class PluginLoader extends AbstractLoader
{
    public function load(string $name): bool
    {
        // TODO: Implement load() method.
    }

    public function locate(string $name): string|bool
    {
        foreach($this->config['megaloader.plugins'] as $namespace => $path) {
            $path = rtrim(str_replace('\\', DIRECTORY_SEPARATOR, $namespace), DIRECTORY_SEPARATOR);
            $fn = Paths::join($path, 'manifest.yaml');
            $manifest = file_get_contents($fn);
            print($manifest);
        }
        return false;
    }
}