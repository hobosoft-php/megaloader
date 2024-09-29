<?php

use Library\Autoloader\Scanners\GlobScanner;

class FileCache implements \Psr\SimpleCache\CacheInterface
{
    public function __construct(
        protected string $path,
    )
    {
        if (!file_exists($this->path) && !mkdir($concurrentDirectory = $this->path, 0755, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return file_get_contents($this->path . DIRECTORY_SEPARATOR . $key);
        }
        return $default;
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        file_put_contents($this->path . DIRECTORY_SEPARATOR . $key, $value);
        return true;
    }

    public function delete(string $key): bool
    {
        unlink($this->path . DIRECTORY_SEPARATOR . $key);
        return true;
    }

    public function clear(): bool
    {
        $scanner = new GlobScanner();
        $files = $scanner->scan($this->path);
        foreach ($files as $file) {
            print("cache cleared: $file\n");
            //unlink($file);
        }
        return true;
    }

    public function has(string $key): bool
    {
        return file_exists($this->path . DIRECTORY_SEPARATOR . $key);
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key);
        }
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }
}
