<?php

namespace Hobosoft\MegaLoader\Contracts;

interface ClassLookupInterface
{
    public function lookupClass(string $className): ?string;
}
