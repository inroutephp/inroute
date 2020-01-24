Feature: Handle route middleware pipelines

  Scenario: I add a route middleware
    Given code:
    """
    class EmptyRoute
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
    And code:
    """
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class Middleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return new \Laminas\Diactoros\Response\TextResponse('MIDDLEWARE');
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["EmptyRoute"]
    }
    """
    When I build application
    And I request "GET" "/action"
    Then the response body is "MIDDLEWARE"

  Scenario: I add multiple route middlewares
    Given code:
    """
    class MultiplePipeRoute
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         * @\inroutephp\inroute\Annotations\Pipe(middlewares="MultiplePipeMiddleware")
         * @\inroutephp\inroute\Annotations\Pipe(middlewares="MultiplePipeMiddleware")
         */
        function action()
        {
            return new \Laminas\Diactoros\Response\TextResponse('C');
        }
    }
    """
    And code:
    """
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    class MultiplePipeMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return new \Laminas\Diactoros\Response\TextResponse(
                $handler->handle($request)->getBody()->getContents() . ":M"
            );
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["MultiplePipeRoute"]
    }
    """
    When I build application
    And I request "GET" "/action"
    Then the response body is "C:M:M"

  Scenario: I add middleware using a custom annotation
    Given code:
    """
    use Psr\Http\Server\MiddlewareInterface;
    use Psr\Http\Server\RequestHandlerInterface;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    /** @Annotation */
    class CustomAnnotation extends inroutephp\inroute\Annotations\Pipe
    {
        public $middlewares = ['CustomAnnotationMiddleware'];
        public $attributes = ['custom_attribute' => 'ATTRIBUTE'];
    }

    class CustomAnnotationRoute
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         * @CustomAnnotation
         */
        function action()
        {
        }
    }

    class CustomAnnotationMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            return new \Laminas\Diactoros\Response\TextResponse($request->getAttribute('custom_attribute'));
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["CustomAnnotationRoute"]
    }
    """
    When I build application
    And I request "GET" "/action"
    Then the response body is "ATTRIBUTE"
