Feature: Add a custom compiler pass

  Scenario: I create a custom compiler pass
    Given code:
    """
    class CompilerPassRoute
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         */
        function action()
        {
        }
    }

    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class CompilerPassMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return new \Zend\Diactoros\Response\TextResponse('MIDDLEWARE');
        }
    }

    use inroutephp\inroute\Compiler\CompilerPassInterface;
    use inroutephp\inroute\Runtime\RouteInterface;

    class CompilerPass implements CompilerPassInterface
    {
        public function processRoute(RouteInterface $route): RouteInterface
        {
            return $route->withMiddleware(CompilerPassMiddleware::CLASS);
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["CompilerPassRoute"],
        "compiler-passes": ["CompilerPass"]
    }
    """
    When I build application
    And I request "GET" "/action"
    Then the response body is "MIDDLEWARE"
