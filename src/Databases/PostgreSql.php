<?php

namespace Iphis\DbRestorer\Databases;

use Iphis\DbRestorer\DbRestorer;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;
use Symfony\Component\Process\Process;

class PostgreSql extends DbRestorer
{
    public function __construct()
    {
        $this->port = 5432;
    }

    protected function getRestoreProcess(string $dumpFile): Process
    {
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

        return $process;
    }

    /** {@inheritdoc} */
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

    /** {@inheritdoc} */
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

    /** {@inheritdoc} */
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
