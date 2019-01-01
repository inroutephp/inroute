Feature: Basic routing
  In order to create http applications
  As a user
  I need to be able to rout http requests

  Scenario: I route to action
    Given a controller "RouteController":
    """
    use Zend\Diactoros\Response\TextResponse;

    class RouteController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         */
        function action()
        {
            return new TextResponse('ACTION');
        }
    }
    """
    When I request "GET" "/action"
    Then the response body is "ACTION"

  Scenario: I specify multiple routes to the same controller
    Given a controller "MultipleController":
    """
    use Zend\Diactoros\Response\TextResponse;

    class MultipleController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         * @\inroutephp\inroute\Annotations\PUT(path="/action")
         */
        function action()
        {
            return new TextResponse('ACTION');
        }
    }
    """
    When I request "PUT" "/action"
    Then the response body is "ACTION"

  Scenario: I set the base path as a class annotation
    Given a controller "BasePathController":
    """
    use Zend\Diactoros\Response\TextResponse;

    /**
     * @\inroutephp\inroute\Annotations\BasePath(path="/base")
     */
    class BasePathController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET
         */
        function action()
        {
            return new TextResponse('ACTION');
        }
    }
    """
    When I request "GET" "/base"
    Then the response body is "ACTION"

  Scenario: I omit the route path
    Given a controller "NoPathController":
    """
    use Zend\Diactoros\Response\TextResponse;

    class NoPathController
    {
        /**
         * @\inroutephp\inroute\Annotations\GET
         */
        function action()
        {
            return new TextResponse('ACTION');
        }
    }
    """
    When I request "GET" ""
    Then the response body is "ACTION"
