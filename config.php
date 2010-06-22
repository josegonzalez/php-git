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
 * $repo_suffix = "/.git/";
 */
	$CONFIG['repo_suffix'] = "/.git/";

/* The date format to show the dates of commits */
	$CONFIG['git_date_format'] = "d.m.Y (H:i)";

	$CONFIG['cache_name'] = ".temp/cache/";
	$CONFIG['secret_name'] = ".temp/secrets/";
	$CONFIG['bundle_name'] = ".temp/bundles/";
	$CONFIG['cache_directory'] = dirname(__FILE__) . "/" . $CONFIG['cache_name'];

	$CONFIG['repo_http_relpath'] = "";
	$CONFIG['http_server_name'] = "http://localhost/";
	$CONFIG['http_method_prefix'] = $CONFIG['http_server_name'] . $CONFIG['repo_http_relpath'];
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
