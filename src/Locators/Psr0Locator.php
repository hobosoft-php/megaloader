<?php

namespace Hobosoft\MegaLoader\Locators;

use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\MegaLoader;
use Hobosoft\MegaLoader\Traits\LocatorTraits;
use Hobosoft\MegaLoader\Utils;

class Psr0Locator implements LocatorInterface
{
    use LocatorTraits;

    protected function makeFilename(string $path, array $className): string
    {
        return $path . implode('/', $className) . '.php';
    }

    public function locate(string $name): string|bool
    {
        $fn = strtr($name, '\\/', '/');
        $sfn = lcfirst($fn);
        if (file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $sfn . '.php'))) {
            return $sfn;
        }
        foreach (($this->config['psr-0'] ?? []) as $k => $v) {
            $k = strtr($k, '\\/', '/');
            if (str_starts_with($fn, $k)) {
                if (file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $v . substr($fn, strlen($k)) . '.php'))) {
                    return $sfn;
                }
            }
        }
        return false;
    }
}
