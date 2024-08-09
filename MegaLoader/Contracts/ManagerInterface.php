<?php

namespace Hobosoft\MegaLoader\Contracts;

interface ManagerInterface
{
    public function setConfig(mixed $config): void;
    public function getConfig(string $string): mixed;
}