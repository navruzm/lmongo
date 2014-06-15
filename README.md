LMongo [![Build Status](https://secure.travis-ci.org/navruzm/lmongo.png?branch=master)](https://travis-ci.org/navruzm/lmongo)
======

LMongo is [MongoDB](http://www.mongodb.org/) package for [Laravel 4](http://laravel.com/).


### Installation

Add `navruzm/lmongo` as a requirement to composer.json:

```json
{
    "require": {
        "navruzm/lmongo": "*@dev"
    }
}
```
And then run `composer update`

Once Composer has installed or updated your packages you need to register LMongo. Open up `app/config/app.php` and find the `providers` key and add:

```php
'LMongo\LMongoServiceProvider'
```

Then find the `aliases` key and add following line to the array:

```php
'LMongo'          => 'LMongo\Facades\LMongo',
'EloquentMongo'   => 'LMongo\Eloquent\Model',
```

Finally you need to publish a configuration file by running the following Artisan command.

```terminal
$ php artisan config:publish navruzm/lmongo
```
This will copy the default configuration file to app/config/packages/navruzm/lmongo/config.php

### Documentation

[View the official documentation](http://navruzm.github.io/lmongo/)