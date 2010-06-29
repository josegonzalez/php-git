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
  // | Author: Xan Manning http://knoxious.co.uk/                             |
  // +------------------------------------------------------------------------+ 

/* git-php Configuration file */

/* Add the default css */
	$CONFIG['git_css'] = true;

/* Add the git logo in the footer */
	$CONFIG['git_logo'] = true;

/* True if the bundle transfer is active */
	$CONFIG['git_bundle_active'] = true;

/* True if committing is active */
	$CONFIG['git_commiting_active'] = true;

/* Personal information */
	$CONFIG['email_address'] = "mail@example.com";
	$CONFIG['repo_title']  = "My public repository";

/* The directory where repo directories reside (Trailing slash) */
	$CONFIG['repo_directory'] = "/var/user/";

/* The suffix, that can be used for complementing the repo directory,
 * useful when you are using nonbare repositories, then:
 * $CONFIG['repo_suffix'] = "/.git/";
 * Bare repositories should be set to NULL;
 */
    $CONFIG['repo_suffix'] = "/.git/";
    # $CONFIG['repo_suffix'] = NULL;        // Bare Repository!

/* The path to GeSHi (geshi.php). Can be relative. */
	$CONFIG['geshi_directory'] = "/path/to/geshi.php";

/* GeSHi line styles. (Produces tiger striping) */
    $CONFIG['geshi_linea_style'] = 'background: #FFFFFF;';
    $CONFIG['geshi_lineb_style'] = 'background: #F5F5F5; font-weight: normal;';

/* The date format to show the dates of commits */
	$CONFIG['git_date_format'] = "d.m.Y (H:i)";

	$CONFIG['cache_name'] = ".temp/cache/";
	$CONFIG['secret_name'] = ".temp/secrets/";
	$CONFIG['bundle_name'] = ".temp/bundles/";
	$CONFIG['cache_directory'] = dirname(__FILE__) . "/" . $CONFIG['cache_name'];
    $CONFIG['bundle_directory'] = dirname(__FILE__) . "/" . $CONFIG['bundle_name'];
    $CONFIG['secret_directory'] = dirname(__FILE__) . "/" . $CONFIG['secret_name'];

/* There has been a major change here, sometimes the preferred method for 
 * cloning is via git:// or ssh:// protocol. The option to change how a repository
 * is cloned has been added to this release.
 *
 * Example usage. /home/git/project.git/ can be accessed with git://[SERVER_ADDRESS]/project.git (Git Protocol)
 * $CONFIG['git_server_method'] = "git://[SERVER_ADDRESS]/";
 *
 * Let's say /home/git/user_folder/project.git/ wants to be accessed with SSH. [USER]@[SERVER_ADDRESS]:user_folder/project.git
 * and this repository is for one user only...
 * $CONFIG['git_server_method'] = "[USER]@SERVER_ADDRESS]:";
 * $CONFIG['repo_relpath'] = "user_folder/";
 */

	$CONFIG['repo_relpath'] = "";
	$CONFIG['git_server_method'] = "http://localhost/";
	$CONFIG['git_method_prefix'] = $CONFIG['git_server_method'] . $CONFIG['repo_relpath'];
	$CONFIG['communication_link'] = "http://mypersonalwebsite.com";

/* if git is not installed into standard path, we need to set the path */
	$mypath = getenv("PATH");
	$addpath = "/usr/local/git/bin";
	if (isset($mypath)) {
		$mypath .= ":" . $addpath;
	} else {
		$mypath = $addpath;
	}
	putenv("PATH=" . $mypath);
?>
