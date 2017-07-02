# Restore the contents of a database (wich was Dumped with Spatie\DbDumper)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/iphis/db-restorer.svg?style=flat-square)](https://packagist.org/packages/iphis/db-restorer)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/iphis/db-restorer/master.svg?style=flat-square)](https://travis-ci.org/iphis/db-restorer)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/bd8dcd6b-19db-4d65-9cdd-3b6ecb2626b1.svg?style=flat-square)](https://insight.sensiolabs.com/projects/bd8dcd6b-19db-4d65-9cdd-3b6ecb2626b1)
[![Quality Score](https://img.shields.io/scrutinizer/g/iphis/db-restorer.svg?style=flat-square)](https://scrutinizer-ci.com/g/iphis/db-restorer)
[![StyleCI](https://styleci.io/repos/49829051/shield?branch=master)](https://styleci.io/repos/49829051)
[![Total Downloads](https://img.shields.io/packagist/dt/iphis/db-restorer.svg?style=flat-square)](https://packagist.org/packages/iphis/db-restorer)

This repo contains an easy to use class to restore a database using PHP. Currently MySQL, PostgreSQL, SQLite and MongoDB are supported. Behind
the scenes `mysqlrestore`, `pg_restore`, `sqlite3` and `mongorestore` are used.

Here's are simple examples of how to restore a database from dump with different drivers:

**MySQL**
```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

**PostgreSQL**

```php
Iphis\DbRestorer\Databases\PostgreSql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

**SQLite**

```php
Iphis\DbRestorer\Databases\Sqlite::create()
    ->setDbName($pathToDatabaseFile)
    ->restoreFromFile('dump.sql');
```

**MongoDB**

```php
Iphis\DbRestorer\Databases\MongoDb::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->enableCompression()
    ->restoreFromFile('dump.gz');
```

## Requirements
For dumping MySQL-db's `mysqlrestore` should be installed.

For dumping PostgreSQL-db's `pg_restore` should be installed.

For dumping SQLite-db's `sqlite3` should be installed.

For dumping MongoDB-db's `mongorestore` should be installed.

## Installation

You can install the package via composer:
``` bash
$ composer require iphis/db-restorer
```

## Usage

This is the simplest way to restore a dump of a MySql db:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

If you're working with PostgreSQL just use that restorer, most methods are available on both the MySql. and PostgreSql-restorer.

```php
Iphis\DbRestorer\Databases\PostgreSql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

If the `mysqlrestore` (or `pg_restore`) binary is installed in a non default location you can let the package know by using the`setRestoreBinaryPath()`-function:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setRestoreBinaryPath('/custom/location')
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

### Restore only specific tables

Using an array:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->onlyTables(['table1', 'table2', 'table3'])
    ->restoreFromFile('dump.sql');
```
Using a string:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->onlyTables('table1, table2, table3')
    ->restoreFromFile('dump.sql');
```



### Adding extra options
If you want to add an arbitrary option to the restore command you can use `addOption`

```php
$dumpCommand = MySql::create()
    ->setDbName('dbname')
    ->setUserName('username')
    ->setPassword('password')
    ->addExtraOption('--xml')
    ->getRestoreCommand('dump.sql', 'credentials.txt');
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits
This is heavily inspired by the work of [Freek Van der Herten](https://github.com/freekmurze) in Spatie\DbDumper
- [Tobias Knipping](https://github.com/to-kn)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
