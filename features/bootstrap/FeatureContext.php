<?php

use inroutephp\inroute\Compiler\CompilerFacade;
use inroutephp\inroute\Compiler\Settings\ArraySettings;
use inroutephp\inroute\Compiler\Settings\ManagedSettings;
use inroutephp\inroute\Compiler\Settings\SettingsInterface;
use inroutephp\inroute\Runtime\HttpRouterInterface;
use inroutephp\inroute\Runtime\Middleware\Pipeline;
use inroutephp\inroute\Runtime\NaiveContainer;
use Psr\Http\Message\ResponseInterface;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class FeatureContext implements Context
{
    /** @var string */
    private $containerClass = NaiveContainer::class;

    /** @var HttpRouterInterface */
    private $router;

    /** @var SettingsInterface */
    private $settings;

    /** @var ResponseInterface */
    private $response;

    /** @var \Exception */
    private $exception;

    public function __construct(array $defaultSettings = [])
    {
        $this->settings = new ArraySettings($defaultSettings);
    }

    /**
     * @Given code:
     */
    public function code(PyStringNode $code)
    {
        eval((string)$code);
    }

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
     * @Given a router:
     */
    public function aRouter(PyStringNode $code)
    {
        $this->router = eval((string)$code);
    }

    /**
     * @Given compiler settings:
     */
    public function compilerSettings(PyStringNode $json)
    {
        $this->settings = new ManagedSettings(
            new ArraySettings(json_decode((string)$json, true)),
            $this->settings
        );
    }

    /**
     * @When I build application
     */
    public function iBuildApplication()
    {
        $routerClass = uniqid('HttpRouter');
        $containerClass = $this->containerClass;

        eval(
            (new CompilerFacade)->compileProject(
                new ManagedSettings(
                    $this->settings,
                    new ArraySettings([
                        'container' => $containerClass,
                        'target-namespace' => '',
                        'target-classname' => $routerClass,
                    ])
                )
            )
        );

        $this->router = new $routerClass;
        $this->router->setContainer(new $containerClass);
    }

    /**
     * @When I request :method :path
     */
    public function iRequest($method, $path)
    {
        $request = (new \Laminas\Diactoros\ServerRequestFactory)->createServerRequest($method, $path);

        try {
            $this->response = (new Pipeline($this->router))->handle($request);
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
