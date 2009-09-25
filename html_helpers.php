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

  // this functions existance starts from php5
function array_diff_ukey1( $array1, $array2 )
{
  if( !is_array($array1) ) return array();
  $a1 = array_keys( $array1 );
  $res = array();
        
  foreach( $a1 as $b ){
    if( isset( $array2[$b] ) ) continue;
    $res[$b] = $array1[$b];
  }
  return $res;
}

// creates a href= beginning and keeps record with the carryon arguments
function html_ahref( $arguments, $class="" )
{
  $ahref = "<a ";
  if( $class != "" ) $ahref .= "class=\"$class\" ";
  $ahref .= "href=\"";

  return html_ref( $arguments, $ahref );
}

function html_ref( $arguments, $prefix )
{
  global $keepurl;
    
  if( !is_array($keepurl) ) $keepurl = array();

  $diff = array_diff_key( $keepurl, $arguments );
  $ahref = $prefix.sanitized_url();
  $a = array_keys( $diff );
  foreach( $a as $d ){
    if( $diff[$d] != "" ) $ahref .= "$d={$diff[$d]}&";
  }
  $a = array_keys( $arguments );
  foreach( $a as $d ){
    if( $arguments[$d] != "" ) $ahref .= "$d={$arguments[$d]}&";
  }
  $now = floor(time()/15/60); // one hour
  $ahref .= "tm=$now";
  $ahref .= "\">";
  return $ahref;
}       

function html_header()  {
  global $title;
  global $git_embed;
    
  if (!$git_embed)    {
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n";
    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n";
    echo "<head>\n";
    echo "\t<title>$title</title>\n";
    echo "\t<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
    echo "\t<meta NAME=\"ROBOTS\" CONTENT=\"NOFOLLOW\" />\n";
    echo "</head>\n";
    echo "<body>\n";
  }
  /* Add rss2 link */
  if (isset($_GET['p']))  {
    echo "<link rel=\"alternate\" title=\"{$_GET['p']}\" href=\"".sanitized_url()."p={$_GET['p']}&dl=rss2\" type=\"application/rss+xml\" />\n";
  }
  echo "<div id=\"gitbody\">\n";
    
}

function html_breadcrumbs()  {
  echo "<div class=\"githead\">\n";
  $crumb = "<a href=\"".sanitized_url()."\">projects</a> / ";

  if (isset($_GET['p']))
    $crumb .= html_ahref( array( 'p'=>$_GET['p'], 'pg'=>"" ) ) . $_GET['p'] ."</a> / ";
    
  if (isset($_GET['b']))
    $crumb .= "blob";

  if (isset($_GET['t']))
    $crumb .= "tree";

  if ($_GET['a'] == 'commitdiff')
    $crumb .= 'commitdiff';

  echo $crumb;
  echo "</div>\n";
}

function html_pages() {
  global $git_bundle_active;
  if( isset($_GET['p']) ){
    html_spacer();
    $now = floor(time()/15/60); // one hour
    echo "<center>";
    echo "<a href=\"git.php?p=".$_GET['p']."&tm=$now\">browse</a>";
    if( $git_bundle_active ){ echo " | <a href=\"commit.php?p=".$_GET['p']."\">commit</a>"; }
    echo "</center>";
  }
}

function html_footer()  {
  global $git_embed;
  global $git_logo;

  echo "<div class=\"gitfooter\">\n";

  if (isset($_GET['p']))  {
    echo "<a class=\"rss_logo\" href=\"".sanitized_url()."p={$_GET['p']}&dl=rss2\" >RSS</a>\n";
  }

  if ($git_logo)    {
    echo "<a href=\"http://www.kernel.org/pub/software/scm/git/docs/\">" . 
      "<img src=\"".sanitized_url()."dl=git_logo\" style=\"border-width: 0px;\"/></a>\n";
  }

  echo "</div>\n";
  echo "</div>\n";
  if (!$git_embed)    {
    echo "</body>\n";
    echo "</html>\n";
  }
}

