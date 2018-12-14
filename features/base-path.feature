Feature: Specify a base path
  In order to create http applications
  As a user
  I need to be able to set base paths

  Scenario: I dispatch middleware pipeline
    Given a controller "BasePathController":
    """
    use Zend\Diactoros\Response\TextResponse;

    /**
     * @\inroutephp\inroute\Annotations\BasePath(path="/base")
     */
    class BasePathController
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
    When I request "GET" "/base/action"
    Then the response body is "ACTION"
