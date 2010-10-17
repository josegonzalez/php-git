<?php
class Html {

    public static function header($config) {

    }

    public static function style($config)   {
    	if (file_exists(WEB . "css" . DS . "style.css")) {
    		echo "<link rel=\"stylesheet\" href=\"css/style.css\" type=\"text/css\" />\n";
    	}
    	if ($config['git_css']) {
    		echo "<link rel=\"stylesheet\" href=\"css/gitstyle.css\" type=\"text/css\" />\n";
    	}
    }

    function breadcrumbs() {
        echo "<div class=\"githead\">\n";
        $crumb = "<a href=\"" . sanitized_url() . "\">projects</a> / ";

        if (isset($_GET['p'])){
            $crumb .= html_ahref(array('p'=>$_GET['p'], 'pg'=>"")) . $_GET['p'] ."</a> / ";
        }
        if (isset($_GET['b'])){
            $crumb .= "blob";
        }
        if (isset($_GET['t'])){
            $crumb .= "tree";
        }
        if ($_GET['a'] == 'commitdiff'){
            $crumb .= 'commitdiff';
        }

        echo $crumb;
        echo "</div>\n";
    }
    

}