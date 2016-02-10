<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:04
 */

namespace Framework\Router;


/**
 * Class Router
 * @package Framework\Router
 */
class Router
{

    /**
     * An associative array.
     * Contains preliminary mapped routes.
     *
     * @var array
     */
    private static $_map = array();

    /**
     * Router constructor.
     * @param array $routing_map Contains preliminary mapped routes from config.
     */
    public function __construct($routing_map = array())
    {

        self::$_map = $routing_map;
    }

    /**
     * Parces route.
     *
     * @param $uri
     * @return array $route_found An associative array of route's parameters.
     */
    public function parseRoute($uri)
    {
        $route_found = null;

        foreach (self::$_map as $route) {
            $param_names = $this->parseParameterName($route); // Parse parameter's names from config's route pattern
            $pattern = $this->prepare($route, $param_names); // Prepare root: taking into account pattern requirements for route parameters

            if (preg_match_all($pattern, $uri, $params)) {

                $route_found = $route;

                if (!empty($param_names)) {
                    array_shift($params);
                    for ($i = 0; $i < count($params); $i++) { // Prepare array $params  to be able combining with array $param_names
                        $prepared_params[$i] = $params[$i][0];
                    }
                    $combine_params = array_combine($param_names, $prepared_params);
                    $route_found['parameters'] = $combine_params;
                }
                break;
            }
        }
        return $route_found;
    }

    /**
     * Parse parameter's names from config's route pattern
     *
     * @param string $route
     * @return array|null Route parameter's names
     */
    private function parseParameterName($route)
    {
        $trimmed_names = null;

        if (preg_match_all('~({.+})~Ui', $route['pattern'], $matched_names)) {
            array_shift($matched_names);
            $trimmed_names = array_map(function ($name) {
                return preg_replace('~[{}]~', '', $name);
            }, $matched_names[0]);
        }
        return $trimmed_names;
    }

    /**
     * Prepare URI pattern.
     *
     * @param string $route URI pattern from config.
     * @param array $names Route parameter's names from config.
     * @return string $pattern Prepared URI pattern with considering of pattern requirements for route parameters.
     */
    private function prepare($route, $names)
    {
        $pattern = $route['pattern'];
        if (!empty($names)) {
            foreach ($names as $key) {
                if (empty($route['_requirements'][$key])) {
                    $pattern = preg_replace('~\{' . $key . '\}~', '([\w\d_]+)', $pattern);
                } else {
                    $pattern = preg_replace('~\{' . $key . '\}~', '(' . $route['_requirements'][$key] . ')', $pattern);
                }
            }
        }
        return '~^' . $pattern . '$~';
    }


    public function buildRoute($route_name, $params = null)
    {
        $route_found = !empty(self::$_map[$route_name]['pattern']) ? self::$_map[$route_name]['pattern'] : null;

        if ($route_found && !empty($params)) {
            foreach ($params as $key => $value) {
                $route_found = preg_replace('~\{' . $key . '\}~', $value, $route_found);
            }
        }
        return $route_found;
    }

    function test1()
    {
        echo '<pre>';
        echo $path = '/', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/test_redirect', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/test_json', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/signin', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/login', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/logout', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/profile', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/posts/add', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/posts/25/edit/30', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/posts/35/edit', "\n";
        print_r($this->parseRoute($path));
        echo '</pre>';
        exit();
    }

    function test2()
    {
        echo '<pre>';
        echo $route_name = 'home', "\n";
        echo ($this->buildRoute($route_name)),  "\n";
        echo $route_name = 'testredirect', "\n";
        echo ($this->buildRoute($route_name)),  "\n";
        echo $route_name = 'test_json', "\n";
        echo ($this->buildRoute($route_name)), "\n";
        echo $route_name = 'signin', "\n";
        echo ($this->buildRoute($route_name)), "\n";
        echo $route_name = 'login', "\n";
        echo ($this->buildRoute($route_name)), "\n";
        echo $route_name = 'logout', "\n";
        echo ($this->buildRoute($route_name)), "\n";
        echo $route_name = 'update_profile', "\n";
        echo $this->buildRoute($route_name), "\n";
        echo $route_name = 'profile', "\n";
        echo ($this->buildRoute($route_name)), "\n";
        echo $route_name = 'add_post', "\n";
        echo ($this->buildRoute($route_name)), "\n";
        echo $route_name = 'show_post', "\n";
        echo ($this->buildRoute($route_name, array("id" => 25, "post" => 30))), "\n";
        echo $route_name = 'edit_post', "\n";
        echo ($this->buildRoute($route_name, array("id" => 25))), "\n";
        echo '</pre>';
        exit();
    }
}