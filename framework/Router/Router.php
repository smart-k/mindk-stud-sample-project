<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:04
 */

namespace Framework\Router;

use Framework\Singleton;


/**
 * Class Router
 * @package Framework\Router
 */
class Router extends Singleton
{

    /**
     * An associative array.
     * Contains preliminary mapped static routes.
     *
     * @var array
     */
    private static $_map = array();

    /**
     * Set preliminary mapped static routes from config
     *
     * @param array $routing_map Contains preliminary mapped static routes from config
     */
    public function setRoutingMap($routing_map = array())
    {
        self::$_map = $routing_map;
        return $this;
    }

    /**
     * Parces route.
     *
     * @param $url
     * @return array $route_found An associative array of route's parameters.
     */
    public function parseRoute($uri = null)
    {
        $route_found = null;

        $uri = empty($uri) ? $_SERVER['REQUEST_URI'] : $uri;
        $uri = '/' . trim($uri, '/'); // Unified standard: /profile

        foreach (self::$_map as $route) {
            $param_names = $this->_parseParameterName($route); // Parse parameter's names from config's route pattern
            $pattern = $this->_prepare($route, $param_names); // Prepare root: taking into account pattern requirements for route parameters

            if (preg_match_all($pattern, $uri, $params)) {

                $route_found = $route;

                if ($route['pattern'] == '/profile') {
                    // Taking into account requirements for the type of HTTP request
                    $route_found = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $route_found = self::$_map['update_profile'] : $route_found = self::$_map['profile'];
                }

                $route_found['parameters'] = array();

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
    private function _parseParameterName($route)
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
    private function _prepare($route, $names)
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

    /**
     * Build route.
     *
     * @param string $route_name
     * @param array $params
     * @return string|null
     */
    public function buildRoute($route_name, $params = null)
    {
        $route_build = !empty(Router::$_map[$route_name]['pattern']) ? Router::$_map[$route_name]['pattern'] : null;

        if ($route_build && !empty($params)) {
            foreach ($params as $key => $value) {
                $route_build = preg_replace('~\{' . $key . '\}~', $value, $route_build);
            }
        }
        return $route_build;
    }
}