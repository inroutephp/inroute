Feature: Add a custom compiler pass
  In order to create http applications
  As a user
  I need to be able to write custom compiler passes

  Scenario: I create a custom compiler pass
    Given a controller "CompilerPassController":
    """
    class CompilerPassController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         */
        function action()
        {
        }
    }
    """
    And a middleware "CompilerPassMiddleware":
    """
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Zend\Diactoros\Response\TextResponse;

    class CompilerPassMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return new TextResponse('MIDDLEWARE');
        }
    }
    """
    And a compiler pass "CompilerPass":
    """
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
    When I request "GET" "/action"
    Then the response body is "MIDDLEWARE"
