<?php

namespace Iphis\DbRestorer\Databases;

use Iphis\DbRestorer\DbRestorer;
use Symfony\Component\Process\Process;

class Sqlite extends DbRestorer
{
    /**
     * Dump the contents of the database to a given file.
     *
     * @param string $dumpFile
     *
     * @throws \Iphis\DbRestorer\Exceptions\RestoreFailed
     */
    public function restoreFromFile(string $dumpFile)
    {
        $command = $this->getRestoreCommand($dumpFile);

        $process = new Process($command);

        if (!is_null($this->timeout)) {
            $process->setTimeout($this->timeout);
        }

        $process->run();

        $this->checkIfRestoreWasSuccessFul($process, $dumpFile);
    }

    /**
     * Get the command that should be performed to dump the database.
     *
     * @param string $dumpFile
     *
     * @return string
     */
    public function getRestoreCommand(string $dumpFile): string
    {
        return implode(
            ' ',
            [
                'echo \'BEGIN IMMEDIATE;\n.dump\' |',
                "\"{$this->restoreBinaryPath}sqlite3\" --bail",
                "\"{$this->dbName}\" >",
                "\"{$dumpFile}\"",
            ]
        );
    }
}
