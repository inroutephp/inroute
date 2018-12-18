<?php

use inroutephp\inroute\Compiler\CompilerFacade;
use inroutephp\inroute\Compiler\Settings\ArraySettings;
use Psr\Http\Message\ResponseInterface;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class FeatureContext implements Context
{
    private $containerClass = \inroutephp\inroute\Runtime\NaiveContainer::CLASS;
    private $controllerClasses = [];
    private $compilerPasses = [];
    private $router = null;

    /** @var ResponseInterface */
    private $response;

    /** @var \Exception */
    private $exception;

    /**
     * @Given a container with services:
     */
    public function aContainerWithServices(PyStringNode $services)
    {
        $this->containerClass = uniqid('Container');

        $code = "
            use Psr\Container\ContainerInterface;
            use Psr\Http\Message\ResponseFactoryInterface;
            use Psr\Http\Message\ResponseInterface;

            class {$this->containerClass} implements ContainerInterface
            {
                public function __construct()
                {
                    \$this->services = $services;
                }

                public function get(\$id)
                {
                    if (!\$this->has(\$id)) {
                        return (new \inroutephp\inroute\Runtime\NaiveContainer)->get(\$id);
                    }
                    return \$this->services[\$id];
                }

                public function has(\$id)
                {
                    return isset(\$this->services[\$id]);
                }
            }
        ";

        eval($code);
    }

    /**
     * @Given a controller :classname:
     */
    public function aController($classname, PyStringNode $code)
    {
        eval((string)$code);
        $this->controllerClasses[] = $classname;
    }

    /**
     * @Given a middleware :classname:
     */
    public function aMiddleware($classname, PyStringNode $code)
    {
        eval((string)$code);
    }

    /**
     * @Given a compiler pass :classname:
     */
    public function aCompilerPass($classname, PyStringNode $code)
    {
        eval((string)$code);
        $this->compilerPasses[] = $classname;
    }

    /**
     * @Given a router :classname:
     */
    public function aRouter($classname, PyStringNode $code)
    {
        eval((string)$code);
        $this->router = new $classname;
    }

    /**
     * @When I request :method :path
     */
    public function iRequest($method, $path)
    {
        if (!$this->router) {
            $routerClass = uniqid('HttpRouter');

            eval(
                (new CompilerFacade)->compileProject(new ArraySettings([
                    'source-classes' => $this->controllerClasses,
                    'compiler-passes' => $this->compilerPasses,
                    'container' => $this->containerClass,
                    'target-namespace' => '',
                    'target-classname' => $routerClass,
                ]))
            );

            $this->router = new $routerClass;
        }

        if ($this->containerClass) {
            $containerClass = $this->containerClass;
            $this->router->setContainer(new $containerClass);
        }

        $request = (new \Zend\Diactoros\ServerRequestFactory)->createServerRequest($method, $path);

        try {
            $this->response = (new \mindplay\middleman\Dispatcher([$this->router]))->dispatch($request);
        } catch (\Exception $e) {
            $this->exception = $e;
        }
    }

    /**
     * @Then the response body is :expected
     */
    public function theResponseBodyIs($expected)
    {
        if (isset($this->exception)) {
            throw $this->exception;
        }

        $body = $this->response->getBody()->getContents();
        if ($body != $expected) {
            throw new \Exception(sprintf(
                'Excpected response body: %s, found: %s',
                $expected,
                $body
            ));
        }
    }

    /**
     * @Then the response code is :expected
     */
    public function theResponseCodeIs($expected)
    {
        if ($this->response->getStatusCode() != $expected) {
            throw new \Exception(sprintf(
                'Excpected response code: %s, found: %s',
                $expected,
                $this->response->getStatusCode()
            ));
        }
    }

    /**
     * @Then a :classname exception is thrown
     */
    public function aExceptionIsThrown($classname)
    {
        $classname = "inroutephp\inroute\Runtime\Exception\\$classname";

        if (!$this->exception instanceof $classname) {
            throw new \Exception(sprintf(
                'Exception of class %s expected, found %s',
                $classname,
                get_class($this->exception)
            ));
        }
    }
}
