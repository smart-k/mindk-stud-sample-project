<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 29.02.2016
 * Time: 9:48
 */

namespace Framework\Renderer;

use Framework\ObjectPool;
use Framework\Response\Response;
use Framework\Exception\BadResponseTypeException;
use Framework\DI\Service;

/**
 * Class Renderer
 *
 * @package Framework\Renderer
 */
class Renderer extends ObjectPool
{
    /**
     * @var string  The file location of the main wrapper template
     */
    protected $_main_template = '';

    /**
     * @var string  The file location of the error template
     */
    protected $_error_template = '';

    /**
     * Get path to the error template
     *
     * @return string The error template directory location
     */
    public function getErrorTemplatePath()
    {
        $pos = strrpos($this->_error_template, '/');
        $template_path = substr($this->_error_template, 0, $pos + 1);
        return $template_path;
    }

    /**
     * Renderer constructor.
     *
     * @param array $config
     */
    public function __construct(Array $config = [])
    {
        $this->_main_template = $config['main_layout'];
        $this->_error_template = $config['error_500'];
    }

    /**
     * Render main wrapper template with specified content
     *
     * @param $content
     *
     * @return html/text
     */
    public function renderMain($content)
    {
        $user = Service::get('session')->getUser(); // Get the user data that are stored in the session.
        $flush = Service::get('session')->getFlush(); // Get the flush messages that are stored in the session.
        Service::get('session')->clearFlush();
        return $this->render($this->_main_template, compact('user', 'flush', 'content'), false);
    }

    /**
     * Render specified template with data provided
     *
     * @param   string $template_path Template full path
     * @param   array $data Data
     * @param   bool    To be wrapped with main template if true
     *
     * @return  text/html $content
     * @throws \Exception If template not found
     */
    public function render($template_path, Array $data = [], $wrap = true)
    {
        /**
         * Closure for ../src/views/Post/index.html.php template
         *
         * @param string $controller_name
         * @param string $action
         * @param array $data
         *
         * @throws \Exception If obtained response is not instance of Response.
         */
        $include = function ($controller_name, $action, Array $data = []) {

            $response = Service::get('application')->getActionResponse($controller_name, $action, $data);

            if ($response instanceof Response) {
                echo htmlspecialchars_decode($response->content);
            } else {
                throw new BadResponseTypeException('Response type not known');
            }
        };

        /**
         * Closure for login.html.php, signin.html.php and layout.html.php templates
         *
         * @param string $route_name
         * @param array|null $params
         *
         * @return string|null
         */
        $getRoute = function ($route_name, Array $params = []) {
            return Service::get('router')->buildRoute($route_name, $params);
        };

        $token = !empty(Service::get('session')->getToken()) ? Service::get('session')->getToken() : null;

        /**
         * Closure for add.html.php, login.html.php, signin.html.php templates
         * Set token into hidden input field on form template
         */
        $generateToken = function () use ($token) {
            echo '<input type="hidden" name="token" value=' . $token . ' >';
        };

        extract($data);

        if (!empty(Service::get('session')->getPost())) { // Show filled post fields when validation failed
            $post = Service::get('session')->getPost();
            Service::get('session')->clearPost();
        }

        if (file_exists($template_path)) {
            ob_start();
            include($template_path);
            $content = ob_get_clean();
        } else {
            throw new \Exception('File ' . $template_path . ' not found');
        }

        if ($wrap) {
            if (file_exists($this->_main_template)) {
                $content = $this->renderMain($content);
            } else {
                throw new \Exception('File ' . $this->_main_template . ' not found');
            }
        }

        return $content;
    }
}