<?php

namespace Hobosoft\MegaLoader\Tests\Classes;

abstract class AbstractDatabase
{
    public function method(string $arg): void
    {
        print(__METHOD__ . " called!\n");
    }
}