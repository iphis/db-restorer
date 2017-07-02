<?php

namespace Iphis\DbRestorer\Test;

use PHPUnit\Framework\TestCase;
use Iphis\DbRestorer\Databases\MongoDb;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;

class MongoDbTest extends TestCase
{
    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(MongoDb::class, MongoDb::create());
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set()
    {
        $this->expectException(CannotStartRestore::class);

        MongoDb::create()->restoreFromFile('test.gz');
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->getRestoreCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname'
            .' --archive=dbname.gz --host localhost --port 27017', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_compression_enabled()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->enableCompression()
            ->getRestoreCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname'
            .' --archive=dbname.gz --host localhost --port 27017 --gzip', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_username_and_password()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getRestoreCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname --archive=dbname.gz'
            .' --username username --password password --host localhost --port 27017', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_command_with_custom_host_and_port()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setHost('mongodb.test.com')
            ->setPort(27018)
            ->getRestoreCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname --archive=dbname.gz'
         .' --host mongodb.test.com --port 27018', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_backup_command_for_a_single_collection()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setCollection('mycollection')
            ->getRestoreCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname --archive=dbname.gz'
            .' --host localhost --port 27017 --collection mycollection', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setRestoreBinaryPath('/custom/directory')
            ->getRestoreCommand('dbname.gz');

        $this->assertSame('\'/custom/directory/mongodump\' --db dbname --archive=dbname.gz'
            .' --host localhost --port 27017', $dumpCommand);
    }
}
