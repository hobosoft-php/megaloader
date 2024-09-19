<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Traits\LocatorTraits;
use Hobosoft\MegaLoader\Utils;

class Psr4Locator implements LocatorInterface
{
    use LocatorTraits;

    protected function makeFilename(string $basePath, string $className, string $search, string|array $replace): string|bool
    {
        if (str_starts_with($className, $search)) {
            $replace = is_string($replace) ? [$replace] : $replace;
            $tail = substr(strtr($className, '\\/', DIRECTORY_SEPARATOR), strlen($search)) . '.php';
            while (!empty($replace)) {
                $entry = array_shift($replace);
                $root = ($entry[0] === '/') ? $entry : Utils::joinPaths(ROOTPATH, $entry);
                if (file_exists(($fn = Utils::joinPaths($root, $tail)))) {
                    return $fn;
                }
            }
        }
        return false;
    }

    public function locate(string $name): string|bool
    {
        foreach (($this->config['psr-4'] ?? []) as $k => $v) {
            if (($filename = $this->makeFilename(ROOTPATH, $name, $k, $v)) !== false) {
                return $filename;
            }
        }
        foreach (MegaLoader::$pluginPsr4 as $k => $v) {
            print("plugin psr4: '$k' = '$v'\n");
            if (($filename = $this->makeFilename(ROOTPATH, $name, $k, $v)) !== false) {
                return $filename;
            }
        }
        return false;
    }
}
