inroute
=======

Generate web router and controller dispatcher from docblock annotations

## Inline invocation using the composer autoloader

    include "vendor/autoload.php";

    $factory = new \itbz\inroute\InrouteFactory();
    $factory->setDirs(array('dir/to/project'));
    $inroute = eval($factory->generate());

    echo $inroute->dispatch('/foo/yeah', $_SERVER);

## Controller

    namespace itbz\test;
    use itbz\inroute\Route;

    /**
     * @inroute
     */
    class Working
    {
        /**
         * @inject $x xfactory
         * @inject $bar foobar
         * @inject $y xx
         */
        public function __construct(\DateTime $bar, array $x, $y = 'optional')
        {
        }

        /**
         * @route GET /foo/{:name}
         */
        public function foo(Route $route)
        {
            return 'Working::foo';
        }

        /**
         * @route POST /bar/{:name}
         */
        public function bar(Route $route)
        {
            var_dump($route);
        }
    }

## DI-container

    namespace itbz\test;

    class Container extends \Pimple
    {
        public function __construct()
        {
            $this['foobar'] = function ($c) {
                return new \DateTime;
            };
            $this['xfactory'] = function ($c) {
                return array();
            };
            $this['xx'] = function ($c) {
                return 'xx';
            };
        }
    }
