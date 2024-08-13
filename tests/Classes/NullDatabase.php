<?php

namespace Hobosoft\MegaLoader\Tests\Classes;

class NullDatabase extends AbstractDatabase
{
    public function method(string $arg): void
    {
        print(__METHOD__ . " called!\n");
    }
}