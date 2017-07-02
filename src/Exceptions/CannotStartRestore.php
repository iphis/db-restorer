<?php

namespace Iphis\DbRestorer\Exceptions;

use Exception;

class CannotStartRestore extends Exception
{
    /**
     * @param string $name
     *
     * @return \Iphis\DbRestorer\Exceptions\CannotStartRestore
     */
    public static function emptyParameter($name)
    {
        return new static("Parameter `{$name}` cannot be empty.");
    }
}
