<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 25.01.2016
 * Time: 18:40
 */

namespace Framework;


abstract class Singleton
{
    /**
     * An associative array
     *
     * Key contains a class name
     * Value contains an instance of the named class
     *
     * Keeps instances of all classes that inherit class Singleton
     * @var array
     */
    private static $instances = array();

    /**
     * Returns an instance of class by its name
     * If class name is not specified, returns an instance of calling class
     *
     * @param string $class_name
     * @return object
     */
    public static function getInstance($class_name = false)
    {
        $str_class_name = ($class_name === false) ? get_called_class() : $class_name;

        if (class_exists($str_class_name)) {
            if (!isset(self::$instances[$str_class_name]))
                self::$instances[$str_class_name] = new $str_class_name();
            return self::$instances[$str_class_name];
        }
// TODO trown an exception "Class does not exists"
    }

    final private function __clone() // We do not need to copy objects
    {
    }

    private function __construct()
    {
    }
}