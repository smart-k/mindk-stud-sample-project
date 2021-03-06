<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:28
 */

namespace Framework;

use Framework\Controller\Controller;
use Framework\Response\Response;
use Framework\Response\ResponseRedirect;
use Framework\Exception\BadResponseTypeException;
use Framework\Exception\HttpNotFoundException;
use Framework\Exception\AuthRequredException;
use Framework\DI\Service;



/**
 * Class Application
 * Front Controller pattern implemented.
 * @package Framework
 */
class Application extends Controller
{
    function run()
    {
        $this->_setServices();

        $route = Service::get('router')->parseRoute();
        try {
            if (!empty($route)) {
                $controllerReflection = new \ReflectionClass($route['controller']);

                if (!$controllerReflection->isSubclassOf('Framework\Controller\Controller')) {
                    throw new \Exception("Unknown controller " . $controllerReflection->name);
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
                    } else {
                        throw new BadResponseTypeException('Response type not known');
                    }
                }
            } else {
                throw new HttpNotFoundException('Route not found');
            }
        } catch (HttpNotFoundException $e) {
            $code = (string)$e->getCode();
            $response = $this->render($code . '.html', array('code' => $code, 'message' => $e->getMessage())); // Render 404
        } catch (AuthRequredException $e) {
            $response = new ResponseRedirect($this->generateRoute('login')); // Reroute to login page
        } catch (\Exception $e) {
            $code = '500';
            $response = $this->render($code . '.html', array('code' => (string)$e->getCode(), 'message' => $e->getMessage())); // Do 500 layout...
        }
        $response->send();
    }

    /**
     * Set required services
     */
    private function _setServices()
    {
        Service::set('router', ObjectPool::get('Framework\Router\Router', include(__DIR__ .'/../app/config/routes.php')));
        Service::set('loader', ObjectPool::get('Loader'));
        Service::set('renderer', ObjectPool::get('Framework\Renderer\Renderer', include(__DIR__ . '/../app/config/config.php')));
    }

}

