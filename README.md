# inroute [![Build Status](https://travis-ci.org/hanneskod/inroute.svg?branch=master)](https://travis-ci.org/hanneskod/inroute) [![Code Coverage](https://scrutinizer-ci.com/g/hanneskod/inroute/badges/coverage.png?s=236858bb9fdc57144bc0012e11ea0256925e18a6)](https://scrutinizer-ci.com/g/hanneskod/inroute/) [![Dependency Status](https://gemnasium.com/hanneskod/inroute.svg)](https://gemnasium.com/hanneskod/inroute)


Generate web router and dispatcher from docblock annotations

When building web-apps a constantly see myself repeating the same pattern.
I define some controller classes that have various dependencies. I write a
DI-container to facilitate instantiating my controllers. I use some router
package and write a file defining all my routes and point them to my controllers.
And lastly I write some kind of dispatch logic where I perform routing, get my
controller objects from the container and execute the controller. All of this is
boring and error-prone.

Inroute tries to fix this by handling dependency injection and routing directly
in the controller classes using annotations (actually docblock style tags).

Inroute is a code generator. It scans your source tree for classes marked with
tha @controller tag. It handles fetching dependencies from your DI-container using
the @param tag. And it sets up all routes based on @route tags. From this it
generates a router and a dispatcher. When done all you have to do is to bootstrap
your application auto-loading and dispatch.

    $app = include 'generated_application.php';
    echo $app->dispatch($url, $_SERVER);


Annotations
-----------

### @controller

All controller classes that should be processed must use the @controller tag.
Optionally you may specify a root path for all controller routes. Se the example
controller below, or the example app in the source tree.

### @route

Controller methods that should be routable use the @route tag. The syntax is

    @route METHOD </path>

Where METHOD is the desired HTTP-method and path is the route path. You can add
route parameters like this

    @route GET </path/{:name}>

Route multiple HTTP-methods to the same controller by listing methods separated
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

(Inroute uses the [Aura Router](https://github.com/auraphp/Aura.Router) package
for routing. For more information see the aura documenatation.)


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

### A controller

    use inroute\Router\Route;

    /**
     * @controller
     */
    class Controller
    {
        /**
         * @route GET </foo/{:name}>
         */
        public function foo(Route $route)
        {
            return $route->name;
        }

        /**
         * @route POST </bar/{:name}>
         */
        public function bar(Route $route)
        {
            var_dump($route);
        }
    }


Installation using [composer](http://getcomposer.org/)
------------------------------------------------------
To your `composer.json` add

    "require-dev": {
        "hanneskod/inroute": "dev-master@dev",
    }


Compiling your project
----------------------
Compile your project using

    $ vendor/bin/inroute build [sourcedir] --loader=[loader] > [target]

Where sourcedir is the base directory of your application source tree, loader is
your composer class loader (vendor/autoload.php) and target is the name of the
generated file.

For more information on how to use the command line utility

    $ vendor/bin/inroute --help


The example app
---------------
The inroute source includes an example application. Build the application using

    $ example/build

The actual application can be found under [example/Application](example/Application).
View the sources for some explanatory comments.

### Running the app in your browser

The example directory contains three different dispatchers:

* [development.php](example/development.php) builds the application on every
  page reload. Use this style of dispatch during development.
* [composer.php](example/composer.php) dispatches the application using the
  composer autoloader. This style of usage requires inroute to be installed as a
  composer dependancy.

Point your browser to either one of these files to view the output.


Testing using [phpunit](http://phpunit.de/)
-------------------------------------------
The unis tests requires that dependencies are installed using composer.

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar install
    $ phpunit
