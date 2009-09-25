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
  // | Author: Zack Bartel <zack@bartel.com>                                  |
  // | Author: Peeter Vois http://people.proekspert.ee/peeter/blog            |
  // +------------------------------------------------------------------------+ 

function security_load_repos() 
{
  global $repo_directory, $repo_suffix, $validargs, $repos;

  if( isset($repo_directory) && file_exists($repo_directory) && is_dir($repo_directory))
    {
      if ($handle = opendir($repo_directory)) 
        {
          while (false !== ($file = readdir($handle))) 
            {
              $fullpath = $repo_directory . $file;
              //printf( "%s,%d\n", $file, is_dir($repo_directory . "/" . $file) );
              if ($file[0] != '.' && is_dir($fullpath) ) 
                {
                  if ( is_dir($fullpath . $repo_suffix) )
                    {
                      /* TODO: Check for valid git repos */
                      // fill the security array.
                      $validargs[] = trim($file);
                      $repos[] = trim($fullpath . "/");
                    }
                }
            }
          closedir($handle);
        } 
    }
  sort($repos);

  // check for cookie attack
  if( isset($_COOKIE['validargs']) )
    {
      hacker_gaught("You have attack cookie named validargs[] set on your browser!\n");
    }
  // testing attack
  //setcookie( 'validargs[]', 'value1 value2' );

}

function security_test_repository_arg()
{
  // security test the arguments
  if( isset($_GET['p']) )
    {
      // check for valid repository name
      if( !is_valid($_GET['p']) )
        hacker_gaught();
    }
}

function security_load_names()
{
  global $validargs, $repo_direcotry, $repo_suffix, $branches, $tags;

  if( isset($_GET['p'] ) )
    {
      // now load the repository into validargs
      $proj=$_GET['p'];
      $out=array();
      $branches=git_parse($proj, "branches" );
      foreach( array_keys($branches) as $tg )
        {
          $validargs[] = $tg;
        }
      $tags=git_parse($proj, "tags");
      foreach( array_keys($tags) as $tg )
        {
          $validargs[] = $tg;
        }
      // add files
      unset($out);
      $head="HEAD";
      if( isset( $_GET['tr'] ) && is_valid( $_GET['tr'] ) ) $head = $_GET['tr'];
      $cmd="GIT_DIR=".get_repo_path($proj).$repo_suffix . " git-ls-tree -r -t ".escapeshellarg($head)." | sed -e 's/\t/ /g'";
      exec($cmd, &$out);
      foreach ($out as $line) 
        {
          $arr = explode(" ", $line);
          //$validargs[] = $arr[2]; // add the hash to valid array
          $validargs[] = basename($arr[3]); // add the file name to valid array
        }       
    }
}

function security_test_arg()
{
  // now, all arguments must be in validargs
  foreach( $_GET as $value )
    {
      if( !is_valid($value) )
        hacker_gaught();
    }
  foreach( $_POST as $value )
    {
      if( !is_valid($value) )
        hacker_gaught();
    }
}

// this function checks if a token is valid argument
function is_valid($token)
{
  global $validargs, $failedarg;

  if( $token == "" ) // empty token is valid too
    return true;
  if( is_numeric( $token ) ) // numeric arguments do not harm
    return true;
  if( is_sha1( $token ) ) // we usually apply sha1 as arguments
    return true;
  foreach($validargs as $va)
    {
      if( $va == $token )
        return true;
    }
  $failedarg = $token;
  return false;
}

function hacker_gaught($mess="")
{
  global $failedarg, $validargs;

  header("Content-Type: text/plain");
  echo "please, do not attack.\n";
  echo "this site is not your enemy.\n\n";
  echo "the failed argument is $failedarg.\n";
  echo "$mess \n";
  foreach( $validargs as $va )
    echo "$va\n";
  die();
}

// checks if the argument is sha1
function is_sha1($val)
{
  //if( !is_string($val) ) return false;
  if( strlen($val) != 40 ) return false;
  for( $i=0; $i<40; $i++ ){
    if( strrpos( "00123456789abcdef", "{$val[$i]}" ) == FALSE ) return false;
  }
  return true;
}

/* Find repo_path for given project */
function get_repo_path($proj)   {
  global $repos;

  foreach ($repos as $repo)   {
    $path = basename($repo);
    if ($path == $proj)
      return $repo;
  }
  return "";
}

