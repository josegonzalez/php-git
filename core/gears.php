<?php 
use Cake\Utility\Inflector;

/**
 * Gears - Template Engine
 *
 * A template engine that can will display a specific template within the templates directory.
 * The template can be bound with variables that are passed into the template from PHP,
 * as well the template can have a wrapping layout template.
 * 
 * @author         Miles Johnson - www.milesj.me
 * @copyright    Copyright 2006-2009, Miles Johnson, Inc.
 * @license     http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        www.milesj.me/resources/script/template-engine
 */

class Gears {

/**
 * Current version: www.milesj.me/resources/script/template-engine
 *
 * @access public
 * @var string
 */
    public $version = '2.1';

/**
 * Settings required for the template engine.
 *
 * @access private
 * @var array
 */
    private $__config = array(
        'ext'           => 'php',
        'element_path'  => '/',
        'layout_path'   => '/',
        'layout'        => null,
        'path'          => '/'
    );

/**
 * The rendered inner content to be used in the layout.
 *
 * @access private
 * @var string
 */
    private $__content;

/**
 * Array of binded template variables.
 *
 * @access private
 * @var array
 */
    private $__variables = array();


/**
 * Array of helper objects
 *
 * @access protected
 * @var array
 */
    protected $_helpers = array();


/**
 * Current Request
 *
 * @access protected
 * @var Object
 */
    protected $_request;

    public function __set($name, $value) {
        if (file_exists(MODELS . Inflector::underscore($name)  . '.php')) {
            require_once(MODELS . Inflector::underscore($name)  . '.php');
            $this->_helpers[$name] = $value;
            return;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->_helpers)) {
            return $this->_helpers[$name];
        }
        
        if (file_exists(VIEWS . 'helpers' . DS . Inflector::underscore($name)  . '.php')) {
            require_once(VIEWS . 'helpers' . DS . Inflector::underscore($name)  . '.php');
            $class = "{$name}Helper";
            $this->_helpers[$name] = new $class;
            return $this->_helpers[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

/**
 * Configure the templates path and file extension.
 *
 * @access public
 * @param string $path
 * @param string $ext
 * @return void
 */
    public function __construct($request, $options = array()) {
        $options = array_merge(array(
            'ext' => 'tpl',
            'path' => '',
            'element_path' => '',
            'layout_path' => '',
            'layout' => 'default.tpl'
        ), $options);

        if (substr($options['element_path'], -1) != DIRECTORY_SEPARATOR) {
            $options['element_path'] .= DIRECTORY_SEPARATOR;
        }

        if (substr($options['layout_path'], -1) != DIRECTORY_SEPARATOR) {
            $options['layout_path'] .= DIRECTORY_SEPARATOR;
        }

        if (substr($options['path'], -1) != DIRECTORY_SEPARATOR) {
            $options['path'] .= DIRECTORY_SEPARATOR;
        }

        $this->_request = $request;
        $this->__config = array(
            'element_path' => $options['element_path'],
            'ext' => trim($options['ext'], '.'),
            'layout' => $options['layout'],
            'layout_path' => $options['layout_path'],
            'path' => $options['path'],
        );
    }

/**
 * Binds variables to all the templates.
 *
 * @access public
 * @param array $vars
 * @return void
 */
    public function bind(array $vars = array()) {
        if (!empty($vars)) {
            foreach ($vars as $var => $value) {
                $this->__variables[$var] = $value;
            }
        }
    }

/**
 * Checks for a valid template extension and that the file exists.
 *
 * @access public
 * @param string $tpl
 * @return string
 */
    public function checkPath($tpl) {
        if (substr($tpl, -(strlen($this->__config['ext']) + 1)) != '.'. $this->__config['ext']) {
            $tpl .= '.'. $this->__config['ext'];
        }

        $tpl = str_replace($this->__config['path'], '', $tpl);

        if (!file_exists($this->__config['path'] . $tpl)) {
            trigger_error('Gears::checkPath(): The template "'. $tpl .'" does not exist', E_USER_ERROR);
        }

        return $tpl;
    }

/**
 * Displays the chosen template and its layout.
 *
 * @access public
 * @param string $tpl
 * @param boolean $return
 * @return mixed
 */
    public function display($tpl, $return = false) {
        // Render inner layout
        $this->__content = $this->render($this->__config['path'] . $this->checkPath($tpl));

        // Render outer layout
        $rendered = $this->render($this->__config['layout_path'] . $this->__config['layout']);

        if ($return === true) {
            return $rendered;
        } else {
            echo $rendered;
        }
    }

/**
 * Return the rendered content.
 *
 * @access public
 * @return string
 */
    public function content() {
        return $this->__content;
    }

/**
 * Include a template within another template. Can pass variables into its own private scope.
 *
 * @access public
 * @param string $tpl
 * @param array $variables
 * @return string
 */
    public function open($tpl, array $variables = array()) {
        return $this->render($this->__config['path'] . $this->checkPath($tpl), $variables);
    }

/**
 * Include an element template within another template. Can pass variables into its own private scope.
 *
 * @access public
 * @param string $tpl
 * @param array $variables
 * @return string
 */
    public function element($tpl, array $variables = array()) {
        return $this->render($this->__config['element_path'] . $tpl . '.php', $variables);
    }

/**
 * Render the template and extract the variables using output buffering.
 *
 * @access public
 * @param string $tpl
 * @param array $variables
 * @return string
 */
    public function render($tpl, array $variables = array()) {
        $variables = array_merge($this->__variables, $variables);
        extract($variables, EXTR_SKIP);

        ob_start();
        require $tpl;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

/**
 * Reset the class back to its defaults. Can save the previous path if necessary.
 *
 * @access public
 * @param boolean $savePath
 * @return void
 */
    public function reset($savePath = true) {
        $path = $this->__config['path'];

        $this->__config = array();
        $this->__content = null;
        $this->__variables = array();

        if ($savePath === true) {
            $this->__config['path'] = $path;
        }
    }

/**
 * Set the current layout to be used.
 *
 * @access public
 * @param string $tpl
 * @return void
 */
    public function setLayout($tpl) {
        if ($path = $this->checkPath($tpl)) {
            $this->__config['layout'] = $path;
        }
    }

}
