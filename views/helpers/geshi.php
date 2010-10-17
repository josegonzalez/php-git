<?php

class GeshiHelper {

    private $_geshi;

    function highlight($source, $language = 'diff') {
        if (!$this->_geshi) {
            require_once(LIBS . 'geshi.php');
            $this->_geshi = new GeSHi($source, $language);
        }

        return $this->_geshi->parse_code();
    }
}