/* TODO: cache this */
// returns URL of this script
// including any set GET-parameters of p, dl, b, a, h, t
function sanitized_url()    {
  global $git_embed;

  /* the sanitized url */
  $url = "{$_SERVER['SCRIPT_NAME']}?";

  if (!$git_embed)    {
    return $url;
  }

  /* the GET vars used by git-php */
  $git_get = array('p', 'dl', 'b', 'a', 'h', 't');


  foreach ($_GET as $var => $val) {
    if (!in_array($var, $git_get))   {
      $get[$var] = $val;
      $url.="$var=$val&amp;";
    }
  }
  return $url;
}

function html_spacer($text = "&nbsp;")  {
  echo "<div class=\"gitspacer\">$text</div>\n";
}

function html_title($text = "&nbsp;")  {
  echo "<div class=\"gittitle\">".$text."</div>\n";
}

function html_style()   {
  global $git_css;
    
  if (file_exists("style.css"))
    echo "<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\" />\n";
  if ($git_css)
    echo "<link rel=\"stylesheet\" href=\"gitstyle.css\" type=\"text/css\" />\n";
}

// *****************************************************************************
// Icons, hardcoded pictures ...
//

$icondesc = array( 'git_logo', 'icon_folder', 'icon_plain', 'icon_color' );
$flagdesc = array();

