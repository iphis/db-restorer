<?php

namespace Iphis\DbRestorer\Test;

use Iphis\DbRestorer\Databases\PostgreSql;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;
use PHPUnit\Framework\TestCase;

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
    public function it_can_generate_a_restore_command()
    {
        $restoreCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getRestoreCommand('dump.sql');

        $this->assertSame('\'pg_restore\' -U username -h localhost -p 5432 -d dbname dump.sql', $restoreCommand);
    }

    /** @test */
    public function it_can_generate_a_restore_command_with_a_custom_port()
    {
        $restoreCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setPort(1234)
            ->getRestoreCommand('dump.sql');

        $this->assertSame('\'pg_restore\' -U username -h localhost -p 1234 -d dbname dump.sql', $restoreCommand);
    }

    /** @test */
    public function it_can_generate_a_restore_command_with_custom_binary_path()
    {
        $restoreCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestoreBinaryPath('/custom/directory')
            ->getRestoreCommand('dump.sql');

        $this->assertSame(
            '\'/custom/directory/pg_restore\' -U username -h localhost -p 5432 -d dbname dump.sql',
            $restoreCommand
        );
    }

    /** @test */
    public function it_can_generate_a_restore_command_with_a_custom_socket()
    {
        $restoreCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket('/var/socket.1234')
            ->getRestoreCommand('dump.sql');

        $this->assertEquals(
            '\'pg_restore\' -U username -h /var/socket.1234 -p 5432 -d dbname dump.sql',
            $restoreCommand
        );
    }

    /** @test */
    public function it_can_generate_a_restore_command_for_specific_tables_as_array()
    {
        $restoreCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables(['tb1', 'tb2', 'tb3'])
            ->getRestoreCommand('dump.sql');

        $this->assertSame(
            '\'pg_restore\' -U username -h localhost -p 5432 -d dbname -t tb1 -t tb2 -t tb3 dump.sql',
            $restoreCommand
        );
    }

    /** @test */
    public function it_can_generate_a_restore_command_for_specific_tables_as_string()
    {
        $restoreCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables('tb1, tb2, tb3')
            ->getRestoreCommand('dump.sql');

        $this->assertSame(
            '\'pg_restore\' -U username -h localhost -p 5432 -d dbname -t tb1 -t tb2 -t tb3 dump.sql',
            $restoreCommand
        );
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
        $restoreCommand = PostgreSql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('-something-else')
            ->getRestoreCommand('dump.sql');

        $this->assertSame(
            '\'pg_restore\' -U username -h localhost -p 5432 -something-else -d dbname dump.sql',
            $restoreCommand
        );
    }

    /** @test */
    public function it_can_get_the_host()
    {
        $dumper = PostgreSql::create()->setHost('myHost');

        $this->assertEquals('myHost', $dumper->getHost());
    }
}
