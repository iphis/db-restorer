<?php

namespace Iphis\DbRestorer;

use Iphis\DbRestorer\Exceptions\CannotStartRestore;
use Iphis\DbRestorer\Exceptions\RestoreFailed;
use Symfony\Component\Process\Process;

abstract class DbRestorer
{
    /** @var string */
    protected $dbName;

    /** @var string */
    protected $userName;

    /** @var string */
    protected $password;

    /** @var string */
    protected $host = 'localhost';

    /** @var int */
    protected $port = 5432;

    /** @var string */
    protected $socket = '';

    /** @var int */
    protected $timeout = 0;

    /** @var string */
    protected $restoreBinaryPath = '';

    /** @var array */
    protected $onlyTables = array();

    /** @var array */
    protected $extraOptions = array();

    public static function create()
    {
        return new static();
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @param string $dbName
     *
     * @return $this
     */
    public function setDbName(string $dbName)
    {
        $this->dbName = $dbName;

        return $this;
    }

    /**
     * @param string $userName
     *
     * @return $this
     */
    public function setUserName(string $userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort(int $port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $socket
     *
     * @return $this
     */
    public function setSocket(string $socket)
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param string $restoreBinaryPath
     *
     * @return $this
     */
    public function setRestoreBinaryPath(string $restoreBinaryPath)
    {
        if ($restoreBinaryPath !== '' && substr($restoreBinaryPath, -1) !== '/') {
            $restoreBinaryPath .= '/';
        }

        $this->restoreBinaryPath = $restoreBinaryPath;

        return $this;
    }

    /**
     * @param string|array $onlyTables
     *
     * @return $this
     */
    public function onlyTables($onlyTables)
    {
        if (!is_array($onlyTables)) {
            $onlyTables = explode(', ', $onlyTables);
        }

        $this->onlyTables = $onlyTables;

        return $this;
    }

    /**
     * @param string $extraOption
     *
     * @return $this
     */
    public function addExtraOption(string $extraOption)
    {
        if (!empty($extraOption)) {
            $this->extraOptions[] = $extraOption;
        }

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

        $process = $this->getRestoreProcess($dumpFile);

        $process->run();

        $this->checkIfRestoreWasSuccessFul($process, $dumpFile);
    }

    /**
     * @throws CannotStartRestore
     *
     * @return void
     */
    abstract protected function guardAgainstIncompleteCredentials();

    /**
     * Create the Process for handling the Restore (if necessary with ENV-Variables etc).
     *
     * @param string $dumpFile
     *
     * @return Process
     */
    abstract protected function getRestoreProcess(string $dumpFile): Process;

    /**
     * @param Process $process
     * @param string  $outputFile
     *
     * @throws RestoreFailed
     */
    protected function checkIfRestoreWasSuccessFul(Process $process, string $outputFile)
    {
        if (!$process->isSuccessful()) {
            throw RestoreFailed::processDidNotEndSuccessfully($process);
        }

        if (!file_exists($outputFile)) {
            throw RestoreFailed::restorefileWasNotReadable();
        }

        if (filesize($outputFile) === 0) {
            throw RestoreFailed::restorefileWasEmpty();
        }
    }
}
