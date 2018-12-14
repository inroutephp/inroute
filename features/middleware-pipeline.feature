Feature: Handle route middleware pipelines
  In order to create http applications
  As a user
  I need to handle middleware pipelines

  Scenario: I add a route middleware
    Given a controller "ActionController":
    """
    class ActionController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         * @\inroutephp\inroute\Annotations\Pipe(middlewares="Middleware")
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
    When I request "GET" "/action"
    Then the response body is "MIDDLEWARE"

  Scenario: I add multiple route middlewares
    Given a controller "MultiplePipeController":
    """
    use Zend\Diactoros\Response\TextResponse;

    class MultiplePipeController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         * @\inroutephp\inroute\Annotations\Pipe(middlewares="MultiplePipeMiddleware")
         * @\inroutephp\inroute\Annotations\Pipe(middlewares="MultiplePipeMiddleware")
         */
        function action()
        {
            return new TextResponse('C');
        }
    }
    """
    And a middleware "MultiplePipeMiddleware":
    """
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Zend\Diactoros\Response\TextResponse;

    class MultiplePipeMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return new TextResponse(
                $handler->handle($request)->getBody()->getContents() . ":M"
            );
        }
    }
    """
    When I request "GET" "/action"
    Then the response body is "C:M:M"

  Scenario: I add middleware using a custom annotation
    Given a controller "CustomAnnotationController":
    """
    /** @Annotation */
    class CustomAnnotation extends inroutephp\inroute\Annotations\Pipe
    {
        public $middlewares = ['CustomAnnotationMiddleware'];
        public $attributes = ['custom_attribute' => 'ATTRIBUTE'];
    }

    class CustomAnnotationController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         * @CustomAnnotation
         */
        function action()
        {
        }
    }
    """
    And a middleware "CustomAnnotationMiddleware":
    """
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Zend\Diactoros\Response\TextResponse;

    class CustomAnnotationMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return new TextResponse($request->getAttribute('custom_attribute'));
        }
    }
    """
    When I request "GET" "/action"
    Then the response body is "ATTRIBUTE"
