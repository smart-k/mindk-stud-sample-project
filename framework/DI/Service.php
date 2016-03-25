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
 * Service Locator pattern has been implemented.
 *
 * @package Framework
 */
class Service
{
    /**
     * @var array The services
     */
    protected static $_services = [];

    /**
     * Set the service
     *
     * @param $name
     * @param mixed $service
     */
    public static function set($name, $service)
    {
        self::$_services[$name] = $service;
    }

    /**
     * Get the service
     *
     * @param $name
     * @return object|null
     */
    public static function get($name)
    {
        return array_key_exists($name, self::$_services) ? self::$_services[$name] : null;
    }
}