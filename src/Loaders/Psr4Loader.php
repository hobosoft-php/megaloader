<?php

namespace Library\Classloader\Loaders;

class Psr4Loader extends AbstractLoader
{
    public function lookupClass(string $className): ?string
    {
        $fn = strtr($className, '\\/', '/');
        $sfn = lcfirst($fn);
        if(file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $sfn . '.php'))) {
            return $sfn;
        }
        foreach(($this->config['replaceStrings'] ?? []) as $k => $v) {
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
