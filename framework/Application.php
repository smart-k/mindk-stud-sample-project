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
        $route = $router->parseRoute();
        if (!empty($route)) {
// @TODO: Invoke appropriate controller and action
        } else {
// @TODO: Throw an exception
        }
    }
}

