Feature: Handle route not found situations

  Scenario: I catch route not found exception
    When I build application
    And I request "GET" "/foo"
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
    When I build application
    And I request "GET" "/foo"
    Then the response code is "404"

  Scenario: I catch route method not allowed exception
    Given code:
    """
    class PostRoute1
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
    And compiler settings:
    """
    {
        "source-classes": ["PostRoute1"]
    }
    """
    When I build application
    And I request "GET" "/foo"
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
    And code:
    """
    class PostRoute2
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
    And compiler settings:
    """
    {
        "source-classes": ["PostRoute2"]
    }
    """
    When I build application
    And I request "GET" "/foo"
    Then the response code is "405"
