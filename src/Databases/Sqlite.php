<?php

namespace Iphis\DbRestorer\Databases;

use Iphis\DbRestorer\DbRestorer;
use Symfony\Component\Process\Process;

class Sqlite extends DbRestorer
{
    protected function guardAgainstIncompleteCredentials()
    {
    }

    protected function getRestoreProcess(string $dumpFile): Process
    {
        $command = $this->getRestoreCommand($dumpFile);

        $process = new Process($command);

        if (!is_null($this->timeout)) {
            $process->setTimeout($this->timeout);
        }

        return $process;
    }

    /** {@inheritdoc} */
    public function getRestoreCommand(string $dumpFile): string
    {
        return implode(
            ' ',
            array(
                'echo \'BEGIN IMMEDIATE;\n.dump\' |',
                "\"{$this->restoreBinaryPath}sqlite3\" --bail",
                "\"{$this->dbName}\" >",
                "\"{$dumpFile}\"",
            )
        );
    }
}
