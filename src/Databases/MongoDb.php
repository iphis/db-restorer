<?php

namespace Iphis\DbRestorer\Databases;

use Iphis\DbRestorer\DbRestorer;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;
use Symfony\Component\Process\Process;

class MongoDb extends DbRestorer
{
    protected $port = 27017;

    /** @var null|string */
    protected $collection = null;

    /** @var bool */
    protected $enableCompression = false;

    /**
     * Verifies if the dbname and host options are set.
     *
     * @throws \Iphis\DbRestorer\Exceptions\CannotStartRestore
     *
     * @return void
     */
    protected function guardAgainstIncompleteCredentials()
    {
        foreach (array('dbName', 'host') as $requiredProperty) {
            if (strlen($this->$requiredProperty) === 0) {
                throw CannotStartRestore::emptyParameter($requiredProperty);
            }
        }
    }

    /**
     * @param string $collection
     *
     * @return \Iphis\DbRestorer\Databases\MongoDb
     */
    public function setCollection(string $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return \Iphis\DbRestorer\Databases\MongoDb
     */
    public function enableCompression()
    {
        $this->enableCompression = true;

        return $this;
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

    /**
     * @param string $dumpFile
     * @return string
     */
    public function getRestoreCommand(string $dumpFile): string
    {
        $command = array(
            "'{$this->restoreBinaryPath}mongodump'",
            "--db {$this->dbName}",
            "--archive=$dumpFile",
        );

        if (isset($this->userName)) {
            $command[] = "--username {$this->userName}";
        }

        if (isset($this->password)) {
            $command[] = "--password {$this->password}";
        }

        if (isset($this->host)) {
            $command[] = "--host {$this->host}";
        }

        if (isset($this->port)) {
            $command[] = "--port {$this->port}";
        }

        if (isset($this->collection)) {
            $command[] = "--collection {$this->collection}";
        }

        if ($this->enableCompression) {
            $command[] = '--gzip';
        }

        return implode(' ', $command);
    }
}
