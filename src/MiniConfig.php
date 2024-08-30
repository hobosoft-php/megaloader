<?php

namespace Hobosoft\MegaLoader;

class MiniConfig implements \ArrayAccess
{
    public function __construct(
        protected array $config = [],
    )
    {
    }

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
}