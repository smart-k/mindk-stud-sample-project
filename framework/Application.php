<?php
/**
 * Created by PhpStorm.
 * User: user_pc
 * Date: 08.02.2016
 * Time: 12:28
 */

namespace Framework;

use Framework\Controller\Controller;
use Framework\DI\Service;
use Framework\Response\Response;
use Framework\Response\ResponseRedirect;
use Framework\Exception\BadResponseTypeException;
use Framework\Exception\AuthRequiredException;
use Framework\Exception\HttpNotFoundException;
use Framework\Exception\DatabaseException;


/**
 * Class Application
 * Front Controller pattern has been implemented.
 *
 * @package Framework
 */
class Application extends Controller
{
    /**
     * Application constructor.
     * Set required services.
     *
     * @param string $config_path
     */
    public function __construct($config_path)
    {
        Service::set('config', include($config_path));

        if (Service::get('config')['mode'] === 'prod') {
            ini_set('php_flag display_errors', 'off');
        }

        Service::set('router', ObjectPool::get('Framework\Router\Router', Service::get('config')['routes']));
        Service::set('loader', ObjectPool::get('Loader'));
        Service::set('renderer', ObjectPool::get('Framework\Renderer\Renderer', Service::get('config')));
        Service::set('request', ObjectPool::get('Framework\Request\Request'));
        Service::set('security', ObjectPool::get('Framework\Security\Security'));
        Service::set('session', ObjectPool::get('Framework\Session\Session'));


        extract(Service::get('config')['pdo']);
        $dns .= ';charset=latin1';
        $db = new \PDO($dns, $user, $password);

        Service::set('db', $db);
        Service::set('application', $this);
    }

    /**
     * @param string $user_session_name Name for user data saved in session
     */
    public function run($user_session_name = 'user')
    {
        $route = Service::get('router')->parseRoute();

        try {
            if (!empty($route)) {

                if (isset($_SESSION[$user_session_name])) { // Check user role on the basis of user data saved in session
                    $user_role = is_array($_SESSION[$user_session_name]) ? $_SESSION[$user_session_name]['role'] : $_SESSION[$user_session_name]->role;
                }

                if (empty($route['security']) || !empty($route['security']) && $user_role === $route['security'][0]) {

                    $response = $this->getActionResponse($route['controller'], $route['action'], $route['parameters']);

                    if ($response instanceof Response) {
                        // ...
                    } else {
                        throw new BadResponseTypeException('Response type not known');
                    }
                } else {
                    Service::get('session')->returnUrl = $route['pattern'];
                    throw new AuthRequiredException();
                }
            } else {
                throw new HttpNotFoundException('Route not found');
            }
        } catch (HttpNotFoundException $e) {
            $code = (string)$e->getCode();
            $response = $this->render($code . '.html', array('code' => $code, 'message' => $e->getMessage())); // Render 404
        } catch (AuthRequiredException $e) {
            $response = new ResponseRedirect($this->generateRoute('login')); // Reroute to login page
        } catch (DatabaseException $e) {
            $code = '500';
            $response = $this->render($code . '.html', array('code' => (string)$e->getCode(), 'message' => $e->getMessage())); // Render 500
        } catch (\Exception $e) {
            $code = '500';
            $response = $this->render($code . '.html', array('code' => (string)$e->getCode(), 'message' => $e->getMessage())); // Render 500
        }
        $response->send();
    }

    /**
     * Invoke obtained controller action with parameters via Reflection and return controller response.
     *
     * @param string $controller_name
     * @param string $action
     * @param array $data
     *
     * @return Response|null
     * @throws \Exception If obtained controller is not subclass of Controller class
     */
    public function getActionResponse($controller_name, $action, Array $data = [])
    {
        $action .= 'Action';

        $controllerReflection = new \ReflectionClass($controller_name);

        if (!$controllerReflection->isSubclassOf('Framework\Controller\Controller')) {
            throw new \Exception("Unknown controller " . $controllerReflection->name);
        }

        if ($controllerReflection->hasMethod($action)) {
            // ReflectionMethod::invokeArgs() has been overloaded in class ReflectionMethodNamedArgs
            // Now it provides controller action invoking with named arguments
            $actionReflection = new ReflectionMethodNamedArgs($controller_name, $action);
            $controller = $controllerReflection->newInstance();
            $response = $actionReflection->invokeArgs($controller, $data);
            return $response;
        }
        return null;
    }
}

