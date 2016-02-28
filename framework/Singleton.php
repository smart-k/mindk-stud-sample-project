<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 08.02.2016
 * Time: 11:07
 */

namespace Framework;

/**
 * Class Singleton
 * Loads different components via the Object pool pattern
 * @package Framework
 */
abstract class Singleton
{
    /**
     * An associative array.
     *
     * Key contains a class name.
     * Value contains a class instance.
     * @var array
     */
    private static $_loaded_instances = array();

    /**
     * Loads the class instance.
     *
     * @param string $class_name
     * @return object
     *
     */
    public static function getInstance($class_name = false)
    {
        $name = ($class_name === false) ? get_called_class() : $class_name;

        if (class_exists($name)) {
            if (empty(self::$_loaded_instances[$name])) {
                self::$_loaded_instances[$name] = new $name();
            }
            return self::$_loaded_instances[$name];
        } else {
            throw new \Exception('Class '.$name.'  not exist!');
        }
    }
}