<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 25.01.2016
 * Time: 18:58
 */

namespace Framework;

use Framework\Router\Router;

class Application extends Singleton
{
   /* public $config = null;
    public $uri = null;*/

    public function __construct($app_config)
    {

//        $this->initSystemHandlers();
        $this->config = new Registry($app_config);

//        include IDEAL . 'classes/adapter/db.php';
//        $this->db = new db();
//        $this->db->connect($this->config->db);
    }

    function run()
    {
        Router::getInstance()->parse();
        $controller = Application::getInstance(Router::getInstance()->controller.'Controller');
        $controller->__call('action'.Router::getInstance()->action);

       /* $this->uri = new Registry(Router::getInstance()->parse($_SERVER['REQUEST_URI']));
        $controller = Application::getInstance($this->uri->controller);

        ob_start(); // Enables output buffering
        $controller->__call('action' . $this->uri->action, array($this->uri->id));
        $content = ob_get_clean(); // Gets current buffer contents and deletes it*/

        /*if ($this->config->scripts and is_array($this->config->scripts)) {
            foreach ($this->config->scripts as $script) {
                $controller->addScript($script);
            }
        }
        if ($this->config->styles and is_array($this->config->styles)) {
            foreach ($this->config->styles as $style) {
                $controller->addStyleSheet($style);
            }
        }
        $controller->renderPage($content);*/
    }

   /* protected function initSystemHandlers()
    {
        set_exception_handler(array($this, 'handleException'));
        set_error_handler(array($this, 'handleError'), error_reporting());
    }

    public function handleError($code, $message, $file, $line)
    {
        if ($code & error_reporting()) {
            restore_error_handler();
            restore_exception_handler();
            try {
                $this->displayError($code, $message, $file, $line);
            } catch (Exception $e) {
                $this->displayException($e);
            }
        }
    }

    public function handleException($exception)
    {
        restore_error_handler();
        restore_exception_handler();
        $this->displayException($exception);
    }

    public function displayError($code, $message, $file, $line)
    {
        echo "<h1>PHP Error [$code]</h1>\n";
        echo "<p>$message ($file:$line)</p>\n";
        echo '<pre>';

        $trace = debug_backtrace();

        if (count($trace) > 3)
            $trace = array_slice($trace, 3);

        foreach ($trace as $i => $t) {
            if (!isset($t['file']))
                $t['file'] = 'unknown';
            if (!isset($t['line']))
                $t['line'] = 0;
            if (!isset($t['function']))
                $t['function'] = 'unknown';
            echo "#$i {$t['file']}({$t['line']}): ";
            if (isset($t['object']) && is_object($t['object']))
                echo get_class($t['object']) . '->';
            echo "{$t['function']}()\n";
        }

        echo '</pre>';
        exit();
    }

    public function displayException($exception)
    {
        echo '<h1>' . get_class($exception) . "</h1>\n";
        echo '<p>' . $exception->getMessage() . ' (' . $exception->getFile() . ':' . $exception->getLine() . ')</p>';
        echo '<pre>' . $exception->getTraceAsString() . '</pre>';
    }*/
}