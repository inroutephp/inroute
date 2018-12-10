Feature: Handle middleware pipeline for route
  In order to create http applications
  As a user
  I need to handle middleware pipelines

  Scenario: I dispatch middleware pipeline
    Given a controller "ActionController":
    """
    class ActionController
    {
        /**
         * @\inroutephp\inroute\Annotation\Route(
         *     method="GET",
         *     path="/action"
         * )
         */
        function action()
        {
        }
    }
    """
    And a middleware "Middleware":
    """
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Zend\Diactoros\Response\TextResponse;

    class Middleware implements MiddlewareInterface
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
            return $route->withMiddleware(Middleware::CLASS);
        }
    }
    """
    When I request "GET" "/action"
    Then the response body is "MIDDLEWARE"
