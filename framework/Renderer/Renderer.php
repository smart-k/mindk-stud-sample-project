<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 29.02.2016
 * Time: 9:48
 */

namespace Framework\Renderer;


use Framework\ObjectPool;
use Framework\ReflectionMethodNamedArgs;
use Framework\Response\Response;
use Framework\Exception\BadResponseTypeException;

/**
 * Class Renderer
 * @package Framework\Renderer
 */
class Renderer extends ObjectPool
{
    /**
     * @var string  Main wrapper template file location
     */
    protected $_main_template = '';
    protected $_error_template = '';

    /**
     * @return string Path to error template directory
     */
    public function getErrorTemplatePath()
    {
        $pos = strrpos($this->_error_template, '/');
        $template_path = substr($this->_error_template, 0, $pos + 1);
        return $template_path;
    }

    /**
     * Renderer constructor.
     * @param array $config
     */
    public function __construct($config = array())
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
    function renderMain($content)
    {

        //@TODO: set all required vars and closures..

        return $this->render($this->_main_template, compact('content'), false);
    }

    /**
     * Render specified template file with data provided
     *
     * @param   string $template_path Template file path (full)
     * @param   array  $data Data array
     * @param   bool    To be wrapped with main template if true
     *
     * @return  text/html $content
     * @throws \Exception If template file not found
     */
    public function render($template_path, $data = array(), $wrap = true)
    {
        $data['include'] = '$this->_widget';
        extract($data);
        // @TODO: provide all required vars or closures...
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

    /**
     * Widget for view template
     *
     * @param string $controller_name
     * @param string $action
     * @param array $data
     *
     * @throws \Exception If obtained controller is not subclass of base controller.
     */
    private function _widget($controller_name, $action, $data = array())
    {
        $controllerReflection = new \ReflectionClass($controller_name);
        if (!$controllerReflection->isSubclassOf('Framework\Controller\Controller')) {
            throw new \Exception("Unknown controller " . $controllerReflection->name);
        }
        $action .= 'Action';
        if ($controllerReflection->hasMethod($action)) {
            // ReflectionMethod::invokeArgs() has overloaded in class ReflectionMethodNamedArgs
            // Now it provides invoking with named arguments
            $actionReflection = new ReflectionMethodNamedArgs($controller_name, $action);
            $controller = $controllerReflection->newInstance();
            $response = $actionReflection->invokeArgs($controller, $data);
            if ($response instanceof Response) {
                echo htmlspecialchars_decode($response['content']);
            } else {
                throw new BadResponseTypeException('Response type not known');
            }
        }
    }
}