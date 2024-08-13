<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\Boot\Paths;
use Hobosoft\FileLoaders\Loader\FileLocator;
use Hobosoft\FileLoaders\Loaders;

class PluginLoader extends AbstractLoader
{
    public function load(string $name): bool
    {
        // TODO: Implement load() method.
        return false;
    }

    public function locate(string $name): string|bool
    {
        foreach($this->config['megaloader.plugins'] as $namespace => $path) {
            if(str_starts_with($name, $namespace)) {
                $fn = Paths::join($path, substr($name, strlen($namespace)), 'manifest.yaml');
                $manifest = Loaders::load($fn, pathinfo($fn, PATHINFO_EXTENSION));
                if(class_exists(($cls = $manifest['manifest']['package']['class']), true) === false) {
                    throw new \Exception("Problems loading plugin root class '$cls'");
                }
                print_r($manifest);
            }
        }
        return false;
    }
}