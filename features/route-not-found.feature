Feature: Handle route not found situations
  In order to create http applications
  As a user
  I need to handle route not found situations

  Scenario: I catch route not found exception
    Given an empty router
    And an empty container
    When I request "/foo"
    Then a "RouteNotFoundException" exception is thrown

  Scenario: I generate a route not found response using a response factory
    Given an empty router
    And a container like:
    """
    [
        'Psr\\Http\\Message\\ResponseFactoryInterface' => function () {
            return new class implements \\Psr\\Http\\Message\\ResponseFactoryInterface {
                public function createResponse(int $code = 200, string $phrase = ''): \\Psr\\Http\\Message\\ResponseInterface
                {
                    return (new \\Zend\\Diactoros\\Response)->withStatus($code);
                }
            };
        },
    ]
    """
    When I request "/foo"
    Then a "404" response is returned
