<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:04
 */

namespace Framework\Router;


use Framework\Factory;

/**
 * Class Router
 * @package Framework\Router
 */
class Router
{

    /**
     * An associative array.
     * Contains mapped routes.
     *
     * @var array
     */
    protected static $_map = array();

    /**
     * Router constructor.
     * @param array $routing_map Mapped routes from config file.
     */
    public function __construct($routing_map = array())
    {

        self::$_map = $routing_map;
    }

    /**
     * Parces route.
     *
     * @param $url
     * @return array $request
     */
    public function parseRoute($url)
    {

        $request = $_REQUEST;

        echo "<pre>";

        foreach (self::$_map as $route) {

            $pattern = $this->prepare($route);

            echo $pattern . '<br />';

            /*if (preg_match($pattern, $url, $params)) {

                // Get assoc array of params:
                preg_match($pattern, str_replace(array('{', '}'), '', $route['pattern']), $param_names);
                $params = array_map('urldecode', $params);
                $params = array_combine($param_names, $params);
                array_shift($params); // Get rid of 0 element

                $route_found = $route;
                $route_found['params'] = $params;

                break;
            }*/

        }

        return $request;
    }

    public function buildRoute($route_name, $params = array())
    {
        // @TODO: Your code...
    }

    private function prepare($route)
    {

        $pattern = preg_replace('~\{[\w\d_]+\}~i', '([\w\d_]+)', $route['pattern']);

        $pattern = '~^' . $pattern . '$~';

        return $pattern;
    }
}