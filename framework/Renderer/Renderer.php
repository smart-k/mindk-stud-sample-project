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
     * @var string  Main wrapper template file location
     */
    protected $_main_template = '';

    /**
     * @var string  Error template file location
     */
    protected $_error_template = '';

    /**
     * @return string Error template directory location
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
     * Render main template with specified content
     *
     * @param $content
     *
     * @return html/text
     */
    public function renderMain($content)
    {
        $flush = Service::get('session')->messages ?: [];

        if (isset($flush)) {
            Service::get('session')->unset_data(array('messages'));
        }

        return $this->render($this->_main_template, compact('flush', 'content'), false);
    }

    /**
     * Render specified template with data provided
     *
     * @param   string $template_path Template path (full)
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
         * @return string|null
         */
        $getRoute = function ($route_name, Array $params = []) {
            return Service::get('router')->buildRoute($route_name, $params);
        };

        $token = Service::get('session')->_token ?: null;

        /**
         * Closure for add.html.php, login.html.php, signin.html.php templates
         * Set token into hidden input field on form template
         */
        $generateToken = function () use ($token) {
            echo '<input type="hidden" name="token" value=' . $token . ' >';
        };

        $user = Service::get('security')->getUser(); // Get the user data that are stored in the session.

        extract($data);

        if (Service::get('session')->post) { // Show filled post fields when validation failed
            $post = unserialize(Service::get('session')->post);
            unset(Service::get('session')->post);
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