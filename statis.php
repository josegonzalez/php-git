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

function stat_inc_count( $proj )
{
  $td = 0; $tt = 0;
  stat_get_count( $proj, $td, $tt, true );
}

function stat_get_count( $proj, &$today, &$total, $inc=false )
{
  file_stat_get_count( $proj, $today, $total, $inc, 'counters' );
}

// local function (only called from within this file.
function file_stat_get_count( $proj, &$today, &$total, $inc, $fbasename )
{
  global $cache_name, $repo_suffix;
  $rtoday = 0;
  $rtotal = 0;
  $now = floor(time()/24/60/60); // number of days since 1970
  $fname = dirname($proj)."/".$cache_name.$fbasename."-".basename($proj,".git");
  $fd = 0;
  
  
  //$fp1 = sem_get(fileinode($fname), 1);
  //sem_acquire($fp1);
  
  if( file_exists( $fname ) )
    $file = fopen( $fname, "r" ); // open the counter file
  else
    $file = FALSE;
  if( $file != FALSE ){
    fseek( $file, 0 ); // rewind the file to beginning
    // read out the counter value
    fscanf( $file, "%d %d %d", $fd, $rtoday, $rtotal );
    if( $fd != $now ){
      $rtoday = 0;
      $fd = $now;
    }
    if( $inc ){
      $rtoday++;
      $rtotal++;
    }
    fclose( $file );
  }
  // uncomment the next lines to erase the counters
  //$rtoday = 0;
  //$rtotal = 0;        
  $file = fopen( $fname, "w" ); // open or create the counter file      
  // write the counter value
  fseek( $file, 0 ); // rewind the file to beginning
  fwrite( $file, "$fd $rtoday $rtotal\n" );
  fclose( $file );
  $today = $rtoday;
  $total = $rtotal;     
}


?>
