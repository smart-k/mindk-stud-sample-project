<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 01.03.2016
 * Time: 9:10
 */

namespace Framework\DI;


/**
 * Class Service
 *
 * @package Framework
 */
class Service
{
    /**
     * @var array   Services
     */
    protected static $_services = array();

    /**
     * Setting the service
     *
     * @param $name
     * @param mixed $object
     */
    public static function set($name, $object){

        self::$_services[$name] = $object;
    }

    /**
     * Getting the service
     *
     * @param $name
     * @return object|null
     */
    public static function get($name){

        return array_key_exists($name, self::$_services) ? self::$_services[$name] : null;
    }
}