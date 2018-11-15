# Proton Database Inconsistency checker

## Build

```
composer install
```

```
chmod -R 666 log
```

## Usage

Both script should be run in cronjob or daemon

`checkBlobs.php` checks BlobStorage table looking for wrong NumReferences values and orphan blobs.
If you want to run few simultaneous instances, checking part of database, run it with offset and limit params for sql, i.e.

`php checkBlobs.php 0 1000` - to check 1000 first rows

`php checkBlobs.php 1000 1000` - to check rows form 1000 to 2000

etc.


`checkRefs.php` checks tables (defined in config/tables.ini) for wrong BlobStorage References.

For few simultaneous works, run it with parameter being name of table, i.e.
`php checkRefs.php MessageData`

`php checkRefs.php Attachment`

etc.

## Alerts

For email/SQS/whatever alerts use appropriate Logger implementing [PSR-3 interface](https://www.php-fig.org/psr/psr-3/)
For now, it simply logs to alert.log file.

## Tests

```
./vendor/bin/phpunit
```
