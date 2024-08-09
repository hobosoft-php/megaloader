<?php

namespace Library\Classloader\Contracts;

interface MetadataInterface
{
    public function getMetadata(string $name): string|bool;
    public function setMetadata(string $name, string $data): bool;
}