function create_random_message( $len )
{
  $mess= "";
  for( $i=0; $i<$len; $i++ )
    {
      $num = rand(0,9);
      $mess .= "$num";
    }
  return $mess;
}

function draw_human_checker( $amessage )
{
  $fh = imagefontheight(5);
  $fw = imagefontwidth(5);

  if( !is_string( $amessage ) ) die();

  $ml = strlen( $amessage );
  $w = $ml * $fw +6;
  $h = $fh+6;
  $prob = 5;


  $im = imagecreate( $w, $h );
  $cvar[0] = imagecolorallocate($im, 255, 255, 255 );
  $cvar[1] = imagecolorallocate($im, 200,   0,   0 );
  $cvar[2] = imagecolorallocate($im,   0, 200,   0 );
  $cvar[3] = imagecolorallocate($im,   0,   0, 200 );
  $cvar[4] = imagecolorallocate($im,   0,   0,   0 );

  $col1 = rand(0,4);
  do{ $col2 = rand(0,4); } while( $col1 == $col2 );

  //echo "$fh,$fw";
  //die();

  imagefill( $im, 0, 0, $cvar[$col1] );
        
  for( $i=0; $i<$ml; $i++ )
    {
      $m = "$amessage[$i]";
      imagestring( $im, 5, $i*$fw+rand(0,4), rand(0,12)-3, $m, $cvar[$col2] );
    }

  $n = $fw*$h*$prob / 100;
  for( $i=3; $i<$w; $i+=$fw )
    {
      for( $j=0; $j<$n; $j++ )
        {
          $x = rand($i,$i+$fw);
          $y = rand(0,$h-1);
          $idx = imagecolorat( $im, $x, $y );
          if( $idx == $col1 ){
            imagesetpixel($im,$x,$y,$cvar[$col2]);
          }
          else{
            imagesetpixel($im,$x,$y,$cvar[$col1]);
          }            
        }
    }
  imagepng( $im );
  die();
}

function create_secrets_directory()
{
  global $repo_directory, $secret_name;
    
  $dname = $repo_directory.$secret_name;
  if( create_directory( $dname ) == false ) return false;
  /* TODO: This is Apache-specific. How to generalize? */
  $file = fopen( $dname.".htaccess", "w" ); 
  fwrite( $file, "Deny from all\n" );
  fclose( $file );
  $file = fopen( $dname."index.html", "w" );
  fwrite( $file, "Access denied\n" );
  fclose( $file );
  return true;
}

function create_secret()
{
  global $repo_directory, $secret_name;
    
  $now = floor(time()/60/60); // number of hours since 1970
  $secret = "";

  create_secrets_directory();
  do{ $secret = create_random_message( 9 ); }while( file_exists($repo_directory.$secret_name.$secret) );
  $file = fopen( $repo_directory.$secret_name.$secret, "w" );
  fwrite( $file, "$now" );
  fclose( $file );
  return $secret;
}

function clean_up_secrets()
{
  global $repo_directory, $secret_name;
  $now = floor(time()/60/60); // number hours since 1970
  create_secrets_directory();
  if ($handle = opendir($repo_directory.$secret_name)) 
    {
      while (false !== ($fname = readdir($handle))) 
        {
          if( !is_numeric($fname) ) continue;
          $fullpath = $repo_directory.$secret_name.$fname;
          //printf( "%s,%d\n", $file, is_dir($repo_directory . "/" . $file) );
          if ( !is_file($fullpath) ) continue;
          $file = fopen( $fullpath , "r" );
          $fd = 0;
          fscanf( $file, "%d", $fd );
          fclose( $file );
          if( abs( $fd - $now ) > 1 ) unlink( $fullpath );
        }
      closedir($handle);
    } 
}

function check_secret( $fname )
{
  global $repo_directory, $secret_name;
    
  if( !is_numeric($fname) )
    {
      hacker_gaught( $fname );
      die(); // dangerously wrong secret
    }
    
  $fullpath = $repo_directory.$secret_name.$fname;
  if( !file_exists($fullpath) ) return false; // wrong secret
  unlink( $fullpath ); // clean up the obsolete secret
  return true;
}

?>
