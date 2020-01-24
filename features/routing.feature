Feature: Basic routing

  Scenario: I route to action
    Given code:
    """
    class ActionRoute
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         */
        function action()
        {
            return new \Laminas\Diactoros\Response\TextResponse('ACTION');
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["ActionRoute"]
    }
    """
    When I build application
    And I request "GET" "/action"
    Then the response body is "ACTION"

  Scenario: I specify multiple routes to the same method
    Given code:
    """
    class MultipleRoutes
    {
        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         * @\inroutephp\inroute\Annotations\PUT(path="/action")
         */
        function action()
        {
            return new \Laminas\Diactoros\Response\TextResponse('ACTION');
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["MultipleRoutes"]
    }
    """
    When I build application
    And I request "PUT" "/action"
    Then the response body is "ACTION"

  Scenario: I set the base path as a class annotation
    Given code:
    """
    /**
     * @\inroutephp\inroute\Annotations\BasePath(path="/base")
     */
    class BasePathRoute
    {
        /**
         * @\inroutephp\inroute\Annotations\GET
         */
        function action()
        {
            return new \Laminas\Diactoros\Response\TextResponse('ACTION');
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["BasePathRoute"]
    }
    """
    When I build application
    And I request "GET" "/base"
    Then the response body is "ACTION"

  Scenario: I omit the route path
    Given code:
    """
    class NoPathRoute
    {
        /**
         * @\inroutephp\inroute\Annotations\GET
         */
        function action()
        {
            return new \Laminas\Diactoros\Response\TextResponse('ACTION');
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["NoPathRoute"]
    }
    """
    When I build application
    And I request "GET" ""
    Then the response body is "ACTION"
