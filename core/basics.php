<?php
/**
 * Basic Cake functionality.
 *
 * Core functions for including other source files, loading models and so forth.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Loads libraries from CORE. Takes optional number of parameters.
 *
 * Example:
 *
 * `uses('flay', 'time');`
 *
 * @param string $name Filename without the .php part
 * @deprecated Will be removed in 2.0
 * @link http://book.cakephp.org/view/1140/uses
 */
	function uses() {
		$args = func_get_args();
		$result = false;
		foreach ($args as $file) {
			$result = include_once(CORE . strtolower($file) . '.php');
			if (!$result) return false;
		}
		return true;
	}

/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/view/1190/Basic-Debugging
 * @link http://book.cakephp.org/view/1128/debug
 */
	function debug($var = false, $showHtml = false, $showFrom = true) {
		if ($showFrom) {
			$calledFrom = debug_backtrace();
			echo '<strong>' . $calledFrom[0]['file'] . '</strong>';
			echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
		}
		echo "\n<pre class=\"cake-debug\">\n";

		$var = print_r($var, true);
		if ($showHtml) {
			$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
		}
		echo $var . "\n</pre>\n";
	}

/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/view/1190/Basic-Debugging
 * @link http://book.cakephp.org/view/1128/debug
 */
	function diebug($var = false, $showHtml = false, $showFrom = true) {
		if ($showFrom) {
			$calledFrom = debug_backtrace();
			echo '<strong>' . $calledFrom[0]['file'] . '</strong>';
			echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
		}
		echo "\n<pre class=\"cake-debug\">\n";

		$var = print_r($var, true);
		if ($showHtml) {
			$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
		}
		echo $var . "\n</pre>\n";
		exit;
	}

/**
 * Splits a dot syntax plugin name into its plugin and classname.
 * If $name does not have a dot, then index 0 will be null.
 *
 * Commonly used like `list($plugin, $name) = pluginSplit($name);`
 *
 * @param string $name The name you want to plugin split.
 * @param boolean $dotAppend Set to true if you want the plugin to have a '.' appended to it.
 * @param string $plugin Optional default plugin to use if no plugin is found. Defaults to null.
 * @return array Array with 2 indexes.  0 => plugin name, 1 => classname
 */
	function pluginSplit($name, $dotAppend = false, $plugin = null) {
		if (strpos($name, '.') !== false) {
			$parts = explode('.', $name, 2);
			if ($dotAppend) {
				$parts[0] .= '.';
			}
			return $parts;
		}
		return array($plugin, $name);
	}
