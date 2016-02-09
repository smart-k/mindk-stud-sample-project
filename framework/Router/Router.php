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
    protected static $_map = array();

    /**
     * Router constructor.
     * @param array $routing_map Contains preliminary mapped routes.
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

            $pattern = $this->prepare($route);

            if (preg_match($pattern, $uri, $params)) {

                $route_found = $route;

                if (preg_match('~({.+})~Ui', $route['pattern'], $param_name)) // // Get associative array of parameter
                {
                     $param_name = array_map(function ($name) {
                        return trim($name, '{}');
                    }, $param_name);

                    $combine_param = array_combine($param_name, $params);
                    $route_found['parameter'] = $combine_param;
                }


                break;
            }

        }

        return $route_found;
    }

    /**
     * @param $route URI pattern from preliminary mapped route.
     * @return string $pattern Prepared URI pattern with considering of URI pattern requirements.
     */
    private function prepare($route)
    {
        if (empty($route['_requirements']['id'])) {
            $pattern = preg_replace('~\{[\w\d_]+\}~i', '([\w\d_]+)', $route['pattern']);
        } else {
            $pattern = preg_replace('~\{[\w\d_]+\}~i', '(' . $route['_requirements']['id'] . ')', $route['pattern']);
        }

        $pattern = '~^' . $pattern . '$~';

        return $pattern;
    }


    public function buildRoute($route_name, $params = array())
    {
        // @TODO: Your code...
    }

    function test()
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
        echo $path = '/posts/25', "\n";
        print_r($this->parseRoute($path));
        echo $path = '/posts/35/edit', "\n";
        print_r($this->parseRoute($path));
        echo '</pre>';
        exit();
    }

}