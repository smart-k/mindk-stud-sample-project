<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 27.01.2016
 * Time: 12:13
 */

namespace Framework\Controller;


use Framework\Singleton;

class Controller extends Singleton
{
    function __call( $methodName,$args=array() ){
        if( is_callable( array($this,$methodName) ) )
            return call_user_func_array(array($this,$methodName),$args);
        /*else
            throw new Except('In controller '.get_called_class().' method '.$methodName.' not found!');*/
    }
}