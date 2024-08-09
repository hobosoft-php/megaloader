<?php

namespace Hobosoft\MegaLoader\Metadata;

class ClassInfo
{
    public function __construct(
        public readonly string $className,
        public readonly string $fileName,
        public readonly array $extra = [],
        public readonly bool $loaded = false,
    )
    {
    }

}
