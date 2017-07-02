<?php

namespace Iphis\DbRestorer\Exceptions;

use Exception;

class CannotSetParameter extends Exception
{
    /**
     * @param string $name
     * @param string $conflictName
     *
     * @return \Iphis\DbRestorer\Exceptions\CannotSetParameter
     */
    public static function conflictingParameters($name, $conflictName)
    {
        return new static("Cannot set `{$name}` because it conflicts with parameter `{$conflictName}`.");
    }
}
