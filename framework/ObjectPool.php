<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 08.02.2016
 * Time: 11:07
 */

namespace Framework;

/**
 * Class ObjectPool
 * Load different components via the Object pool pattern
 * @package Framework
 */
abstract class ObjectPool
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
     * Load the class instance.
     *
     * @param boolean|string $class_name Full class name if present
     * @param array $args Arguments if present
     * @return object Return class instance
     * @throws \Exception If class does not exist
     */
    static function get($class_name = false, $args = array())
    {
        $name = ($class_name === false) ? get_called_class() : $class_name;

        if (class_exists($name)) {
            if (empty(self::$_loaded_instances[$name])) {
                self::$_loaded_instances[$name] = new $name($args);
            }
            return self::$_loaded_instances[$name];
        } else {
            throw new \Exception('Class ' . $name . '  not exist!');
        }
    }

    /**
     * Add already created singleton instance into array $_loaded_instances
     * @param object $instance Singleton instance
     * @return object
     */
    static function add($instance)
    {
        $instanceReflection = new \ReflectionClass($instance);
        if (empty(self::$_loaded_instances[$instanceReflection->name])) {
            self::$_loaded_instances[$instanceReflection->name] = $instance;
        }
    }
}