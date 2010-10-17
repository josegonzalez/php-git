<?php
date_default_timezone_set('America/New_York');

$CONFIG['repo_directory'] =  ROOT. 'repositories' . DS;
$CONFIG['repo_suffix'] = "/.git/";
$CONFIG['git_date_format'] = "d.m.Y (H:i)";
$CONFIG['temp_directory'] = TMP;
$CONFIG['git_binary'] = '/usr/local/bin/git';
$CONFIG['git_css'] = true;
$CONFIG['repo_http_relpath'] = '';
$CONFIG['http_server_name'] = 'http://localhost/';
$CONFIG['http_method_prefix'] = "{$CONFIG['http_server_name']}{$CONFIG['repo_http_relpath']}";;
$CONFIG['help_link'] = 'http://example.com';