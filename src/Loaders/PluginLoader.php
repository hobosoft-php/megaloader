<?php

namespace Hobosoft\MegaLoader\Loaders;

use Hobosoft\FileLoaders\Loaders;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Utils;

class PluginLoader implements LoaderInterface
{
    const string TYPE = 'plugin';

    public function locate(string $name): string|bool
    {
        foreach ($this->parent->getConfig()['megaloader.plugins'] as $namespace => $path) {
            if (str_starts_with($name, $namespace)) {
                $fn = Utils::joinPaths($path, substr($name, strlen($namespace)), 'manifest.yaml');
                $manifest = Loaders::load($fn, pathinfo($fn, PATHINFO_EXTENSION));
                if (class_exists(($cls = $manifest['manifest']['package']['class']), true) === false) {
                    throw new \Exception("Problems loading plugin root class '$cls'");
                }
                print_r($manifest);
            }
        }
        return false;
    }

    public function load(string $name): bool
    {

        return false;
    }
}