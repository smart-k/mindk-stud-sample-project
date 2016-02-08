<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:28
 */

namespace Framework;

use Framework\Router\Router;


/**
 * Class Application
 * Front Controller pattern implemented.
 * @package Framework
 */
class Application
{

    public function run()
    {
        $router = new Router(include('../app/config/routes.php'));

        $route = $router->parseRoute($_SERVER['REQUEST_URI']);

        if (!empty($route)) {

        } else {
// @TODO: Trow an exception
        }

        echo '<pre>';
        print_r($route);
    }
}