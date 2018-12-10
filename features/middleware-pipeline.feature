Feature: Handle middleware pipeline for route
  In order to create http applications
  As a user
  I need to handle middleware pipelines

  Scenario: I dispatch middleware pipeline
    Given a router like:
    """
    return new class implements \\Psr\\Http\\Server\\MiddlewareInterface {
        use AuraHttpRouterTrait;

        protected function loadAuraRoutes(Map $map): void
        {
            AuraRoute::__set_state([
                'name' => 'foo',
                'path' => '/foo',
                'serviceId' => 'controller',
                'serviceMethod' => 'action',
                'middlewareServiceIds' => ['middleware']
            ])->writeTo($map);
        }
    }
    """
    And a container like:
    """
    [
        'controller' => function () {
            return new class {
                public function action()
                {
                    return (new \\Zend\\Diactoros\\Response);
                }
            };
        },
        'middleware' => function () {
            return new class implements \\Psr\\Http\\Server\\MiddlewareInterface {
                public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
                {
                    return $handler->handle($request)->withStatus(300);
                }
            }
        }
    ]
    """
    When I request "/foo"
    Then a "300" response is returned
