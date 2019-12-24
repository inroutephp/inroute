Feature: Ignore unknown annotations while compiling

  Scenario: I ignore unknown annotation
    Given code:
    """
    class RouteWithUnknownAnnotation
    {
        /**
         * @required
         */
        public function setDependency($dep)
        {
        }

        /**
         * @\inroutephp\inroute\Annotations\GET(path="/action")
         */
        function action()
        {
            return new \Zend\Diactoros\Response\TextResponse('FOO');
        }
    }
    """
    And compiler settings:
    """
    {
        "source-classes": ["RouteWithUnknownAnnotation"],
        "ignore-annotations": ["required"]
    }
    """
    When I build application
    And I request "GET" "/action"
    Then the response body is "FOO"
