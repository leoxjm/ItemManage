<?php

///**
// * Registering an autoloader
// */
//$loader = new \Phalcon\Loader();
//
//$loader->registerDirs(
//    [
//        $config->application->modelsDir
//    ]
//)->register();


namespace app;

/**
 * Autoload.
 */
class Autoloader
{
    /**
     * Autoload root path.
     *
     * @var string
     */
    protected static $_autoloadRootPath = '';


    protected static $_loadedFiles = array();

    /**
     * Set autoload root path.
     *
     * @param string $root_path
     * @return void
     */
    public static function setRootPath($root_path)
    {
        self::$_autoloadRootPath = $root_path;
    }

    /**
     * Load files by namespace.
     *
     * @param string $name
     * @return boolean
     */
    public static function loadByNamespace($name)
    {
        if(isset(self::$_loadedFiles[md5($name)])){
            return true;
        }

        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $name);

        if (strpos($name, 'app\\') === 0) {
            if (self::$_autoloadRootPath) {
                $class_file = self::$_autoloadRootPath . DIRECTORY_SEPARATOR  . substr($class_path, strlen('app\\')) . '.php';
            }
            if (empty($class_file) || !is_file($class_file)) {
                $class_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . substr($class_path, strlen('dds\\')) . '.php';
            }

        } else {
            if (self::$_autoloadRootPath) {
                $class_file = self::$_autoloadRootPath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. $class_path . '.php';
            }
            if (empty($class_file) || !is_file($class_file)) {
                $class_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "$class_path.php";
            }
        }

        if (is_file($class_file)) {
            require($class_file);
            if (class_exists($name, false)) {
                self::$_loadedFiles[md5($name)] = $name;
                return true;
            }
        }

        return false;
    }


}

spl_autoload_register('\app\Autoloader::loadByNamespace');