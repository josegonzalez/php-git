<?php

	/*
		This file configures the git.php script
	*/

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
	$emailaddress = "Peeter.Vois@proekspert.ee";

    $title  = "Peeter's public repository";
    $repo_index = "index.aux";
    
    /* The directory where repo directories resist */
    $repo_directory = "/home/peeter/public_html/git/";
    
    /* The suffix, that can be used for complementing the repo directory,
    useful when you are using nonbare repositories, then:
    $repo_suffix = '/.git/';
    */
    $repo_suffix = '';
    
    /* The date format to show the dates of commits */
    $git_date_format = 'd.m.Y (H:i)';
    
    $cache_name=".cache/";
    $secret_name=".secrets/";
	$bundle_name=".bundles/";
    $cache_directory = $repo_directory.$cache_name;

    $repo_http_relpath = "/peeter/git/";
    $http_server_name = "http://people.proekspert.ee";
    $http_method_prefix = $http_server_name.$repo_http_relpath;
    $communication_link = "http://people.proekspert.ee/peeter/blog";

    //if git is not installed into standard path, we need to set the path
    $mypath= getenv("PATH");
    $addpath = "/home/peeter/local/bin";
    if (isset($mypath))
    {
        $mypath .= ":$addpath";
    }
    else
     	$mypath = $addpath;
    putenv("PATH=$mypath");
?>
