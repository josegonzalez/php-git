<?php
/* git-php Configuration file */

/* Add the default css */
	$git_css = true;

/* Add the git logo in the footer */
	$git_logo = true;

/* True if the voting mechanism with SMS is active */
	$git_sms_active = false;

/* True if the bundle transfer is active */
	$git_bundle_active = true;

/* True if committing is active */
	$git_commiting_active = true;

/* E-mail address to notify about the bundles */
	$emailaddress = "mail@example.com";

	$title  = "Owner's public repository";
	$repo_index = "index.aux";

/* The directory where repo directories resist */
	$repo_directory = dirname(__FILE__) . "/";

/* The suffix, that can be used for complementing the repo directory,
 * useful when you are using nonbare repositories, then:
 * $repo_suffix = "/.git/";
 */
	$repo_suffix = "/.git/";

/* The date format to show the dates of commits */
	$git_date_format = 'd.m.Y (H:i)';

	$cache_name = "./temp/cache/";
	$secret_name = "./temp/secrets/";
	$bundle_name = "./temp/bundles/";
	$cache_directory = "{$repo_directory}{$cache_name}";

	$repo_http_relpath = "/git-php/";
	$http_server_name = "localhost";
	$http_method_prefix = "{$http_server_name}{$repo_http_relpath}";
	$communication_link = "http://mypersonalwebsite.com";

/* if git is not installed into standard path, we need to set the path */
	$mypath= getenv("PATH");
	$addpath = "/usr/local/git/bin";
	if (isset($mypath)) {
		$mypath .= ":$addpath";
	} else {
		$mypath = $addpath;
	}
	putenv("PATH=$mypath");
?>