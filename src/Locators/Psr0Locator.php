<?php

namespace Hobosoft\MegaLoader\Locators;

class Psr0Locator extends AbstractLocator
{
    protected function makeFilename(string $path, array $className): string
    {
        return $path . implode('/', $className) . '.php';
    }

    public function locate(string $name): string|bool
    {
        $fn = strtr($name, '\\/', '/');
        $sfn = lcfirst($fn);
        if(file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $sfn . '.php'))) {
            return $sfn;
        }
        foreach(($this->config[$this->configSection.'.psr-0'] ?? []) as $k => $v) {
            $k = strtr($k, '\\/', '/');
            if(str_starts_with($fn, $k)) {
                if(file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $v . substr($fn, strlen($k)) . '.php'))) {
                    return $sfn;
                }
            }
        }
        return false;
    }
}
