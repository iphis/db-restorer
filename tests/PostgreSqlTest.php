<?php

namespace Iphis\DbRestorer\Test;

use PHPUnit\Framework\TestCase;
use Iphis\DbRestorer\Databases\PostgreSql;
use Iphis\DbRestorer\Exceptions\CannotSetParameter;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;

class PostgreSqlTest extends TestCase
{
    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(PostgreSql::class, PostgreSql::create());
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set()
    {
        $this->expectException(CannotStartRestore::class);

        PostgreSql::create()->restoreFromFile('test.sql');
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getRestoreCommand('dump.sql');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 5432 --file="dump.sql"', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_using_inserts()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useInserts()
            ->getRestoreCommand('dump.sql');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 5432 --file="dump.sql" --inserts', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_a_custom_port()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setPort(1234)
            ->getRestoreCommand('dump.sql');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 1234 --file="dump.sql"', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestoreBinaryPath('/custom/directory')
            ->getRestoreCommand('dump.sql');

        $this->assertSame('\'/custom/directory/pg_dump\' -U username -h localhost -p 5432 --file="dump.sql"', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_a_custom_socket()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket('/var/socket.1234')
            ->getRestoreCommand('dump.sql');

        $this->assertEquals('\'pg_dump\' -U username -h /var/socket.1234 -p 5432 --file="dump.sql"', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_array()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables(['tb1', 'tb2', 'tb3'])
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 5432 --file="dump.sql" -t tb1 -t tb2 -t tb3', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_string()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables('tb1, tb2, tb3')
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 5432 --file="dump.sql" -t tb1 -t tb2 -t tb3', $dumpCommand);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_exclude_tables_after_setting_tables()
    {
        $this->expectException(CannotSetParameter::class);

        PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables('tb1, tb2, tb3')
            ->excludeTables('tb4, tb5, tb6');
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_array()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables(['tb1', 'tb2', 'tb3'])
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 5432 --file="dump.sql" -T tb1 -T tb2 -T tb3', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_string()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1, tb2, tb3')
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 5432 --file="dump.sql" -T tb1 -T tb2 -T tb3', $dumpCommand);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_tables_after_setting_exclude_tables()
    {
        $this->expectException(CannotSetParameter::class);

        PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1, tb2, tb3')
            ->includeTables('tb4, tb5, tb6');
    }

    /** @test */
    public function it_can_generate_the_contents_of_a_credentials_file()
    {
        $credentialsFileContent = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setHost('hostname')
            ->setPort(5432)
            ->getContentsOfCredentialsFile();

        $this->assertSame('hostname:5432:dbname:username:password', $credentialsFileContent);
    }

    /** @test */
    public function it_can_get_the_name_of_the_db()
    {
        $dbName = 'testName';

        $dbDumper = PostgreSql::create()->setDbName($dbName);

        $this->assertEquals($dbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_add_an_extra_option()
    {
        $dumpCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('-something-else')
            ->getRestoreCommand('dump.sql');

        $this->assertSame('\'pg_dump\' -U username -h localhost -p 5432 --file="dump.sql" -something-else', $dumpCommand);
    }

    /** @test */
    public function it_can_get_the_host()
    {
        $dumper = PostgreSql::create()->setHost('myHost');

        $this->assertEquals('myHost', $dumper->getHost());
    }
}
