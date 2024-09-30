<?php

namespace Hobosoft\MegaLoader;

abstract class MiniDecorator
{
    protected mixed $decorated = null;

    protected function setDecoratedObject(mixed $object): void
    {
        $this->decorated = $object;
    }

    public function __call(string $name, array $arguments): mixed
    {
        if(is_null($this->decorated)) {
            if(method_exists(static::class, $name)) {
                return call_user_func_array([static::class, $name], $arguments);
            }
        }
        else {
            if(method_exists($this->decorated, $name)) {
                return call_user_func_array([$this->decorated, $name], $arguments);
            }
        }
        throw new \InvalidArgumentException("Call to undefined method MiniConfig::$name()");
    }
}