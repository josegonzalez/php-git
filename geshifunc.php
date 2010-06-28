<?php

  /* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
  // +------------------------------------------------------------------------+
  // | git-php - PHP front end to git repositories   [GeSHi Patch]            |
  // +------------------------------------------------------------------------+
  // | Copyright (c) 2006 Zack Bartel                                         |
  // +------------------------------------------------------------------------+
  // | This program is free software; you can redistribute it and/or          |
  // | modify it under the terms of the GNU General Public License            |
  // | as published by the Free Software Foundation; either version 2         |
  // | of the License, or (at your option) any later version.                 |
  // |                                                                        |
  // | This program is distributed in the hope that it will be useful,        |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
  // | GNU General Public License for more details.                           |
  // |                                                                        |
  // | You should have received a copy of the GNU General Public License      |
  // | along with this program; if not, write to the Free Software            |
  // | Foundation, Inc., 59 Temple Place - Suite 330,                         |
  // | Boston, MA  02111-1307, USA.                                           |
  // +------------------------------------------------------------------------+
  // | Author: Xan Manning http://knoxious.co.uk/                             |
  // +------------------------------------------------------------------------+ 

include_once($CONFIG['geshi_directory']);

function geshi_init()
    {
        global $geshi;
        global $CONFIG;
        $geshi = new GeSHi('<!-- Unavailable -->', 'html4strict');
		$geshi->enable_classes();
		$geshi->set_header_type(GESHI_HEADER_PRE_VALID);
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
        $geshi->set_line_style($CONFIG['geshi_linea_style'], $CONFIG['geshi_lineb_style'], TRUE);
    }

function geshi_highlight($code, $language)
    {
        global $geshi;
        $geshi->set_language($language);
		$geshi->set_source($code);
		$geshiCode = $geshi->parse_code();
        return $geshiCode;
    }

function geshi_style()
    {
        global $geshi;
		$geshiStyle = "<style type=\"text/css\">" . $geshi->get_stylesheet() . "</style>";
        return $geshiStyle;
    }

?>
