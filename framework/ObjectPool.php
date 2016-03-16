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
 *
 * @package Framework
 */
abstract class ObjectPool
{
    /**
     * An assoc array.
     *
     * Key contains a class name.
     * Value contains a class instance.
     * @var array
     */
    private static $_loaded_instances = [];

    /**
     * Load the class instance.
     *
     * @param string|null $class_name Full class name if present
     * @param array $args Arguments if present
     *
     * @return object Return class instance
     * @throws \Exception If class does not exist
     */
    public static function get($class_name = null, Array $args = [])
    {
        $name = $class_name ?: get_called_class();

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
     *
     * @param object $instance Singleton instance
     *
     * @return object
     */
    public static function add($instance)
    {
        $instanceReflection = new \ReflectionClass($instance);
        if (empty(self::$_loaded_instances[$instanceReflection->name])) {
            self::$_loaded_instances[$instanceReflection->name] = $instance;
        }
    }
}