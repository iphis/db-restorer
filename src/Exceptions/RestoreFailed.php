<?php

namespace Iphis\DbRestorer\Exceptions;

use Exception;
use Symfony\Component\Process\Process;

class RestoreFailed extends Exception
{
    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Iphis\DbRestorer\Exceptions\RestoreFailed
     */
    public static function processDidNotEndSuccessfully(Process $process)
    {
        return new static(
            "The restore process failed with exitcode {$process->getExitCode()} : {$process->getExitCodeText()} : {$process->getErrorOutput()}"
        );
    }

    /**
     * @return \Iphis\DbRestorer\Exceptions\RestoreFailed
     */
    public static function restorefileWasNotReadable()
    {
        return new static('The restorefile could not be read');
    }

    /**
     * @return \Iphis\DbRestorer\Exceptions\RestoreFailed
     */
    public static function restorefileWasEmpty()
    {
        return new static('The provied restorefile is empty');
    }
}
