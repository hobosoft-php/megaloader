<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\Boot\Paths;
use Hobosoft\MegaLoader\MegaLoader;

class Psr4Locator extends AbstractLocator
{
    protected function makeFilename(string $basePath, string $className, string $search, string|array $replace): string|bool
    {
        if($search === 'Hobosoft\\MegaLoader\\') {
            print("");
        }
        if(str_starts_with($className, $search)) {
            $replace = is_string($replace) ? [$replace] : $replace;
            $tail = substr(strtr($className, '\\/', DIRECTORY_SEPARATOR), strlen($search)) . '.php';
            while(!empty($replace)) {
                $entry = array_shift($replace);
                $root = ($entry[0] === '/') ? $entry : Paths::join(ROOTPATH, $entry);
                if(file_exists(($fn = Paths::join($root, $tail)))) {
                    return $fn;
                }
            }
        }
        return false;
    }

    public function locate(string $name): string|bool
    {
        foreach(($this->loader->getConfig()[$this->configSection.'.psr-4'] ?? []) as $k => $v) {
            if(($filename = $this->makeFilename(ROOTPATH, $name, $k, $v)) !== false) {
                return $filename;
            }
        }
        return false;
    }
}
