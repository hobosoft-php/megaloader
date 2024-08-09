<?php

namespace Library\Classloader\Loaders;

class ScanningLoader extends AbstractLoader
{
    public function lookupClass(string $className): ?string
    {
        $cwd = ROOTPATH;
        $fn = strtr($className, '\\/', '/');
        $sfn = lcfirst($fn);
        if(file_exists(($sfn = $cwd . DIRECTORY_SEPARATOR . $sfn . '.php'))) {
            return $sfn;
        }
        return null;
    }
}