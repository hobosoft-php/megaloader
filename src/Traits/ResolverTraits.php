<?php

namespace Hobosoft\MegaLoader\Traits;

use Closure;
use Hobosoft\MegaLoader\Contracts\LoaderInterface;
use Hobosoft\MegaLoader\Contracts\LocatorInterface;
use Hobosoft\MegaLoader\Contracts\ResolverInterface;
use Hobosoft\MegaLoader\MiniConfig;
use Hobosoft\MegaLoader\Type;
use Hobosoft\MegaLoader\Utils;

trait ResolverTraits
{
    protected array $children = [];

    protected function addType(Type $type): self
    {
        if($this->hasType($type)) {
            throw new \InvalidArgumentException(__METHOD__.": Type already registered.");
        }
        $this->children[$type->value] = [];
        return $this;
    }

    protected function hasType(Type $type): bool
    {
        return isset($this->children[$type->value]);
    }

    public function add(Type $type, string $className, Closure $closure = null): mixed
    {
        print("adding type '{$type->name}' to ".get_called_class()."...$className.\n");
        if($this->hasType($type) === false) {
            $this->addType($type);
        }
        if(is_null($closure)) {
            class_exists($className, true);
            $this->children[$type->value][$className] = static fn($p, $q, $k) => new ($className)($p, $q, $k);
        }
        else {
            $this->children[$type->value][$className] = $closure;
        }
        return $this;
    }

    public function get(Type $type = null): mixed
    {
        return match(true) {
            is_null($type) => $this->children,
            $this->hasType($type) => $this->children[$type->value],
            default => false,
        };
    }

    public function decorate(Type $type, string $loaderClass, string $replacementClass): bool
    {
        if($this->hasType($type) === false) {
            return false;
        }
        $previousLoader = $this->children[$type->value][$loaderClass];
        $this->children[$type->value][$loaderClass] = function($re) use($replacementClass, $previousLoader) {
            return new ($replacementClass)($re, $previousLoader);
        };
        return true;
    }

    public function resolve(string $name, mixed $type = null): mixed
    {
        $type = Type::fromMixed($type);
        foreach(($this->children[$type->value] ?? []) as $k => $child) {
            if($child instanceof Closure) {
                $impl = class_implements($this);
                if(isset($impl[LocatorInterface::class])) {
                    $child = ($child)($this->config, $this->logger, $this);
                }
                else {
                    $child = ($child)($this->config, $this->logger, $this->locatorResolver);
                }
                $this->children[$type->value][$k] = $child;
            }
            $ret = match(true) {
                $child instanceof ResolverInterface => $child->resolve($name, $type),
                $child instanceof LocatorInterface => $child->locate($name),
                $child instanceof LoaderInterface => $child->load($name),
                default => false,
            };
            if($ret !== false) {
                return $ret;
            }
        }
        return false;
    }

    public function dump(): void
    {
        foreach($this->children as $type => $child) {
            $typeName = Type::fromMixed($type)->name;
            print("type:  ".$typeName."\n");
            foreach($child as $k => $v) {
                print("   $k\n");
            }
        }
    }

//    public function setConfig(MiniConfig $config): void
//    {
//        $this->config = $config;
//    }

    public function getConfig(): MiniConfig
    {
        return $this->config;
    }
}