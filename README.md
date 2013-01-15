LMongo
==============

LMongo is [MongoDB](http://www.mongodb.org/) service provider for [Laravel 4](http://laravel.com/).

Installation
============

Add `navruzm/lmongo` as a requirement to composer.json:

```json
{
    "require": {
        "navruzm/lmongo": "*"
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
'LMongo'      => 'LMongo\Facades\LMongo',
```

Finally you need to add the MongoDB database configuration to the `config/database.php` file:

```php
'mongodb' => array(

        'default' => array(
            'host'     => '127.0.0.1',
            'port'     => 27017,
            'database' => 'laravel',
        )
    ),
```

Usage
=====
You may get a MongoDB instance by calling the `LMongo::connection` method:

```php
$LMongo = LMongo::connection();
```
This will give you an instance of the default MongoDB server. You may pass the server name to the `connection` method to get a specific server as defined in your mongodb configuration:

```php
$LMongo = LMongo::connection('othermongodbserver');
```
LMongo uses magic method to pass the collection name to the Database class and return MongoCollection instance. Then you can use any of [MongoCollection methods](http://php.net/manual/en/class.mongocollection.php):

```php
$item = $LMongo->collection_name->findOne(array('key', 'value'));

$items = $LMongo->collection_name->find(array('key', 'value'))->limit(5);

$LMongo->collection_name->remove(array('key', 'value'));
```
Get the [MongoDB](http://php.net/manual/en/class.mongodb.php) object:

```php
$mongodb = $LMongo->getMongoDBObject();

$collection_names = $mongodb->getCollectionNames();
```
Get the [MongoClient](http://php.net/manual/en/class.mongoclient.php) object:

```php
$mongo = $LMongo->getMongoClientObject();

$databases = $mongo->listDBs();
```