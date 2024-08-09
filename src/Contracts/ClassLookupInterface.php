<?php

namespace Library\Classloader\Contracts;

interface ClassLookupInterface
{
    public function lookupClass(string $className): ?string;
}
