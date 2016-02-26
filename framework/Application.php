<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:28
 */

namespace Framework;

use Framework\Controller\Controller;
use Framework\Router\Router;
use Framework\Response\Response;
use Framework\Response\ResponseRedirect;
use Framework\Exception\BadResponseTypeException;
use Framework\Exception\HttpNotFoundException;
use Framework\Exception\AuthRequredException;

/**
 * Class Application
 * Front Controller pattern implemented.
 * @package Framework
 */
class Application extends Controller
{
    public function run()
    {
        $router = new Router(include('../app/config/routes.php'));
        $route = $router->parseRoute();
        try {
            if (!empty($route)) {
                $controllerReflection = new \ReflectionClass($route['controller']);

                if (!$controllerReflection->isSubclassOf('Framework\Controller\Controller')) {
                    throw new \Exception("Unknown controller " . $controllerReflection->name);
                }

                $action = $route['action'] . 'Action';

                if ($controllerReflection->hasMethod($action)) {
                    // ReflectionMethod::invokeArgs() has overloaded in class ReflectionMet  hodNamedArgs
                    // Now it provides invoking with named arguments
                    $actionReflection = new ReflectionMethodNamedArgs($route['controller'], $action);
                    $controller = $controllerReflection->newInstance();
                    $response = $actionReflection->invokeArgs($controller, $route['parameters']);
                    if ($response instanceof Response) {
                        $response->send();
                    } else {
                        throw new BadResponseTypeException('Response type not known');
                    }
                }
            } else {
                throw new HttpNotFoundException('Route not found');
            }
        } catch (HttpNotFoundException $e) {
            $this->renderError(array('code' => '404', 'message' => $e->getMessage())); // Render 404
        } catch (AuthRequredException $e) {
            $response = new ResponseRedirect($this->generateRoute('login')); // Reroute to login page
            $response->send();
        } catch (\Exception $e) {
            $this->renderError(array('code' => '500', 'message' => $e->getMessage())); // // Do 500 layout...
        }
    }

}

