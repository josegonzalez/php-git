<?php

  /* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
  // +------------------------------------------------------------------------+
  // | git-php - PHP front end to git repositories                            |
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
  // |                                                                        |
  // | Author: Peeter Vois http://people.proekspert.ee/peeter/blog            |
  // +------------------------------------------------------------------------+ 

function create_directory( $fullpath )
{
  if( ($fullpath[0] != '/') && ($fullpath[1] == 0) ){
    echo "Wrong path name $fullpath\n";
    die();
  }
  if( ! is_dir($fullpath) ){
    if( ! mkdir($fullpath) ){
      echo "Error by making directory $fullpath\n";
      die();
    }
    chmod( $fullpath, 0777 );
    return true;
  }
  return false; 
}

?>
