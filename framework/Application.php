<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:28
 */

namespace Framework;

use Framework\Router\Router;
use Framework\Response\Response;


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
        try {
            if (!empty($route)) {
                $controllerReflection = new \ReflectionClass($route['controller']);

                if (!$controllerReflection->isSubclassOf('Framework\Controller\Controller')) {
                    throw new \Exception("Unknown controller $controllerReflection");
                }

                $action = $route['action'] . 'Action';

                if ($controllerReflection->hasMethod($action)) {
                    // ReflectionMethod::invokeArgs() has overloaded in class ReflectionMethodNamedArgs
                    // Now it provides invoking with named arguments
                    $actionReflection = new ReflectionMethodNamedArgs($route['controller'], $action);
                    $controller = $controllerReflection->newInstance();
                    $response = $actionReflection->invokeArgs($controller, $route['parameters']);
                    if ($response instanceof Response) {
                        // ...
                        $response->send();
                    } else {
                        throw new BadResponseTypeException('Ooops');
                    }
                }
            } else {
                throw new HttpNotFoundException('Route not found');
            }
        } catch (HttpNotFoundException $e) {
            // Render 404 or just show msg
        } catch (AuthRequredException $e) {
            // Reroute to login page
            //$response = new RedirectResponse(...);
        } catch (\Exception $e) {
            // Do 500 layout...
            echo $e->getMessage();
        }
    }
}

