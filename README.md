# Dump the contents of a database

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/db-dumper.svg?style=flat-square)](https://packagist.org/packages/spatie/db-dumper)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/db-dumper/master.svg?style=flat-square)](https://travis-ci.org/spatie/db-dumper)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/bd8dcd6b-19db-4d65-9cdd-3b6ecb2626b1.svg?style=flat-square)](https://insight.sensiolabs.com/projects/bd8dcd6b-19db-4d65-9cdd-3b6ecb2626b1)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/db-dumper.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/db-dumper)
[![StyleCI](https://styleci.io/repos/49829051/shield?branch=master)](https://styleci.io/repos/49829051)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/db-dumper.svg?style=flat-square)](https://packagist.org/packages/spatie/db-dumper)

This repo contains an easy to use class to dump a database using PHP. Currently MySQL, PostgreSQL, SQLite and MongoDB are supported. Behind
the scenes `mysqldump`, `pg_dump`, `sqlite3` and `mongodump` are used.

Here's are simple examples of how to create a database dump with different drivers:

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

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Requirements
For dumping MySQL-db's `mysqldump` should be installed.

For dumping PostgreSQL-db's `pg_dump` should be installed.

For dumping SQLite-db's `sqlite3` should be installed.

For dumping MongoDB-db's `mongodump` should be installed.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

All postcards are published [on our website](https://spatie.be/en/opensource/postcards).

## Installation

You can install the package via composer:
``` bash
$ composer require spatie/db-dumper
```

## Usage

This is the simplest way to create a dump of a MySql db:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

If you're working with PostgreSQL just use that dumper, most methods are available on both the MySql. and PostgreSql-dumper.

```php
Iphis\DbRestorer\Databases\PostgreSql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

If the `mysqldump` (or `pg_dump`) binary is installed in a non default location you can let the package know by using the`setRestoreBinaryPath()`-function:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setRestoreBinaryPath('/custom/location')
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->restoreFromFile('dump.sql');
```

### Dump specific tables

Using an array:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->includeTables(['table1', 'table2', 'table3'])
    ->restoreFromFile('dump.sql');
```
Using a string:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->includeTables('table1, table2, table3')
    ->restoreFromFile('dump.sql');
```

### Excluding tables from the dump

Using an array:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->excludeTables(['table1', 'table2', 'table3'])
    ->restoreFromFile('dump.sql');
```
Using a string:

```php
Iphis\DbRestorer\Databases\MySql::create()
    ->setDbName($databaseName)
    ->setUserName($userName)
    ->setPassword($password)
    ->excludeTables('table1, table2, table3')
    ->restoreFromFile('dump.sql');
```



### Adding extra options
If you want to add an arbitrary option to the dump command you can use `addOption`

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

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

Initial PostgreSQL support was contributed by [Adriano Machado](https://github.com/ammachado). SQlite support was contributed by [Peter Matseykanets](https://twitter.com/pmatseykanets).

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
