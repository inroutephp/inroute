INROUTE
=======

[![Build Status](https://travis-ci.org/iio/inroute.png?branch=master)](https://travis-ci.org/iio/inroute)

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
tha @inroute tag. It handles fetching dependencies from your DI-container using
the @param tag. And it sets up all routes based on @route tags. From this it
generates a router and a dispatcher. When done all you have to do is to bootstrap
your application auto-loading and dispatch.

    $app = include 'generated_application.php';
    echo $app->dispatch($url, $_SERVER);



Annotations
-----------

### @inroute

All controller classes that should be processed must use the @inroute tag. Se
example controller below, or the example app in the source tree.

### @param

Controllers that needs dependencies injected specify these using the @param
tag. The syntax is

    @param Type $varName inject:containerMethod

Where $varName is the name of the contructor parameter and containerMethod is
the name of the DI-container method that should be called to create the dependency.

### @route

Controller methods that should be routable use the @route tag. The syntax is

    @route METHOD /path

Where METHOD is the desired HTTP-method and path is the route path. You can add
route parameters like this

    @route GET /path/{:name}

Route multiple HTTP-methods to the same controller by listing methods separated
by commas (but without spaces!).

    @route POST,PUT /path/{:name}

And acces the parameter from the generated route object

    $name = $route->getValue('name');

#### Using regular expressions when defining params

You may use regular expression subpatterns when defining parameters.

    @route GET /path/{:name:(pattern)}

Where name is the name of the parameter and pattern is the matching subpattern.

For example you can definie a path that takes a numeric id parameter:

    @route GET /object/{:id:(\d+)}

(Inroute uses the [Aura Router](https://github.com/auraphp/Aura.Router) package
for routing. For more information see the aura documenatation.)



Interfaces
----------

### ContainerInterface

Your DI-container must implement the ContainerInterface for inroute to find it.
Se the example below or the example application in the source tree.

Containers must currently subclass [Pimple](https://github.com/fabpot/Pimple).
This is neither a clean or flexible solution. Please fork and hack away!

### CallerInterface

By default controller methods are called with a Route object as single parameter.
If you want to create more parameters at dispatch (for example some request
object) you can write your own caller. The syntax is straight forward, se
te example application in the source tree for an example.



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

Using getDependency to inject $dep and defining two routes.

    use iio\inroute\Route;

    /**
     * @inroute
     */
    class Controller
    {
        /**
         * @param mixed $dep getDependency
         */
        public function __construct($dep)
        {
        }

        /**
         * @route GET /foo/{:name}
         */
        public function foo(Route $route)
        {
            return $route->getValue('name');
        }

        /**
         * @route POST /bar/{:name}
         */
        public function bar(Route $route)
        {
            var_dump($route);
        }
    }



###A DI-container

Defining the getDependency method

    class Container extends \Pimple implements iio\inroute\ContainerInterface
    {
        public function __construct()
        {
            $this['getDependency'] = function ($c) {
                return new Dependency;
            };
        }
    }



Installing
----------

Inroute can be installed using composer and the packagist repository. Add
iio/inroute as a dependency to your composer.json. When installed through
composer the phar binary is accessed via

    vendor/bin/inroute

Non-composer projects can can build inroute.phar and use it as described below.

If you are using php with the suhosin patch and want to use the phar archive you
might need to set

    suhosin.executor.include.whitelist="phar"

in your php.ini.



Compiling your project
----------------------

Compile your project using

    > bin/inroute build [sourcedir] --loader=[loader] > [target]

Where sourcedir is the base directory of your application source tree, loader is
your composer class loader (vendor/autoload.php) and target is the name of the
generated file.

Optionally you may compile your project using the phar library

    > php inroute.phar build [sourcedir] --loader=[loader] > [target]



The example app
---------------

The inroute source includes an example application. Build the application using

    > example/build

Or view the contens of build for the exact syntax.

The actual application can be found under example/Application. View the sources
for some explanatory comments.

### Running the app in your browser

The example directory contains three different dispatchers:

* development.php builds the application on every page reload. Use this style
  of dispatch during development.
* composer.php dispatches the application using the composer autoloader. This
  style of usage requires inroute to be installed as a composer dependancy.
* phar.php dispatches using the phar archive. This only requires the phar file.
  Slightly slower than using composer, but sutable for non-composer projects.

Point your browser to either one of these files to view the output.

### Problems with xcache when using phar

Running inroute from phar when xcache is installed has been known to trigger
a PharException with message '__HALT_COMPILER(); must be declared in a phar'. If
this problem occurs try to uninstall xcache.



Testing
-------

Run unittests by typing

    > phpunit

Optionally you can use Phing to run tests together with additional checks.

[Installation instructions.](http://www.phing.info/trac/wiki/Users/Installation)

Then from the root project directory type

    > phing

The build directory will be filled with the test results. Point your browser to
*build/index.html* to investigate.



Building the phar
-----------------

Build the phar using

    > bin/compile

Or with phing

    > phing build

Building the phar requires

    phar.readonly=0

in your php.ini.
