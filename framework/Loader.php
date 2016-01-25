<?php
/**
 * Created by PhpStorm.
 * User: K.Gritsenko
 * Date: 25.01.2016
 * Time: 8:58
 */

//namespace Framework;


class Loader
{
    /**
     * An associative array
     *
     * Key contains a namespace prefix
     * Value contains a base directories for the classes in this namespace
     * @var array
     */
    static private $prefixes = array();

    /**
     * Register the loader on the SPL stack
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));

    }

    /**
     * Adds the base directory for the namespace prefix
     *
     * @param string $prefix Namespace prefix
     * @param string $base_dir Base directory for the class files of the namespace
     * @param bool $first If true, add the base directory to the begin of the array. In this case, it will be checked first
     * @return void
     */
    static function addNamespacePath($prefix, $base_dir, $first = false)
    {
        /**
         * Namespace prefix normalization
         * @var string
         */
        $prefix = trim($prefix, '\\') . '\\';

        /**
         * Base directory normalization
         * @var string
         */
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // Initialize an array of base directories for the classes in this namespace
        if (isset(self::$prefixes[$prefix]) === false) {
            self::$prefixes[$prefix] = array();
        }

        // Adds the base directory to the associative array
        if ($first) {
            array_unshift(self::$prefixes[$prefix], $base_dir);
        } else {
            array_push(self::$prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Loads a file for a given full class name
     *
     * @param string $class Full class name
     * @return mixed If success returns full file name, otherwise returns false
     */
    private function loadClass($class)
    {
        /**
         * Namespace prefix
         * @var string
         */
        $prefix = $class;

        // Walk around the namespace prefix to determine the file name
        while (false !== $pos = strrpos($prefix, '\\')) {

            // Saves namespace prefix with trailing separator
            $prefix = substr($class, 0, $pos + 1);

            // Gets relative class name
            $relative_class = substr($class, $pos + 1);

            // Trying to load the file that matches namespace prefix and relative class name
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // Remove trailing separator for the next iteration of strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        // File not found
        return false;
    }

    /**
     * Loads the file that matches namespace prefix and relative class name
     *
     * @param string $prefix Namespace prefix
     * @param string $relative_class Relative class name
     * @return mixed false if file was not loaded, otherwise returns the name of the loaded file
     */
    private function loadMappedFile($prefix, $relative_class)
    {
        // Checks whether this namespace prefix has any base directory
        if (isset(self::$prefixes[$prefix]) === false) {
            return false;
        }

        // Looks for a file in the base directories
        foreach (self::$prefixes[$prefix] as $base_dir) {

            // Substitutes namespace prefix for base directory prefix
            // Substitutes namespace separators for directory separators
            // Adds .php to relative class name
            $file = $base_dir
                . str_replace('\\', '/', $relative_class)
                . '.php';

            // If file exists - load it
            if ($this->requireFile($file)) {
                return $file;
            }
        }

        // File not found
        return false;
    }
    /**
     * If file exists then loads it
     *
     * @param string $file File to load
     * @return bool true if file exists, otherwise - false
     */
    private function requireFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }
}

// Creates an instance of the Loader
$loader = new Loader();

// Register the loader
$loader->register();

// Register base directory for a namespace prefix Framework\ ..
Loader::addNamespacePath('Framework\\' , __DIR__ . '/../framework' );