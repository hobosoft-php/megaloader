<?php

namespace Library\Classloader\Metadata;

use Library\Classloader\Contracts\MetadataInterface;

class ClassMetadata extends GenericMetadata
{
    public array $info = [
        'filename' => null,
        'classname' => null,
        'namespace' => null,

        'loadCount' => 0,

        'cacheFilename' => null,
    ];

    public function __construct(
        public readonly string $fileName,
        public readonly string $className,
        public readonly string $namespace,
    )
    {
        $this->setMetadata('fileName', $fileName);
        $this->setMetadata('className', $className);
        $this->setMetadata('namespace', $namespace);
        //$this->info['loadCount'] = $loadCount;
        //$this->info['cacheFileName'] = $fileName;
    }
}