function write_img_png($imgptr)
{
  $img['icon_folder']['name'] = "icon_folder.png";
  $img['icon_folder']['bin'] = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52".
    "\x00\x00\x00\x10\x00\x00\x00\x10\x08\x06\x00\x00\x00\x1f\xf3\xff" .
    "\x61\x00\x00\x00\x06\x62\x4b\x47\x44\x00\xff\x00\xff\x00\xff\xa0" .
    "\xbd\xa7\x93\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0b\x13\x00" .
    "\x00\x0b\x13\x01\x00\x9a\x9c\x18\x00\x00\x00\x07\x74\x49\x4d\x45" .
    "\x07\xd7\x0a\x07\x13\x33\x34\xdf\x37\x2a\x83\x00\x00\x00\x1d\x74" .
    "\x45\x58\x74\x43\x6f\x6d\x6d\x65\x6e\x74\x00\x43\x72\x65\x61\x74" .
    "\x65\x64\x20\x77\x69\x74\x68\x20\x54\x68\x65\x20\x47\x49\x4d\x50" .
    "\xef\x64\x25\x6e\x00\x00\x00\xbb\x49\x44\x41\x54\x38\xcb\xc5\x92" .
    "\xb1\x0e\xc2\x20\x10\x86\xbf\xa2\xaf\x40\xe2\x02\xc3\x25\x3e\x8f" .
    "\x8f\xd0\xd5\xd5\xc5\x87\x70\x71\xf5\x35\x7c\x1e\x13\x86\x76\xec" .
    "\xd8\xd9\xe0\x52\x9a\x13\xa1\x75\xeb\x9f\x10\x02\xf7\x1f\xf7\x01" .
    "\x07\x5b\xab\xa9\xec\xc7\x3f\x7d\xc5\x40\x7c\x3e\x8e\x78\x67\x01" .
    "\xe8\xfa\x81\xd3\xf9\x55\x3d\xc4\xe4\xc9\x22\x82\x77\x96\x43\x3b" .
    "\x72\x68\x47\xbc\xb3\x88\x48\xa2\x8a\x93\x27\xfe\x10\x88\x48\xbc" .
    "\x5f\x77\x00\x73\xf5\x92\xba\x7e\x00\xe0\x72\x7b\x13\x42\x68\x66" .
    "\x82\x10\xc2\x6a\xb2\x8e\x27\xbf\x29\x05\x75\xa5\xd2\x5a\xfb\x4c" .
    "\x0d\xd3\x3b\x4b\xd7\x0f\x73\x62\x5a\xe7\xda\x2f\x61\xe6\xd7\x29" .
    "\x5d\xaf\x4a\x90\xaa\xe9\x79\x95\x20\xa1\xeb\x1e\x28\xd1\xe8\x83" .
    "\x8c\xfa\xc6\xc5\xc7\xca\x93\x93\xff\x4b\x53\x83\xac\x0e\xdd\x48" .
    "\xdb\xeb\x03\x99\xd1\x5c\xda\xa6\x06\x82\x95\x00\x00\x00\x00\x49" .
    "\x45\x4e\x44\xae\x42\x60\x82";

  $img['icon_plain']['name'] = "icon_plain.png";
  $img['icon_plain']['bin'] =  "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
    "\x00\x00\x00\x10\x00\x00\x00\x10\x08\x06\x00\x00\x00\x1f\xf3\xff" .
    "\x61\x00\x00\x00\x06\x62\x4b\x47\x44\x00\xff\x00\xff\x00\xff\xa0" .
    "\xbd\xa7\x93\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0b\x13\x00" .
    "\x00\x0b\x13\x01\x00\x9a\x9c\x18\x00\x00\x00\x07\x74\x49\x4d\x45" .
    "\x07\xd7\x0a\x07\x13\x35\x1d\xcb\xdf\x15\x69\x00\x00\x00\x1d\x74" .
    "\x45\x58\x74\x43\x6f\x6d\x6d\x65\x6e\x74\x00\x43\x72\x65\x61\x74" .
    "\x65\x64\x20\x77\x69\x74\x68\x20\x54\x68\x65\x20\x47\x49\x4d\x50" .
    "\xef\x64\x25\x6e\x00\x00\x00\x7a\x49\x44\x41\x54\x38\xcb\xbd\x93" .
    "\x5d\x0a\x80\x30\x0c\x83\x53\x4f\xd6\xec\xdd\x1d\x76\x1e\xc0\xdd" .
    "\xac\xbe\x4c\x29\xfb\x81\x29\x6a\x60\x50\xda\x8e\x7c\x04\x0a\xbc" .
    "\x25\xaa\x1a\x80\xe1\x2b\xf3\x46\xe2\x6a\x33\xb3\xa1\x81\x88\xf4" .
    "\xfe\x60\xa9\x17\x03\x89\x40\x36\x75\xa1\x44\x21\xba\x4f\x10\x48" .
    "\xac\x31\x62\x4b\x09\x00\xb0\xe7\x2c\xf5\x8e\x9d\xa2\xaa\x51\xf5" .
    "\xaa\x7d\xcf\xe5\x72\x8f\xa0\x93\x87\x4c\x65\xe0\x7b\x3e\x8f\x6f" .
    "\x09\x7a\xae\x3d\xf7\xff\x33\xf8\x87\xe0\xb3\x63\x9a\x39\xac\x47" .
    "\x3a\x00\x9a\x8c\x62\xbd\x3f\x77\x7d\x0b\x00\x00\x00\x00\x49\x45" .
    "\x4e\x44\xae\x42\x60\x82";

  $img['icon_color']['name'] = "icon_color.png";
  $img['icon_color']['bin'] =  "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
    "\x00\x00\x00\x10\x00\x00\x00\x10\x08\x06\x00\x00\x00\x1f\xf3\xff" .
    "\x61\x00\x00\x00\x06\x62\x4b\x47\x44\x00\xff\x00\xff\x00\xff\xa0" .
    "\xbd\xa7\x93\x00\x00\x00\x09\x70\x48\x59\x73\x00\x00\x0b\x13\x00" .
    "\x00\x0b\x13\x01\x00\x9a\x9c\x18\x00\x00\x00\x07\x74\x49\x4d\x45" .
    "\x07\xd7\x0a\x08\x07\x1d\x34\x97\x7c\x38\x55\x00\x00\x00\x1d\x74" .
    "\x45\x58\x74\x43\x6f\x6d\x6d\x65\x6e\x74\x00\x43\x72\x65\x61\x74" .
    "\x65\x64\x20\x77\x69\x74\x68\x20\x54\x68\x65\x20\x47\x49\x4d\x50" .
    "\xef\x64\x25\x6e\x00\x00\x00\xae\x49\x44\x41\x54\x38\xcb\xb5\x93" .
    "\x41\x0e\x82\x30\x10\x45\x5f\xc1\x23\x78\x0d\xef\xd0\x76\x2f\x7b" .
    "\x2e\xe0\xa1\x38\x80\xec\xcb\x5e\xba\x91\xbd\x89\x77\x70\x61\xbc" .
    "\x02\xc3\x86\x90\x2a\xc5\x20\xa9\x3f\x99\x74\x26\x9d\x76\xfe\xfc" .
    "\x76\x20\x15\x8c\xd6\x02\x2c\xda\xb8\x3f\x83\x0a\x7c\xa9\xa5\x5f" .
    "\x2c\x50\xaa\x2c\x76\x86\x6c\x96\xd8\xe5\x94\x5d\x0e\x40\x65\x2c" .
    "\x95\xb1\x21\x4b\x46\x46\xbf\x33\xa8\x8c\xe5\x58\x14\x34\xce\x01" .
    "\xd0\x7a\xaf\x00\x76\xb1\xc4\xf3\xfd\x35\xc5\xfe\x79\x9b\xfc\xc6" .
    "\x39\x5a\xef\xb7\x69\x10\xd1\x43\x45\x35\x08\xfb\xfe\xa6\x47\x32" .
    "\x06\xab\x34\x08\x2b\x9f\xda\x4b\x5a\x06\x7c\x5c\x20\x5c\xd5\x64" .
    "\x8f\xfd\x41\x6a\xe9\xc5\x68\xfd\xb6\x86\x7f\x21\xfd\x2b\xfc\x6d" .
    "\x98\xd6\x0c\xd6\x26\x0c\x52\x3d\x71\xd0\xf9\x33\x21\x71\x00\x00" .
    "\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82";

  $img['git_logo']['name'] = "git-logo.png";
  $img['git_logo']['bin'] = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
    "\x00\x00\x00\x48\x00\x00\x00\x1b\x04\x03\x00\x00\x00\x2d\xd9\xd4" .
    "\x2d\x00\x00\x00\x18\x50\x4c\x54\x45\xff\xff\xff\x60\x60\x5d\xb0" .
    "\xaf\xaa\x00\x80\x00\xce\xcd\xc7\xc0\x00\x00\xe8\xe8\xe6\xf7\xf7" .
    "\xf6\x95\x0c\xa7\x47\x00\x00\x00\x73\x49\x44\x41\x54\x28\xcf\x63" .
    "\x48\x67\x20\x04\x4a\x5c\x18\x0a\x08\x2a\x62\x53\x61\x20\x02\x08" .
    "\x0d\x69\x45\xac\xa1\xa1\x01\x30\x0c\x93\x60\x36\x26\x52\x91\xb1" .
    "\x01\x11\xd6\xe1\x55\x64\x6c\x6c\xcc\x6c\x6c\x0c\xa2\x0c\x70\x2a" .
    "\x62\x06\x2a\xc1\x62\x1d\xb3\x01\x02\x53\xa4\x08\xe8\x00\x03\x18" .
    "\x26\x56\x11\xd4\xe1\x20\x97\x1b\xe0\xb4\x0e\x35\x24\x71\x29\x82" .
    "\x99\x30\xb8\x93\x0a\x11\xb9\x45\x88\xc1\x8d\xa0\xa2\x44\x21\x06" .
    "\x27\x41\x82\x40\x85\xc1\x45\x89\x20\x70\x01\x00\xa4\x3d\x21\xc5" .
    "\x12\x1c\x9a\xfe\x00\x00\x00\x00\x49\x45\x4e\x44\xae\x42\x60\x82";

  if( !isset($img[$imgptr]['name']) ){ $img[$imgptr]['name'] = "$imgptr.png"; }
  $filesize = strlen($img[$imgptr]['bin']);
  header("Pragma: public"); // required
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: private",false); // required for certain browsers
  header("Content-Transfer-Encoding: binary");
  header("Content-Type: img/png");
  header("Content-Length: " . $filesize);
  header("Content-Disposition: attachment; filename=".$img[$imgptr]['name'].";" );
  header("Expires: +1d");
  echo $img[$imgptr]['bin'];
  die();
}


?>
