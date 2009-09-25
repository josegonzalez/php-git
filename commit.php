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

global $title;
global $repos; // list of repositories
global $validargs; // list of allowed arguments
global $git_embed;
global $git_css;
global $git_logo;
global $http_method_prefix; // prefix path for http clone method
global $communication_link; // link for sending a message to owner
global $failedarg;
global $cache_name;
global $tags;
global $branches;
global $nr_of_shortlog_lines;
        
global $keepurl; //the arguments that must be resent

//repos could be made by an embeder script
if (!is_array($repos))
  $repos = array();

if(!is_array($validargs))
  $validargs = array();
                
require_once( "config.php" );
require_once( "security.php" );
require_once( "html_helpers.php" );
require_once( "filestuff.php" );

if( !$git_commiting_active ) die();

security_load_repos();
security_test_repository_arg();
//security_load_names();

// some simple methods do not need checking
        
clean_up_secrets();

if (isset($_GET['dl']))
  if ( in_array( $_GET['dl'], $icondesc, true ) )
    write_img_png($_GET['dl']);
  else if ( in_array( $_GET['dl'], $flagdesc, true ) )
    write_img_png($_GET['dl']);
  else if ( $_GET['dl'] =="human_check" )
    draw_human_checker(create_secret());

if (isset($_POST['action']))
  if (check_secret($_POST['check']))
    {
      send_the_main_page('verify');
      die();
    }
  else
    {
      send_the_main_page('badsecret');
      die();
    }

send_the_main_page();
die();


function send_the_submit_form()
{
  html_spacer();
  html_title("BUNDLE INFORMATION");
  html_spacer();

  echo html_ref( array( 'p'=>$_GET['p'], 'a'=>"jump_to_tag" ),"<form method=post enctype=\"multipart/form-data\" action=\"");
  echo "<div class=\"optiontable\">";
  echo "<table>\n";
  echo "<tr><td class=\"descol\">Short MEANINGFUL description </td><td class=\"valcol\"><input type=\"text\" name=\"commiter name\" size=\"40\"></td></tr>\n";
  echo "<tr><td class=\"descol\">Bundle file </td><td class=\"valcol\"><input type=\"file\" name=\"bundle_file\" size=\"40\"></td></tr>\n";
  echo "<tr><td class=\"descol\">enter the value <img src=\"".sanitized_url()."dl=human_check\"/> here </td><td class=\"valcol\"><input type=\"text\" name=\"check\" size=\"40\"></td></tr>\n";
  echo "<tr><td class=\"descol\">Submit </td><td class=\"valcol\"><input type=\"submit\" name=\"action\"  value=\"commit\" size=\"10\"></td></tr>\n";
  echo "</table></div>\n";

  echo "</form>\n";

  send_the_help_section_of_submit();
}

function send_the_help_section_of_submit()
{
  html_spacer();
  html_title("HELP");
  html_spacer();
  echo "<table><tr><td>";
  echo "To create a bundle, you can use the command similar to the following:<br>";
  echo "<b>git bundle create mybundle.bdl master ^v1.0.0</b><br>";
  echo "where v1.0.0 is the tag name that exists in yours and this repository<br>";
  echo "</td></tr></table>";
}

function send_the_bundles_in_queue()
{
  global $repo_directory, $bundle_name;
  $repo=$_GET['p'];     
  html_spacer();
  html_title("BUNDLES IN QUEUE");
  html_spacer();
  $bundles = load_bundles_in_directory();
  echo "<table><th>Nr</nr><th>Bundle file</th><th>Description</th>\n";
  $nr = 0;
  foreach( $bundles as $bdl )
    {
      $nr++;
      echo "<tr><td>$nr</td><td><a href=\"".$bundle_name.$repo."/".$bdl['bdl']."\">".$bdl['bdl']."</a></td><td>".$bdl['name']."</td></tr>\n";
    }
  echo "</table>";
}

// the main page
function send_the_main_page( $subpage = 'submit' )
{
  if( !isset($_GET['p'] ) ) die();

  html_header();
  html_style();
  html_Title("COMMITING TO ".$_GET['p']);
  html_pages();
  switch( $subpage ){
  case 'submit':
    send_the_submit_form();
    break;
  case 'verify':
    check_verify_bundle();
    break;
  case 'badsecret':
    html_spacer();
    html_title( "!!! Wrong secret !!!" );
    html_spacer();
    break;
  }
  send_the_bundles_in_queue();
  html_spacer();
  html_footer();
  die();
}

// **************************

function create_bundles_directory()
{
  global $repo_directory, $bundle_name;
  $repo=$_GET['p'];
  $dname = $repo_directory.$bundle_name."/";
  create_directory( $dname );
  return create_directory( $dname.$repo ) ;
}

