Feature: Ignore unknown annotations while compiling

  Scenario: I ignore unknown annotation
    Given a controller "RouteWithUnknownAnnotation":
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
    return new \inroutephp\inroute\Compiler\Settings\ArraySettings([
        "ignore-annotations" => ["required"]
    ]);
    """
    When I request "GET" "/action"
    Then the response body is "FOO"
