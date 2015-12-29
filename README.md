# Data Manager
[![Latest Version](https://img.shields.io/github/release/chrismichaels84/data-manager.svg?style=flat-square)](https://github.com/chrismichaels84/data-manager/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/chrismichaels84/data-manager/master.svg?style=flat-square)](https://travis-ci.org/chrismichaels84/data-manager)
[![Coverage Status](https://coveralls.io/repos/chrismichaels84/data-manager/badge.svg?branch=master)](https://coveralls.io/r/chrismichaels84/data-manager?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chrismichaels84/data-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chrismichaels84/data-manager/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/michaels/data-manager.svg?style=flat-square)](https://packagist.org/packages/michaels/data-manager)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3ef3b9a4-6078-4ddf-bf0d-c84dac87f37a/big.png)](https://insight.sensiolabs.com/projects/3ef3b9a4-6078-4ddf-bf0d-c84dac87f37a)

Simple data manager for nested data, dot notation access, extendability, and container interoperability.

This project began as a three part tutorial series which can be found at http://phoenixlabstech.org/2015/04/17/building-a-data-manager/

## Goals
  * Light weight
  * Fluent, simple, clear API
  * Manage any data type (closure, object, primitives, etc.)
  * Manage nested data via dot-notation
  * Manage nested data via magic methods ($manager->one()->two()->three)
  * Be composable - integrate into current containers via traits
  * Be extensible.
  * Allow for protected data (immutable)
  * Test coverage, PSR compliant, [container interoperability](https://github.com/container-interop/container-interop), and best practices

## Extras
On top of being a powerful data-manager, there are traits that add features.
They are not documented here, but see other readmes for:
  * [IoC Container](ioc.md): Use Manager as a simple but powerful Dependency Injection Container. Includes
    * Resolving dependencies from classnames, closures, eager loading, and more,
    * Creating singletons,
    * Configuring dependencies for dependencies,
    * Fallbacks, preparing objects, and more.
  * Use Manager as a configuration bank, complete with defaults.
  * Load configuration files (php, yaml, json, xml, and custom)
  * [Collections](collections.md) for extra array-helper methods (based on [Arrayzy](https://github.com/bocharsky-bw/Arrayzy))
    
## Install
Via Composer
``` bash
$ composer require michaels/data-manager
```

## Getting Started
Manager does exactly what you would expect: it *manages* complex items such as config data, arrays, and closures.
The best way to get started is simply instantiate `Michaels\Manager\Manager`

```php
$manager = new Michaels\Manager\Manager([
    'some' => [
        'starting' => [
            'data' => 'here (optional)'
        ]
    ]
]);
// Note, you may initialize Manager with an array or any instance of Traversable (like Manager itself)

/* Basic Usage. All works with dot notation as well */
$manager->add('name', 'value');
$manager->add('some.nested.data', 3); // Use dot notation for namespacing or nesting
$manager->get('name'); // 'value'
$manager->get('doesntexist', 'fallback'); // 'fallback'
$manager->get('doesntexist') // throws an ItemNotFoundException with no fallback
$manager->getIfHas('doesntexist') // returns a NoItemFoundMessage instead of a script-stopping exception
$manager->getAll(); // returns array of all items
$manager->all(); // returns array of all items
$manager->exists('name'); // true
$manager->exists('some.starting.data'); // true
$manager->exists('nope'); // false
$manager->has('something'); // alias of exist
$manager->set('name', 'new-value'); // updates item
$manager->remove('some.starting.data');
$manager->isEmpty(); // true or false
$manager->toJson(); // returns json of all items
echo $manager; // returns json string of all items
$manager->reset($array); // rebuild with new items
$manager->clear(); // empty the manager

/* You can also use $manager as an array or in loops */
$manager['some']['starting']['data']; // 'here (optional)'
//etc

foreach ($manager as $item => $value) {
    // do whatever your heart desires
}

/* You may also push elements onto an array */
$manager->set('a.b', []);
$manager->push('a.b', 'c', 'd', 'e');
$manager->get('a.b'); // ['c', 'd', 'e']

/* Finally, you may manage values using magic methods */
$manager->some()->starting()->data; // 'here (optional)'
$manager->some()->item = 'item'; // sets some.item = 'item'
$manager->some()->item()->drop(); // deletes some.item

// Note that levels are called as a method with no params. The data is then called, updated, or set as a property.
```

## Protecting Data
You can also guard any item or nest from being changed. Simply,
```php
$manager->protect('some.data'); //now some.data and everything under it cannot be altered
$manager->set('some.data.here', 'new-value'); // throws an exception
```

## Merging Defaults Into Current Dataset
When using Manager to store configuration data, it is important to be able to set defaults.
You can merge an array of defaults into manager via `loadDefaults(array $defaults)`

Imagine your configuration starts like
```php
$manager = new Manager([
    'name' => 'My Awesome App',
    'site' => [
        'url' => 'https://youwishyouwerethiscool.com/',
        'protocol' => 'https',
    ]
]);
```

But your app needs `site.assets` for the assets directory. Simply
```php
$manager->loadDefaults([
    'site' => [
        'url' => 'http://the_default_url.com/',
        'assets' => '/assets',
    ],
    'database' => "mysql"
]);
```

And now, your configuration looks like
```php
    'name' => 'My Awesome App',
    'site' => [
        'url' => 'https://youwishyouwerethiscool.com/'
        'protocol' => "https",
        'assets' => '/assets'
    ],
    'database' => "mysql"
```

A couple of things to keep in mind:
  * This works recursively and as far down as you want.
  * If any value is set before loading defaults, that value is preserved
  * If a starting value is set to an array (`one.two = []`) and a default lives beneath (`one.two.three = default`), then the default **will** be set.
  * On the other hand, if the value exists and is **not** an array, the default will be ignored. 
  (`one.two = 'something'`) In this case, there is no `one.two.three`, even after loading defaults.

## Using Manager Traits
If you have your own container objects and want to add Manager functionality to them, you may import traits into your class.

There are 3 Traits that make up Manager:
  1. `ManagesItemsTrait` fulfills `ManagesItemsInterface` and adds most functionality. Look at the interface for full list.
  2. `ArrayableTrait` makes the class usable as an array (`$manager['some']['data']`) or in loops and such
  3. `ChainsNestedItemsTrait` allows you to use fluent properties to manage data (`$manager->one()->two()->three = 'three`)

And 3 extra Traits to give additional functionality:
  1. `CollectionTrait` returns collections with all sorts of [array helpers](https://github.com/bocharsky-bw/Arrayzy)
  2. `ManagesIocTrait` turns Manager into a simple, but complete IoC or Dependency Injection manager
  3. `LoadsFilesTrait` allows Manager to load data from config files.
  
Each of these traits is documented below. To include the functionality simply `use` the trait.
it is possible to use ALL traits together in one class, though I don't know why you would need to.

*NOTE THAT* all traits depend on ManagesItemsTrait. If you try to use ChainsNestedItemsTrait or ArrayableTrait without ManagesItemsTrait, you will get all sorts of errors.

```php
/* An example for UberManager */
class MyContainer {
    use Michaels\Manager\Traits\ManagesItemsTrait;
    use Michaels\Manager\Traits\ChainsNestedItemsTrait;
    use Michaels\Manager\Traits\ArrayableTrait;
    use Michaels\Manager\Traits\CollectionTrait; 
    use Michaels\Manager\Traits\LoadsFilesTrait;
    use Michaels\Manager\Traits\ManagesIocTrait;

    // Your stuff here. And you may override anything you like.
    // Remember to add a constructor if you want :)
}
```

If you do use a trait, and want to initialize your class at construction, use the `initManager()` method.

```php
class MyClass
{
    use ManagesItemsTrait;
    
    public function __construct($beginningItems)
    {
        $this->initManager($beginningItems);
    }
}

```

initManager() is used so it doesn't conflict with user-defined init() methods.

#### Two important notes
  1. Using `ManagesItemsTrait` does not implement ArrayAccess, so you can't use your manager as an array (`$manager['one']`). Use `ArrayableTrait` for that.
  2. `ManagesItemsTrait` no longer includes a constructor. It is just best not to include constructors in traits. It is recommended (though not necessary) to use a constructor in your class:
```php
public function __construct($items = [])
{
    $this->initManager($items);
}
```

You may also use the **tests** under `tests/traits` to test your integrated functionality. You may have to grab these through cloning the repo. composer usually won't include tests in your `require`
## Additional Features

### Collections
Right now, you may choose to return a `MutableArray` which comes with a lot of helpers.
Simply include `Michaels\Manager\Traits\CollectionTrait` into your class. By default, `get()` and `getAll()` will return collections.
These collections behave *identically* to arrays, but come with extra methods. See [Arrayzy](https://github.com/bocharsky-bw/Arrayzy) for a full api.

It is possible to chain things for brevity:
```php
$value = $manager->get('one.two.three')->walk(function(){});
```
In this case, `walk()` is part of the collection and will apply the callback to each value, then return the updated contents.

You may also disable this by `$manager->useCollections = false`

### Loading Files
Manager also gives you the ability to load file data into the Manager. 
A good use case for this is loading configuration data out of different configuration files.

  1. `use LoadsFilesTrait`
  2. Use `$manager->loadFiles($files)` to load a group of files. 
  
The `$files` argument can be a `FileBag` object or an array of:
  1. `\SplFileInfo` objects
  2. Valid paths (`__DIR__.'/some/path/here.json'`)
  
For more powerful uses (like loading entire directories or advanced filesystem searches), you may also us [Symfony's Finder Component](https://github.com/symfony/Finder).
```php
$finder = new Finder();
$finder->files()->in(__DIR__);
$manager->loadFiles($finder);
```

The data will be added to manager under the filename. So, if you load `config.json` you could `$manager->get('config.item')`;
It is possible to set a custom namespace for each file:
```php
$manager->loadFiles([
  __DIR__.'/path/to/filename.json', // auto namespaces under filename
  [new \SplFileInfo('path/to/file.yaml'), 'namespace'], // will namespace like ->get('namespace.item')
  [__DIR__.'/path/to/another.php, 'another'] // will namespace like ->get('another.item')
]);
```

#### Adding a Decoder
Manager supports Yaml, Json and PHP file formats out of the box. To use the Yaml decoder, you must require the Symfony Yaml Component in your composer.json file.

If you have special files not covered by the decoders available in Manager, you can also create your own and add it to the Manager prior to decoding the files.
Simply implement the `Contracts\DecoderInterface` and return an array of data (not namespaced).
For an example custom decoder, have a look at the `\CustomXmlDecoder` class in the `/Decoders` directory. 

Once you've created your custom decoder, you can add it with the `$manager->addDecoder()` method *before* loading any file data. 

### Managing Dependencies
Please see [ioc.md](ioc.md) for details.

## Some Advanced Features
By default, Manager stores all the items in an `$items` property. 
If you are using the `ManagesItemsTrait` and want to use an internal property besides `$items` to avoid collisions, you have two options:

  1. Use `$manager->setItemsName($nameOfProperty)` either in your constructor or before you add anything
  2. Set the `$dataItemsName` property to a string of the new property name. Then be sure to call `initManager()` in your constructor.

## Exceptions
If you try to `get()` an item that doesn't exist, and there is no fallback, an `ItemNotFoundException` will be thrown.

If you do not want an exception, use `getIfHas($alias)` which will return a `NoItemFoundMessage` object, or use a fallback value `get($item, $fallback)`.

If you try to nest under an existing value that is not an array, an `NestingUnderNonArrayException` will be thrown.
```php
$manager = new Manager(['one' => 1]);
$manager->add("one.two", "two-value"); // exception
```

If you try to alter a protected item, a `ModifyingProtectedItemException` will be thrown.

See /exceptions for more

## Upgrading
Note that between 0.8.2 and 0.8.3, the `__constuct()` method was removed from `ManagesItemsTrait`. If you are using that trait directly, you should implement your own. 

See `Michaels\Manager\Manager` for an example.

## Interoperability
Data Manager is [PSR compliant](http://www.php-fig.org/) and [Container Interoperability](https://github.com/container-interop/container-interop) compliant. Any oversights, please let me know.

## Testing
We try for at least 80% test coverage.
``` bash
$ phpunit
```

You may also use the **tests** under `tests/traits` to test your integrated functionality. You may have to grab these through cloning the repo. composer usually won't include tests in your `require`

## Contributing
Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email chrismichaels84@gmail.com instead of using the issue tracker.

## Credits
- [Michael Wilson](https://github.com/chrismichaels84)
- [Scott](https://github.com/smolinari)
- Open an issue to join in!

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
