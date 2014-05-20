<?php namespace inroute\Runtime;
if (!interface_exists('inroute\Runtime\PreFilterInterface')) {
interface PreFilterInterface
{
    public function filter(Environment $env);
}
}
if (!class_exists('inroute\Runtime\NextRouteException')) {
class NextRouteException extends \Exception
{
}
}
if (!class_exists('inroute\Runtime\Regex')) {
class Regex
{
    private $regex;
    private $matches = array();
    public function __construct($regex = '')
    {
        $this->regex = $regex ?: '[^/]+';
    }
    public function __tostring()
    {
        return $this->regex;
    }
    public function getRegex()
    {
        return "#^{$this->regex}$#";
    }
    public function match($str)
    {
        return !!preg_match($this->getRegex(), $str, $this->matches);
    }
    public function __get($key)
    {
        if (isset($this->matches[$key])) {
            return $this->matches[$key];
        }
        return '';
    }
}
}
if (!class_exists('inroute\Runtime\Route')) {
class Route
{
    private $tokens;
    private $regex;
    private $env;
    private $preFilters;
    private $postFilters;
    private $methodMatch = '';
    private $pathMatch = '';
    public function __construct(
        array $tokens,
        Regex $regex,
        Environment $env,
        array $preFilters,
        array $postFilters
    ) {
        $this->tokens = $tokens;
        $this->regex = $regex;
        $this->env = $env;
        $this->preFilters = $preFilters;
        $this->postFilters = $postFilters;
    }
    public function execute(callable $instantiator)
    {
        $this->env->set('route', $this);
        foreach ($this->preFilters as $filtername) {
            $this->instantiateAndExecute($filtername, 'filter', $this->env, $instantiator);
        }
        $returnValue = $this->instantiateAndExecute(
            $this->env->get('controller_name'),
            $this->env->get('controller_method'),
            $this->env,
            $instantiator
        );
        foreach ($this->postFilters as $filtername) {
            $returnValue = $this->instantiateAndExecute($filtername, 'filter', $returnValue, $instantiator);
        }
        return $returnValue;
    }
    private function instantiateAndExecute($classname, $methodname, $arg, callable $instantiator)
    {
        return call_user_func(
            [
                call_user_func($instantiator, $classname),
                $methodname
            ],
            $arg
        );
    }
    public function isMethodMatch($method)
    {
        if (in_array($method, (array)$this->env->get('http_methods'))) {
            $this->methodMatch = $method;
            return true;
        }
        $this->methodMatch = '';
        return false;
    }
    public function isPathMatch($path)
    {
        if ($this->regex->match($path)) {
            $this->pathMatch = $path;
            return true;
        }
        $this->pathMatch = '';
        return false;
    }
    public function getName()
    {
        return sprintf(
            '%s::%s',
            $this->env->get('controller_name'),
            $this->env->get('controller_method')
        );
    }
    public function getMethod()
    {
        return $this->methodMatch;
    }
    public function getPath()
    {
        return $this->pathMatch;
    }
    public function __get($key)
    {
        return $this->regex->$key;
    }
    public function generate(array $params)
    {
        $parts = array();
        foreach ($this->tokens as $token) {
            if (is_string($token)) {
                $parts[] = $token;
                continue;
            }
            if (!isset($params[$token->getName()])) {
                throw new \RuntimeException("Parameter <{$token->getName()}> missing.");
            }
            $parts[] = $token->substitute($params[$token->getName()]);
        }
        return implode('/', $parts);
    }
}
}
if (!class_exists('inroute\Runtime\Router')) {
class Router
{
    private $routes;
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }
}
}
if (!class_exists('inroute\Runtime\PathSegment')) {
class PathSegment
{
    private $name;
    private $regex;
    public function __construct($name, Regex $regex)
    {
        $this->name = $name;
        $this->regex = $regex;
    }
    public function getName()
    {
        return $this->name;
    }
    public function __tostring()
    {
        return "(?<{$this->name}>{$this->regex})";
    }
    public function substitute($value)
    {
        if ($this->regex->match($value)) {
            return $value;
        }
        throw new \RuntimeException("Unable to substitute token <{$this->name}> with <$value>.");
    }
}
}
if (!interface_exists('inroute\Runtime\PostFilterInterface')) {
interface PostFilterInterface
{
    public function filter($value);
}
}
if (!interface_exists('inroute\Runtime\ControllerInterface')) {
interface ControllerInterface
{
}
}
if (!class_exists('inroute\Runtime\Environment')) {
class Environment
{
    private $data;
    public function __construct(array $data = array())
    {
        $this->data = array_change_key_case($data, CASE_LOWER);
    }
    public function set($key, $value)
    {
        $this->data[strtolower($key)] = $value;
    }
    public function get($key)
    {
        $key = strtolower($key);
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return '';
    }
    public function toArray()
    {
        return $this->data;
    }
}
}
if (!class_exists('inroute\Runtime\Instantiator')) {
class Instantiator
{
    public function __invoke($classname)
    {
        return new $classname;
    }
}
}
return new Router(unserialize('a:3:{i:0;O:21:"inroute\Runtime\Route":7:{s:29:" inroute\Runtime\Route tokens";a:2:{i:0;s:0:"";i:1;s:7:"example";}s:28:" inroute\Runtime\Route regex";O:21:"inroute\Runtime\Regex":2:{s:28:" inroute\Runtime\Regex regex";s:8:"/example";s:30:" inroute\Runtime\Regex matches";a:0:{}}s:26:" inroute\Runtime\Route env";O:27:"inroute\Runtime\Environment":1:{s:33:" inroute\Runtime\Environment data";a:4:{s:15:"controller_name";s:23:"inroute\example\Working";s:17:"controller_method";s:3:"foo";s:4:"path";s:8:"/example";s:12:"http_methods";a:0:{}}}s:33:" inroute\Runtime\Route preFilters";a:0:{}s:34:" inroute\Runtime\Route postFilters";a:1:{i:0;s:26:"inroute\example\HtmlFilter";}s:34:" inroute\Runtime\Route methodMatch";s:0:"";s:32:" inroute\Runtime\Route pathMatch";s:0:"";}i:1;O:21:"inroute\Runtime\Route":7:{s:29:" inroute\Runtime\Route tokens";a:2:{i:0;s:0:"";i:1;s:7:"example";}s:28:" inroute\Runtime\Route regex";O:21:"inroute\Runtime\Regex":2:{s:28:" inroute\Runtime\Regex regex";s:8:"/example";s:30:" inroute\Runtime\Regex matches";a:0:{}}s:26:" inroute\Runtime\Route env";O:27:"inroute\Runtime\Environment":1:{s:33:" inroute\Runtime\Environment data";a:4:{s:15:"controller_name";s:23:"inroute\example\Working";s:17:"controller_method";s:3:"bar";s:4:"path";s:8:"/example";s:12:"http_methods";a:0:{}}}s:33:" inroute\Runtime\Route preFilters";a:0:{}s:34:" inroute\Runtime\Route postFilters";a:1:{i:0;s:26:"inroute\example\HtmlFilter";}s:34:" inroute\Runtime\Route methodMatch";s:0:"";s:32:" inroute\Runtime\Route pathMatch";s:0:"";}i:2;O:21:"inroute\Runtime\Route":7:{s:29:" inroute\Runtime\Route tokens";a:2:{i:0;s:0:"";i:1;s:7:"example";}s:28:" inroute\Runtime\Route regex";O:21:"inroute\Runtime\Regex":2:{s:28:" inroute\Runtime\Regex regex";s:8:"/example";s:30:" inroute\Runtime\Regex matches";a:0:{}}s:26:" inroute\Runtime\Route env";O:27:"inroute\Runtime\Environment":1:{s:33:" inroute\Runtime\Environment data";a:4:{s:15:"controller_name";s:23:"inroute\example\Working";s:17:"controller_method";s:7:"noRoute";s:4:"path";s:8:"/example";s:12:"http_methods";a:0:{}}}s:33:" inroute\Runtime\Route preFilters";a:0:{}s:34:" inroute\Runtime\Route postFilters";a:1:{i:0;s:26:"inroute\example\HtmlFilter";}s:34:" inroute\Runtime\Route methodMatch";s:0:"";s:32:" inroute\Runtime\Route pathMatch";s:0:"";}}'));
