<?php

class GeshiHelper {

    private $_geshi;

    function highlight($source, $language = 'diff') {
        if (!$this->_geshi) {
            require_once(LIBS . 'geshi.php');
            $this->_geshi = new GeSHi();
        }

        $this->_geshi->set_source($source);
        $this->_geshi->set_language($language);

        return $this->_geshi->parse_code();
    }
}