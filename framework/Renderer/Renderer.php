<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 29.02.2016
 * Time: 9:48
 */

namespace Framework\Renderer;


use Framework\ObjectPool;

/**
 * Class Renderer
 * @package Framework\Renderer
 */
class Renderer extends ObjectPool
{
    /**
     * @var string  Main wrapper template file location
     */
    private $_main_template = '';

    /**
     * Set main wrapper template file location
     * @param $main_template_file
     */
    function setMainTemplate($main_template_file)
    {
        $this->_main_template = $main_template_file;
        return $this;
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
     * @param   string  Template file path (full)
     * @param   mixed   Data array
     * @param   bool    To be wrapped with main template if true
     *
     * @return  text/html
     */
    public function render($template_path, $data = array(), $wrap = true)
    {
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
}