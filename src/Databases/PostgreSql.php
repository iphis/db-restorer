<?php

namespace Iphis\DbRestorer\Databases;

use Iphis\DbRestorer\DbRestorer;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;
use Symfony\Component\Process\Process;

class PostgreSql extends DbRestorer
{
    /** @var bool */
    protected $useInserts = false;

    public function __construct()
    {
        $this->port = 5432;
    }

    /**
     * Restore the contents of the database from the given file.
     *
     * @param string $dumpFile
     *
     * @throws \Iphis\DbRestorer\Exceptions\CannotStartRestore
     * @throws \Iphis\DbRestorer\Exceptions\RestoreFailed
     */
    public function restoreFromFile(string $dumpFile)
    {
        $this->guardAgainstIncompleteCredentials();

        $command = $this->getRestoreCommand($dumpFile);

        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $this->getContentsOfCredentialsFile());
        $temporaryCredentialsFile = stream_get_meta_data($tempFileHandle)['uri'];

        $process = new Process(
            $command,
            null,
            $this->getEnvironmentVariablesForRestoreCommand($temporaryCredentialsFile)
        );

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
        $command = array(
            "'{$this->restoreBinaryPath}pg_restore'",
            "-U {$this->userName}",
            '-h '.($this->socket === '' ? $this->host : $this->socket),
            "-p {$this->port}",
        );

        foreach ($this->extraOptions as $extraOption) {
            $command[] = $extraOption;
        }

        if (!empty($this->getDbName())) {
            $command[] = '-d '.$this->getDbName();
        }

        if (!empty($this->onlyTables)) {
            $command[] = '-t '.implode(' -t ', $this->onlyTables);
        }

        if (!empty($this->excludeTables)) {
            $command[] = '-T '.implode(' -T ', $this->excludeTables);
        }

        $command[] = "{$dumpFile}";

        return implode(' ', $command);
    }

    public function getContentsOfCredentialsFile(): string
    {
        $contents = array(
            $this->host,
            $this->port,
            $this->dbName,
            $this->userName,
            $this->password,
        );

        return implode(':', $contents);
    }

    protected function guardAgainstIncompleteCredentials()
    {
        foreach (array('userName', 'dbName', 'host') as $requiredProperty) {
            if (empty($this->$requiredProperty)) {
                throw CannotStartRestore::emptyParameter($requiredProperty);
            }
        }
    }

    protected function getEnvironmentVariablesForRestoreCommand(string $temporaryCredentialsFile): array
    {
        return array(
            'PGPASSFILE' => $temporaryCredentialsFile,
            'PGDATABASE' => $this->dbName,
        );
    }
}
