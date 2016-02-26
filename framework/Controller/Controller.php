<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 23.02.2016
 * Time: 15:22
 */

namespace Framework\Controller;

use Framework\Response\ResponseRedirect;
use Framework\Router\Router;
use Framework\Response\Response;


class Controller
{
    public $tplPath;
    public $tplControllerPath;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->tplPath = __DIR__ . '/../../src/Blog/views/';
        $this->tplControllerPath = __DIR__ . '/../../src/Blog/views/' . str_replace('Controller', '', get_called_class()) . '/';
    }

    function __call($methodName, $args = array())
    {
        if (method_exists($this, $methodName))
            return call_user_func_array(array($this, $methodName), $args);
        else
            throw new \Exception('In controller ' . get_called_class() . ' method ' . $methodName . ' not found!');
    }

    /**
     * Redirects to another url.
     * Can redirect via a Location header
     *
     * @param   string $url The url
     * @param   string $content The response content
     * @param   int $code The redirect status code
     * @return  object
     */
    public static function redirect($url, $content = '', $code = 302)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        return new ResponseRedirect($url, $content, $code);
    }

    /**
     * Generate route.
     *
     * @param string $route_name
     * @param array $params
     * @return string|null
     */
    public function generateRoute($route_name, $params = null)
    {
        $route_generated = !empty(Router::$_map[$route_name]['pattern']) ? Router::$_map[$route_name]['pattern'] : null;

        if ($route_generated && !empty($params)) {
            foreach ($params as $key => $value) {
                $route_generated = preg_replace('~\{' . $key . '\}~', $value, $route_generated);
            }
        }
        return $route_generated;
    }

    private function _renderPartial($file, $variables = array(), $output = true)
    {
        extract($variables);

        if (file_exists($file)) {
            if (!$output)
                ob_start();
            include $file;
            return !$output ? ob_get_clean() : true;
        } else
            throw new \Exception('File ' . $file . ' not found');
    }

    /**
     * renderPartial Available in the controller to display the template file.
     *
     * @params $filename Template name in the folder views / controller name / {}. php
     * @params $variables Array keys will be available in the template as variables with the same name
     * @params $output - If you specify false, the data from the template will be displayed in the main stream
     */
    public function renderPartial($filename, $variables = array(), $output = true)
    {
        $file = $this->tplControllerPath . $filename . '.php';
        return $this->_renderPartial($file, $variables, $output);
    }

    /**
     * Performs the complete withdrawal of the page to the screen.
     * Thus, it includes the contents of the template file $filename
     *
     * @params $filename Template name in the folder views / controller name / {}. php
     * @params $variables Array keys will be available in the template as variables with the same name
     * @params $output - If you specify false, the data from the template will be displayed in the main stream

     */
    public function render($filename, $variables = array(), $output = true)
    {
        $this->renderPartial($filename, $variables, $output);
    }

    /**
     * Performs the complete withdrawal of the error page to the screen.
     */
    public function renderError($variables = array())
    {
        $html = $this->_renderPartial($this->tplPath . $variables['code'] . '.html.php', $variables, false);
        echo $html;
        return new Response($variables['message'], $variables['code']);
    }
}