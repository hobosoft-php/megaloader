<?php

namespace Hobosoft\MegaLoader\Loaders;

class Psr0Loader extends \Hobosoft\MegaLoader\Loaders\AbstractLoader
{
    public function lookupClass(string $className): ?string
    {
        $fn = strtr($className, '\\/', '/');
        $sfn = lcfirst($fn);
        if(file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $sfn . '.php'))) {
            return [
                'loader' => static::class,
                'fileName' => $sfn,
                'className' => $className,
            ];

        }
        foreach(($this->config['replaceStrings'] ?? []) as $k => $v) {
            $k = strtr($k, '\\/', '/');
            if(str_starts_with($fn, $k)) {
                if(file_exists(($sfn = ROOTPATH . DIRECTORY_SEPARATOR . $v . substr($fn, strlen($k)) . '.php'))) {
                    return [
                        'loader' => static::class,
                        'fileName' => $sfn,
                        'className' => $className,
                    ];

                }
            }
        }
        return null;
    }
}
