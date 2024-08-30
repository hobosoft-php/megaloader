<?php

namespace Hobosoft\MegaLoader\Metadata;

use Hobosoft\MegaLoader\Contracts\MetadataInterface;

class GenericMetadata implements MetadataInterface
{
    protected array $info = [];

    public function getMetadata(string $name): string|bool
    {
        if (isset($this->info[$name]) === true) {
            return $this->info[$name];
        }
        return false;
    }

    public function setMetadata(string $name, string $data): bool
    {
        if (isset($this->info[$name]) === false) {
            $this->info[$name] = $data;
            return true;
        }
        throw new \Exception("Metadata '$name' already exists");
    }
}