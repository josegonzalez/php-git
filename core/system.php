<?php
class System {

    static $configuration = array();

    public static function verify($config) {
        if (!extension_loaded('gd')) {
            throw new Exception("GD is not installed");
        }

        if (!function_exists('gd_info')) {
            throw new Exception("GD is not installed properly");
        }
    }

    public static function set($config) {
        self::$configuration = $config;
    }

    public static function get($key) {
        if (isset(self::$configuration[$key])) {
            return self::$configuration[$key];
        }

        throw new Exception("Configuration key not found");
    }

}