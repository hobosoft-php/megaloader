<?php

namespace Hobosoft\MegaLoader\Composer;

class ComposerJson
{
    private array $items;
    private bool $modified;

    protected static function load(string $filename): array
    {
        $filename = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filename);
        $filename = rtrim($filename, DIRECTORY_SEPARATOR);
        return json_decode(file_get_contents($filename), true);
    }

    public function __construct(
        private string $filename,
    )
    {
        $this->items = self::load($this->filename);
        $this->modified = false;
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function getKey(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $ret = $this->items;
        foreach ($keys as $k => $v) {
            unset($keys[$k]);
            if (array_key_exists($v, $ret) === false) {
                return $default;
            }
            $ret = $ret[$v];
        }
        return $ret;
    }

    private function finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');
        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }

    private function normalizePaths($value, $path)
    {
        $path && $path = $this->finish($path, '/');
        foreach ($value as $namespace => $_path) {
            if (is_array($_path)) {
                foreach ($_path as $i => $p) {
                    $value[$namespace][$i] = str_replace('//', '/', $path . $this->finish($p, '/'));
                }
            } else {
                $value[$namespace] = str_replace('//', '/', $path . $this->finish($_path, '/'));
            }
        }

        return $value;
    }

    public function isModified(): bool
    {
        return ($this->modified);
    }
}