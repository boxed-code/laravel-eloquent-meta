#Eloquent Meta#

Eloquent meta provides an easy way to implement schemaless meta data stores for `Eloquent` models.


[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/boxedcode/laravel-eloquent-meta/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/boxedcode/laravel-eloquent-meta/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/boxedcode/laravel-eloquent-meta/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/boxedcode/laravel-eloquent-meta/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/boxedcode/laravel-eloquent-meta/badges/build.png?b=master)](https://scrutinizer-ci.com/g/boxedcode/laravel-eloquent-meta/build-status/master)

##Installation##

Add the package via composer:

```php
composer require boxedcode/laravel-eloquent-meta
```

then add the following
line to the `providers` key within your `config/app.php` file:

```php
BoxedCode\Eloquent\Meta\MetaServiceProvider::class
```
You can then create a meta table migration using the artisan command:

```php
php artisan make:meta-migration
```
and then call migrate

```php
php artisan migrate
```

##Basic Usage##

There are two main methods of enabling a meta store on your models:

###Using the Metable trait###
The `Metable` trait adds the basic meta store relation to your model so that it can be accessed like:

```php
class MyModel extends Model
{
    use Metable;
}

...

$model = new MyModel();

// Access via magic accessors on the meta collection.

$model->meta->foo = 'bar';

echo $model->meta->foo; // prints 'bar'

// Access via the collection

$item = $model->meta->whereKey('foo')->first();

echo $model->meta->foo; // prints 'bar'

```

###Using the FluentMeta trait###
The `FluentMeta` trait enables meta access on the model directly like:

```php

use BoxedCode\Eloquent\Meta\FluentMeta;
use Illuminate\Database\Eloquent\Model;

class MyModel extends Model
{
    use FluentMeta;
}

...

$model = new MyModel();

// Access via magic accessors on the model.

$model->foo = 'bar';

echo $model->foo; // prints 'bar'

// Access via the collection

$item = $model->meta->whereKey('foo')->first();

echo $model->meta->foo; // prints 'bar'

```

##License##
See the attached license file.