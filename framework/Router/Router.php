<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 27.01.2016
 * Time: 10:10
 */

namespace Framework\Router;

use Framework\Application;
use Framework\Singleton;


class Router extends Singleton
{
    /* private $path_elements = array('controller', 'action', 'id');

     function parse($path)
     {
         $request = $_REQUEST;

 //        $request['controller'] = Application::getInstance()->config->default_controller;
         $request['controller'] ='Blog\\Controller\\UserController';
 //        $request['action'] = Application::getInstance()->config->default_action;
         $request['action'] = 'index';
         $request['id'] = 0;
         $parts = parse_url($path);
         if (isset($parts['query']) && !empty($parts['query'])) {
             $path = str_replace('?' . $parts['query'], '', $path);
             parse_str($parts['query'], $req);
             $request = array_merge($request, $req);
         }
         foreach (Application::getInstance()->config->router as $rule => $keypath) {
             if (preg_match('#' . $rule . '#sui', $path, $list)) {
                 for ($i = 1; $i < count($list); $i = $i + 1) {
                     $keypath = preg_replace('#\$[a-z0-9]+#', $list[$i], $keypath, 1);
                 }
                 $keypath = explode('/', $keypath);
                 foreach ($keypath as $i => $key) {
                     $request[$this->path_elements[$i]] = $key;
                 }
             }
         }

         return $request;*/
    public $action = 'index';
    public $controller = false;

    function parse()
    {
        if (isset($_REQUEST['controller']))
            $this->controller = $_REQUEST['controller'];
        if (isset($_REQUEST['action']))
            $this->action = $_REQUEST['action'];
    }


/*function test()
{
    echo $path = '/user/', "\n";
    print_r($this->parse($path));
    echo $path = '/user/login/', "\n";
    print_r($this->parse($path));
    echo $path = '/user/profile/15', "\n";
    print_r($this->parse($path));
    echo $path = 'about.html', "\n";
    print_r($this->parse($path));
    echo $path = 'about.html?lenta=1#1', "\n";
    print_r($this->parse($path));
    exit();
}*/
}