<?php

/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 25.01.2016
 * Time: 8:58
 */
use Framework\ObjectPool;

/**
 * Class Loader
 * Autoloads classes from namespace's base directories.
 */
class Loader
{
    /**
     * An associative array.
     *
     * Key contains a namespace.
     * Value contains a base directories for the classes in this namespace.
     * @var array
     */
    static private $_namespaces = array();

    /**
     * Keeps instance of the class Loader.
     * @var Loader|object
     */
    static private $_instance = null;

    /**
     * @return Loader|object
     */
    static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Loader constructor.
     * Registers autoloader in the stack SPL.
     */
    private function __construct()
    {
        spl_autoload_register(array(__CLASS__, '_load'));
    }

    final private function __clone() // We do not need to copy objects so lock it.
    {
    }

    /**
     * Adds the base directory for the namespace.
     *
     * @param string $namespace Namespace prefix.
     * @param string $base_dir Base directory for the class files of the namespace.
     * @param bool $first If true, add the base directory to the begin of the array. In this case, it will be checked first.
     * @return void
     */
    static function addNamespacePath($namespace, $base_dir, $first = false)
    {
        /**
         * Namespace normalization.
         * @var string
         */
        $namespace = trim($namespace, '\\') . '\\';

        /**
         * Base directory normalization.
         * @var string
         */
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // Initialize an array of base directories for the classes in this namespace.
        if (empty(self::$_namespaces[$namespace]) === true) {
            self::$_namespaces[$namespace] = array();
        }

        // Adds the base directory to the associative array.
        if ($first) {
            array_unshift(self::$_namespaces[$namespace], $base_dir);
        } else {
            array_push(self::$_namespaces[$namespace], $base_dir);
        }
    }

    /**
     * Gets a directory path for a given full class name.
     * @param string $class Full class name.
     * @return mixed If success returns directory path, otherwise returns false.
     */
    function getPath($class)
    {
        return $this->_load($class, true);
    }

    /**
     * Loads a file for a given full class name.
     *
     * @param string $class Full class name.
     * @param boolean $get_only_path If true - only return path for file to be loaded
     * @return mixed If success returns full file name, otherwise returns false.
     */
    private function _load($class, $get_only_path = false)
    {
        /**
         * Namespace.
         * @var string
         */
        $namespace = $class;

        // Work around the namespace to determine the file name.
        while (false !== $pos = strrpos($namespace, '\\')) {

            // Saves namespace prefix with separator on the end.
            $namespace = substr($class, 0, $pos + 1);

            // Gets relative class name.
            $relative_class = substr($class, $pos + 1);

            // Trying to load the file that matches namespace prefix and relative class name.
            $file = self::_loadFile($namespace, $relative_class, $get_only_path);
            if ($file) {
                if ($get_only_path == true) {
                    $pos = strrpos($file, '/');
                    $file = substr($file, 0, $pos + 1);
                }
                return $file;
            }

            // Remove separator on the end for the next iteration.
            $namespace = rtrim($namespace, '\\');
        }
    }


    /**
     * Loads the file that matches namespace prefix and relative class name.
     *
     * @param string $namespace Namespace.
     * @param string $relative_class Relative class name.
     * @param boolean $get_only_path If true - only return path for file to be loaded
     * @return mixed False if file was not loaded, otherwise returns the name of the loaded file.
     */
    private function _loadFile($namespace, $relative_class, $get_only_path)
    {
        // Checks whether this namespace prefix has any base directory.
        if (empty(self::$_namespaces[$namespace]) === true) {
            return false;
        }

        // Looks for a file in the base directories.
        foreach (self::$_namespaces[$namespace] as $base_dir) {

            // Substitutes namespace prefix for base directory prefix.
            // Substitutes namespace separators for directory separators.
            // Adds .php to relative class name.
            $file = $base_dir
                . str_replace('\\', '/', $relative_class)
                . '.php';

            // If file exists - load it.
            if ($this->_includeFile($file, $get_only_path)) {
                return $file;
            }
        }

        // File not found.
        return false;
    }

    /**
     * If file exists then loads it.
     *
     * @param string $file File to load
     * @param boolean $get_only_path If true - only return path for file to be loaded
     * @return bool True if file exists, otherwise - false
     */
    private function _includeFile($file, $get_only_path)
    {
        if (file_exists($file)) {

            if ($get_only_path == false) {
                include_once $file;
            }
            return true;
        }
        return false;
    }
}

// Register base directory for namespace prefix Framework\ ..
Loader::addNamespacePath('Framework\\', __DIR__);
$instance = Loader::getInstance();
// Register autoloader.
// Add Loader instance to the Singleton::_loaded_instances
ObjectPool::addInstance($instance);