function load_bundles_in_directory()
{
  global $repo_directory, $bundle_name;
  $repo=$_GET['p'];
  $bundles = array();
  $dname = $repo_directory.$bundle_name.$repo."/";
  create_bundles_directory();
  if ($handle = opendir($dname)) 
    {
      while (false !== ($fname = readdir($handle))) 
        {
          if( !is_numeric($fname) ) continue;
          $fullpath = $dname.$fname;
          if ( !is_file($fullpath) ) continue;
          if ( !is_file($fullpath.".txt") ) continue; // the description file must exist too
          $record['bdl'] = $fname;
          $file = fopen( $fullpath.".txt", "r" );
          if( check_new_head_in_bundle( $fullpath, $out ) )
            $record['name'] = fgets( $file );
          else
            $record['name'] = "*** applied *** ".fgets( $file );
          fclose( $file );
          $bundles[] = $record;
        }
      closedir($handle);
    } 
  return $bundles;
}

function save_bundle()
{
  global $repo_directory, $bundle_name, $emailaddress;
  $repo=$_GET['p'];
  $dname = $repo_directory.$bundle_name.$repo."/";
  create_bundles_directory();
  if( $_FILES['bundle_file']['error'] != UPLOAD_ERR_OK ) return false;
  $fname = "";
  do{ $fname = create_random_message( 9 ); } while( is_file( $dname.$fname ) );
  $fullpath = $dname.$fname;
  if( false == move_uploaded_file( $_FILES['bundle_file']['tmp_name'], $fullpath ) ) return false;
  chmod( $fullpath, 0666 );
  $file = fopen( $fullpath.".txt", "w" );
  fwrite( $file, $_POST['commiter_name'], 40 );
  fclose( $file );
  chmod( $fullpath.".txt", 0666 );
  // send e-mail message about the commitment
  $message = $_POST['commiter_name']."\n ".$fullpath."\n";
  $headers = 'From: '. $emailaddress . "\r\n" .
    'Reply-To: ' . $emailaddress . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
  $ok = mail( $emailaddress, "Bundle sent to ".$repo, $message, $headers );
  if( $ok == false ){
    echo "Error sending email message\n";
  }
  return true;
}

// check if a tag is in repository
function check_tag_in_repo( $the_tag )
{
  global $repo_directory;
  $repo=$_GET['p'];
  $out = array();
  $nr=0;
  do{
    $cmd="GIT_DIR=".escapeshellarg($repo_directory.$repo)." git-rev-list --all --full-history --topo-order ";
    $cmd .= "--max-count=1000 --skip=" .escapeshellarg($nr) ." ";
    $cmd .= "--pretty=format:\"";
    $cmd .= "parents %P%n";
    $cmd .= "endrecord%n\"";
    unset($out);
    $out = array();
    $rrv= exec( $cmd, &$out );
    foreach( $out as $line )
      {
        $nr = $nr +1;
        unset($d);
        $d = explode( " ", $line );
        if( ($d[0] == "commit") && ($d[1]==$the_tag) )
          return true; // tag was found in repo
      }
  }while( count($out) > 0 );
  return false; // tag was not in repo
}


// check if the bundle does include new stuff
function check_new_head_in_bundle( $what, &$out1 )
{
  $repo=$_GET['p'];
  $cmd1="GIT_DIR=".get_repo_path(basename($repo))." git-bundle verify ".escapeshellarg($what)." 2>&1 ";
  //echo $cmd1;
  $out1 = array();
  $status = 1;
  exec( $cmd1, &$out1, &$status );
  if( $status == 0 )
    foreach ( $out1 as $line )
      {
        unset($d);
        $d = explode( " ", $line );
        if( is_sha1( $d[0] ) )
          if( !check_tag_in_repo( $d[0] ) )
            return true;
      }
  $out1[] = "*** Error *** No new tag found in bundle!";
  return false;
}


// returns true if bundle does apply to the database
// returns false if the bunlde does not apply to the database
function check_verify_bundle()
{
  $success=true;
  $repo=$_GET['p'];
  $what=$_FILES['bundle_file']['tmp_name'];
  $out1 = array();
  if( $success ) $success = $success && check_new_head_in_bundle( $what, $out1 );
  if( $success ) $success = $success && save_bundle();
  html_spacer();
  if( $success ){
    html_title("REGISTERED");
  }
  else{
    html_title("!!! ERROR !!!");
  }
  html_spacer();
  echo "<table>\n";
  foreach( $out1 as $out ){
    echo "<tr><td>".$out."</td></tr>\n";
  }
  echo "</table>\n";
  return $status;
}

?>
