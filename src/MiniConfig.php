<?php

namespace Hobosoft\MegaLoader;

use Hobosoft\Config\Utils;

class MiniConfig extends MiniDecorator implements \ArrayAccess
{
    const string CLASSNAME = __CLASS__;

    public function __construct(
        protected array $config = [],
    )
    {
    }

    public function setConfig(mixed $config): void
    {
        if(is_array($config)) {
            $this->config = $config;
        }
        else {
            $this->setDecoratedObject($config);
            //$config->merge($this->config);
        }
    }

    public function toArray(): array
    {
        return $this->config;
    }

    /*
    private function mergeLevel(array &$dest, array $source, bool $replaceExisting = false): void
    {
        $todo = [];
        while(!empty($source)) {
            $k = array_key_first($config);
            $v = array_shift($config);
            if(is_array($v)) {
                $todo[$k] = $v;
            }
            else if(isset($dest[$k])) {
                if($replaceExisting) {
                    $dest[$k] = $v;
                }
            }
            else {
                $dest[$k] = $v;
            }
        }
        foreach($todo as $k => $v) {
            $this->mergeLevel($dest[$k], $source[$k], $replaceExisting);
        }
    }

    public function merge(array|MiniConfig $config, bool $replaceExisting = false): void
    {
        foreach($config as $name => $value) {
            if(is_array($value)) {

            }
            if($replaceExisting === false && $this->has($name)) {
                $this->set($name, $value);
            }
            else {
                $this->set($name, $value);
            }
        }
    }
    */

    public function has(string $name): bool
    {
        return isset($this->config[$name]);
    }

    public function get(string $name = null): array|false
    {
        return is_null($name) ? $this->config : $this->config[$name] ?? false;
    }

    public function set(mixed $name, mixed $data = null): void
    {
        if(is_null($data)) {
            $this->config = $name;
            return;
        }
        $this->config[$name] = $data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->config[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->config[$offset]);
    }

    public function merge(array $cfg)
    {
        $this->config = array_merge($this->config, $cfg);
    }
}