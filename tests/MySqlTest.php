<?php

namespace Iphis\DbRestorer\Test;

use PHPUnit\Framework\TestCase;
use Iphis\DbRestorer\Databases\MySql;
use Iphis\DbRestorer\Exceptions\CannotSetParameter;
use Iphis\DbRestorer\Exceptions\CannotStartRestore;

class MySqlTest extends TestCase
{
    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(MySql::class, MySql::create());
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set()
    {
        $this->expectException(CannotStartRestore::class);

        MySql::create()->restoreFromFile('test.sql');
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_comments()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontSkipComments()
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_extended_insterts()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontUseExtendedInserts()
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --skip-extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestoreBinaryPath('/custom/directory')
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'/custom/directory/mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_extending_inserts()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontUseExtendedInserts()
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --skip-extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_using_single_transaction()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useSingleTransaction()
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --single-transaction --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_a_custom_socket()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket(1234)
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --socket=1234 --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_array()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables(['tb1', 'tb2', 'tb3'])
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname tb1 tb2 tb3', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_string()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables('tb1 tb2 tb3')
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname tb1 tb2 tb3', $dumpCommand);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_exclude_tables_after_setting_tables()
    {
        $this->expectException(CannotSetParameter::class);

        MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->onlyTables('tb1 tb2 tb3')
            ->excludeTables('tb4 tb5 tb6');
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_array()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables(['tb1', 'tb2', 'tb3'])
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert '.
            '--ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_string()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1, tb2, tb3')
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert '.
            '--ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_tables_after_setting_esclude_tables()
    {
        $this->expectException(CannotSetParameter::class);

        MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1 tb2 tb3')
            ->includeTables('tb4 tb5 tb6');
    }

    /** @test */
    public function it_can_generate_the_contents_of_a_credentials_file()
    {
        $credentialsFileContent = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setHost('hostname')
            ->setSocket(1234)
            ->getContentsOfCredentialsFile();

        $this->assertSame(
            '[client]'.PHP_EOL."user = 'username'".PHP_EOL."password = 'password'".PHP_EOL."host = 'hostname'".PHP_EOL."port = '3306'",
            $credentialsFileContent);
    }

    /** @test */
    public function it_can_get_the_name_of_the_db()
    {
        $dbName = 'testName';

        $dbDumper = MySql::create()->setDbName($dbName);

        $this->assertEquals($dbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_add_extra_options()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--extra-option')
            ->addExtraOption('--another-extra-option="value"')
            ->getRestoreCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --extra-option --another-extra-option="value" --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_get_the_host()
    {
        $dumper = MySql::create()->setHost('myHost');

        $this->assertEquals('myHost', $dumper->getHost());
    }
}
