<?php
class System {

    public static function verify($config) {
        if (!extension_loaded('gd')) {
            throw new Exception("GD is not installed");
        }

        if (!function_exists('gd_info')) {
            throw new Exception("GD is not installed properly");
        }
    }

}