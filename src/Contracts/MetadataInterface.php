<?php

namespace Hobosoft\MegaLoader\Contracts;

interface MetadataInterface
{
    public function getMetadata(string $name): string|bool;
    public function setMetadata(string $name, string $data): bool;
}