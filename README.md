# ![Inroute](res/logo.png "Inroute")

[![Packagist Version](https://img.shields.io/packagist/v/inroutephp/inroute.svg?style=flat-square)](https://packagist.org/packages/inroutephp/inroute)
[![Build Status](https://img.shields.io/travis/inroutephp/inroute/master.svg?style=flat-square)](https://travis-ci.org/inroutephp/inroute)
[![Quality Score](https://img.shields.io/scrutinizer/g/inroutephp/inroute.svg?style=flat-square)](https://scrutinizer-ci.com/g/inroutephp/inroute)

Generate web router and dispatcher from docblock annotations.

Inroute is a code generator. It scans your source tree for classes that
implements the [Routable](src/Runtime/Routable.php) interface. And
it sets up all routes based on @route annotations. From this it generates a
router and a dispatcher. When done all you have to do is to bootstrap your
application auto-loading and dispatch.

```php
$router = require 'router.php';
echo $router->dispatch($url, $_SERVER);
```

Alter the behaviour of the application
--------------------------------------
* [SettingsInterface](src/Settings/SettingsInterface.php)
* [Instantiator](src/Runtime/Instantiator.php)


Plugins
-------
* [PluginInterface](src/Plugin/PluginInterface.php)
* [PreFilterInterface](src/Runtime/PreFilterInterface.php)
* [PostFilterInterface](src/Runtime/PostFilterInterface.php)


Annotations
-----------

### @route

Methods that should be routable use the @route tag. The syntax is

    @route METHOD </path>

Where METHOD is the desired HTTP-method and path is the route path. You can add
route parameters like this

    @route GET </path/{:name}>

Route multiple HTTP-methods to the same method by listing methods separated
by commas (but without spaces!).

    @route POST,PUT </path/{:name}>

And acces the parameter from the generated route object

    $name = $route->getValue('name');

#### Using regular expressions when defining params

You may use regular expression subpatterns when defining parameters.

    @route GET </path/{:name:(pattern)}>

Where name is the name of the parameter and pattern is the matching subpattern.

For example you can definie a path that takes a numeric id parameter:

    @route GET </object/{:id:(\d+)}>


The Route object
----------------
For each request a Route object is created. You may access it to read path
parameters.

    $name = $route->getValue('name');

Or to generate urls from the current or other definied routes.

    // Generate this path using the current path parameters
    $path = $route->generate();

    // Generate any path using custom parameters
    $path = $route->generate('routeName', array('name' => 'foobar'));



A short example
---------------

### A route

```php
use inroute\Runtime\Environment;
use inroute\Runtime\Routable;

class MyRoutableClass implements Routable
{
    /**
     * @route GET </foo/{:name}>
     */
    public function foo(Environment $env)
    {
        return $env->get('route')->name;
    }

    /**
     * @route POST </bar/{:name}>
     */
    public function bar(Environment $env)
    {
        var_dump($env);
    }
}
```


Compiling your project
----------------------
Compile your project using

    $ php vendor/bin/inroute build

This will read source paths from your `composer.json` and output the generated
router to `router.php`. For more information on how to use the command line
utility try

    $ php vendor/bin/inroute help build


The example app
---------------
The inroute source includes an example application. Build the application using

    $ php bin/inroute build --no-composer -p example -o example/router.php

The actual application can be found under [example](example).
View the sources for some explanatory comments.

### Running the app in your browser

The example directory contains three different dispatchers:

* [development.php](example/development.php) builds the application on every
  page reload. Use this style of dispatch during development.
* [production.php](example/production.php) dispatches the application using the
  generated router.

Point your browser to either one of these files to view the output.
