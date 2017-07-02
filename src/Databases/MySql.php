<?php

namespace Iphis\DbRestorer\Databases;

use Iphis\DbRestorer\DbRestorer;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;
use Symfony\Component\Process\Process;

class MySql extends DbRestorer
{
    /** @var bool */
    protected $skipComments = true;

    /** @var bool */
    protected $useExtendedInserts = true;

    /** @var bool */
    protected $useSingleTransaction = false;

    /** @var string */
    protected $defaultCharacterSet = '';

    public function __construct()
    {
        $this->port = 3306;
    }

    /**
     * @return $this
     */
    public function skipComments()
    {
        $this->skipComments = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontSkipComments()
    {
        $this->skipComments = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function useExtendedInserts()
    {
        $this->useExtendedInserts = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontUseExtendedInserts()
    {
        $this->useExtendedInserts = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function useSingleTransaction()
    {
        $this->useSingleTransaction = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function dontUseSingleTransaction()
    {
        $this->useSingleTransaction = false;

        return $this;
    }

    /**
     * @param string $characterSet
     *
     * @return $this
     */
    public function setDefaultCharacterSet(string $characterSet)
    {
        $this->defaultCharacterSet = $characterSet;

        return $this;
    }

    /**
     * Dump the contents of the database to the given file.
     *
     * @param string $dumpFile
     *
     * @throws \Iphis\DbRestorer\Exceptions\CannotStartRestore
     * @throws \Iphis\DbRestorer\Exceptions\RestoreFailed
     */
    public function restoreFromFile(string $dumpFile)
    {
        $this->guardAgainstIncompleteCredentials();

        $tempFileHandle = tmpfile();
        fwrite($tempFileHandle, $this->getContentsOfCredentialsFile());
        $temporaryCredentialsFile = stream_get_meta_data($tempFileHandle)['uri'];

        $command = $this->getRestoreCommand($dumpFile, $temporaryCredentialsFile);

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
     * @param string $temporaryCredentialsFile
     *
     * @return string
     */
    public function getRestoreCommand(string $dumpFile, string $temporaryCredentialsFile): string
    {
        $quote = $this->determineQuote();

        $command = [
            "{$quote}{$this->restoreBinaryPath}mysqldump{$quote}",
            "--defaults-extra-file=\"{$temporaryCredentialsFile}\"",
        ];

        if ($this->skipComments) {
            $command[] = '--skip-comments';
        }

        $command[] = $this->useExtendedInserts ? '--extended-insert' : '--skip-extended-insert';

        if ($this->useSingleTransaction) {
            $command[] = '--single-transaction';
        }

        if ($this->socket !== '') {
            $command[] = "--socket={$this->socket}";
        }

        foreach ($this->excludeTables as $tableName) {
            $command[] = "--ignore-table={$this->dbName}.{$tableName}";
        }

        if (!empty($this->defaultCharacterSet)) {
            $command[] = '--default-character-set='.$this->defaultCharacterSet;
        }

        foreach ($this->extraOptions as $extraOption) {
            $command[] = $extraOption;
        }

        $command[] = "--result-file=\"{$dumpFile}\"";

        $command[] = "{$this->dbName}";

        if (!empty($this->onlyTables)) {
            $command[] = implode(' ', $this->onlyTables);
        }

        return implode(' ', $command);
    }

    public function getContentsOfCredentialsFile(): string
    {
        $contents = [
            '[client]',
            "user = '{$this->userName}'",
            "password = '{$this->password}'",
            "host = '{$this->host}'",
            "port = '{$this->port}'",
        ];

        return implode(PHP_EOL, $contents);
    }

    protected function guardAgainstIncompleteCredentials()
    {
        foreach (['userName', 'dbName', 'host'] as $requiredProperty) {
            if (strlen($this->$requiredProperty) === 0) {
                throw CannotStartRestore::emptyParameter($requiredProperty);
            }
        }
    }

    protected function determineQuote(): string
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '"' : "'";
    }
}
