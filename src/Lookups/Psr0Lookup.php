<?php

namespace Hobosoft\MegaLoader\Loaders;

class Psr0Lookup extends \Hobosoft\MegaLoader\Loaders\AbstractLoader
{
    public function lookupClass(string $className): ?string
    {
        $sfn = lcfirst($fn = strtr($className, '\\/', '/'));
        if(file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $sfn . '.php'))) {
            return $sfn;
        }
        foreach(($this->parent->getConfig('psr-0') ?? []) as $k => $v) {
            $k = strtr($k, '\\/', '/');
            if(str_starts_with($fn, $k)) {
                if(file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $v . substr($fn, strlen($k)) . '.php'))) {
                    return $sfn;
                }
            }
        }
        return null;
    }
}
