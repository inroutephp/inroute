Feature: Handle route not found situations
  In order to create http applications
  As a user
  I need to handle route not found situations

  Scenario: I catch route not found exception
    When I request "GET" "/foo"
    Then a "RouteNotFoundException" exception is thrown

  Scenario: I generate a route not found response using a response factory
    Given a container with services:
    """
    [
        ResponseFactoryInterface::CLASS => new class implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $phrase = ''): ResponseInterface
            {
                return (new \Zend\Diactoros\Response)->withStatus($code);
            }
        }
    ]
    """
    When I request "GET" "/foo"
    Then the response code is "404"

  Scenario: I catch route method not allowed exception
    Given a controller "PostController1":
    """
    class PostController1
    {
        /**
         * @\inroutephp\inroute\Annotations\Route(
         *     method="POST",
         *     path="/foo"
         * )
         */
        function foo()
        {
        }
    }
    """
    When I request "GET" "/foo"
    Then a "MethodNotAllowedException" exception is thrown

  Scenario: I generate a method not allowed response using a response factory
    Given a container with services:
    """
    [
        ResponseFactoryInterface::CLASS => new class implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $phrase = ''): ResponseInterface
            {
                return (new \Zend\Diactoros\Response)->withStatus($code);
            }
        }
    ]
    """
    And a controller "PostController2":
    """
    class PostController2
    {
        /**
         * @\inroutephp\inroute\Annotations\Route(
         *     method="POST",
         *     path="/foo"
         * )
         */
        function foo()
        {
        }
    }
    """
    When I request "GET" "/foo"
    Then the response code is "405"
