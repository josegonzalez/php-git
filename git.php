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


global $CONFIG;
global $repos; // list of repositories
global $validargs; // list of allowed arguments
global $git_embed; // is git-php embedded in other scripts (true) or runs on it own (false)?
global $failedarg;
global $tags;
global $branches;
global $nr_of_shortlog_lines;
global $geshi;

global $keepurl; //the arguments that must be resent

require_once("config.php");
require_once("security.php");
require_once("html_helpers.php");
require_once("statis.php");
require_once("filestuff.php");
require_once("geshifunc.php");

$keepurl = array();

//repos could be made by an embeder script
if (!is_array($repos)) {
  $repos = array();
}
if (!is_array($validargs)){
  $validargs = array();
}

system_load_checks();

security_load_repos();
security_test_repository_arg();
security_load_names();
geshi_init();

if (isset($_GET['p'])) {
	// increase statistic counters
	if ($_GET['dl'] != 'rss2') {
		// do not count the rss2 requests
		if ((floor(time() / 15 / 60) - intval($_GET['tm'])) > 4) {
			// do not count the one hour session
			if (((count($_GET) > 1) && isset($_GET['tm'])) || count($_GET) == 1) {
				// prevent counting if no time set and more than one argument given
				stat_inc_count(get_repo_path($_GET['p']));
			}
		}
	}
}

// add some keywords to valid array
$arrowdesc = array('none', 'up', 'down');
$validargs = array_merge($validargs,
	array("targz", "plain", "dlfile", "image", "rss2", "commitdiff", "jump_to_tag", "GO", "SET", "HEAD",),
	$icondesc, $arrowdesc);

security_test_arg();

// fill keepurl fields
$keepargs = array('tr', 'pg', 'tag');
foreach($keepargs as $idx) {
	if (isset($_GET[$idx])) {
		$keepurl[$idx] = $_GET[$idx];
	}
}

unset($validargs);
// end of validity check

// modifying the number of shortlog lines
if (isset($_POST['nr_of_shortlog_lines'])) {
	$nr_of_shortlog_lines = $_POST['nr_of_shortlog_lines'];
	setcookie('nr_of_shortlog_lines', $nr_of_shortlog_lines, time() + 3600 * 24 * 360);
} elseif (isset($_COOKIE['nr_of_shortlog_lines'])) {
	$nr_of_shortlog_lines = $_COOKIE['nr_of_shortlog_lines'];
} else {
	$nr_of_shortlog_lines = 20;
}

if ($nr_of_shortlog_lines > 512 || $nr_of_shortlog_lines < 0 || !is_numeric($nr_of_shortlog_lines)) {
	$nr_of_shortlog_lines = 20;
}

$extGeSHi = array(
'.as'		    => 'actionscript',		#ActionScript
'.ada'		    => 'ada',			    #Ada 
'.scpt'		    => 'applescript',		#AppleScript 
'.applescript'	=> 'applescript',	    #AppleScript 
'.list'		    => 'apt_sources',		#Apt sources 
'.asm'		    => 'asm',			    #ASM 
'.asp'		    => 'asp',			    #ASP 
'.autoconf' 	=> 'autoconf',			#Autoconf 
'.afk'		    => 'autohotkey',		#Autohotkey 
'.au3'		    => 'autoit',			#AutoIt 
'.avs'		    => 'avisynth',			#AviSynth 
'.awk'		    => 'awk',			    #awk 
'.sh'	    	=> 'bash',			    #Bash 
'.bgm'	    	=> 'basic4gl',		    #Basic4GL 
'.bib'	    	=> 'bibtex',			#BibTeX
'.bibtex'   	=> 'bibtex',			#BibTeX 
'.bb'		    => 'blitzbasic',		#BlitzBasic 
'.bnf'	    	=> 'bnf',			    #bnf 
'.boo'		    => 'boo',			    #Boo 
'.c'		    => 'c',				    #C 
'.h'		    => 'c',				    #C 
'.dcl'		    => 'caddcl',			#CAD DCL 
'.cfm'		    => 'cfm',			    #ColdFusion
'.cfml'		    => 'cfm',			    #ColdFusion 
'.clj'		    => 'clojure',			#Clojure 
'.cmake'	    => 'cmake',			    #CMake 
'.cbl'		    => 'cobol',			    #COBOL 
'.cpp'		    => 'cpp',			    #C++
'.hpp'		    => 'cpp',			    #C++
'.cc'		    => 'cpp',			    #C++
'.c++'		    => 'cpp',			    #C++ 
'.cxx'		    => 'cpp',			    #C++ 
'.csh'		    => 'csharp',			#C# 
'.css'		    => 'css',			    #CSS 
'.cue'		    => 'cuesheet',			#Cuesheet 
'.d'		    => 'd',				    #D 
'.dpr'		    => 'delphi',			#Delphi 
'.diff'		    => 'diff',			    #Diff 
'.patch'	    => 'diff',		    	#Diff 
'.dot'		    => 'dot',		    	#dot 
'.es'		    => 'ecmascript',		#ECMAScript 
'.e'		    => 'eiffel',			#Eiffel 
'.erl'		    => 'erlang',			#Erlang 
'.f'		    => 'fortran',			#Fortran 
'.for'		    => 'fortran',			#Fortran 
'.f#'		    => 'fsharp',			#F# 
'.4gl'		    => 'genero',			#genero 
'.gml'		    => 'gml',		    	#GML 
'.plt'		    => 'gnuplot',			#Gnuplot 
'.groovy'	    => 'groovy',			#Groovy 
'.gs'		    => 'haskell',			#Haskell 
'.lgs'		    => 'haskell',			#Haskell 
'.lhs'		    => 'haskell',			#Haskell 
'.html'		    => 'html4strict',		#HTML
'.htm'		    => 'html4strict',		#HTML 
'.xhtml'	    => 'html4strict',		#HTML 
'.shtml'	    => 'html4strict',		#HTML 
'.ini'		    => 'ini',		    	#INI 
'.io'		    => 'io',		    	#Io 
'.java'		    => 'java',		    	#Java 
'.js'		    => 'javascript',    	#Javascript 
'.latex'	    => 'latex',		    	#LaTeX 
'.tex'		    => 'latex',		    	#LaTeX 
'.lsp'		    => 'lisp',		    	#Lisp 
'.lisp'		    => 'lisp',		    	#Lisp 
'.lol'		    => 'lolcode',			#LOLcode 
'.lss'		    => 'lotusscript',		#LotusScript 
'.lua'		    => 'lua',		    	#Lua 
'.mrc'		    => 'mirc',		    	#mIRC Scripting 
'.mxml'		    => 'mxml',		    	#MXML 
'.nsi'		    => 'nsis',		    	#NSIS 
'.nsh'		    => 'nsis',		    	#NSIS 
'.m'		    => 'objc',		    	#Objective-C 
'.oz'		    => 'oz',		    	#OZ 
'.p'		    => 'pascal',			#Pascal
'.pp'		    => 'pascal',			#Pascal 
'.pl'		    => 'perl',		    	#Perl
'.pm'		    => 'perl',		    	#Perl
'.php'		    => 'php',		    	#PHP
'.phtml'	    => 'php',			    #PHP 
'.phps'		    => 'php',			    #PHP 
'.php3'		    => 'php',			    #PHP 
'.php4'		    => 'php',			    #PHP 
'.php5'		    => 'php',			    #PHP 
'.php6'		    => 'php',			    #PHP 
'.pov'		    => 'povray',			#POVRAY 
'.ps1'		    => 'powershell',		#PowerShell 
'.py'		    => 'python',			#Python 
'.pyw'		    => 'python',			#Python 
'.pyc'		    => 'python',			#Python 
'.pyo'		    => 'python',			#Python 
'.pyd'		    => 'python',			#Python 
'.reg'		    => 'reg',			    #Microsoft Registry 
'.r'            => 'rsplus',            #R/SPlus
'.spec'		    => 'rpmspec',			#RPM Specification File 
'.rb'		    => 'ruby',			    #Ruby 
'.rbw'		    => 'ruby',			    #Ruby 
'.scheme'	    => 'scheme',			#Scheme 
'.scm'		    => 'scheme',			#Scheme 
'.smarty'	    => 'smarty',			#Smarty 
'.tpl'		    => 'smarty',			#Smarty 
'.sql'		    => 'sql',			    #SQL 
'.tcl'		    => 'tcl',			    #TCL 
'.vb'		    => 'vb',			    #Visual Basic 
'.vbs'		    => 'vbnet',			    #vb.net 
'.vim'		    => 'vim',			    #Vim Script 
'.bat'		    => 'winbatch',			#Winbatch  
'.cmd'		    => 'winbatch',			#Winbatch 

/* The following are images and need to be displayed as such! */
'.png'          => 'image',             # PNG Image
'.jpg'          => 'image',             # JPEG Image
'.jpeg'         => 'image',             # JPEG Image
'.gif'          => 'image',             # GIF Image
'.bmp'          => 'image',             # Bitmap Image

/* The following are needing to be downloaded! */
'.pdf'          => 'download',
'.doc'          => 'download',
'.xls'          => 'download',
'.ppt'          => 'download',
'.pps'          => 'download', 
'.odt'          => 'download',
'.ods'          => 'download', 
'.odp'          => 'download',
'.odg'          => 'download',
'.odp'          => 'download', 
'.zip'          => 'download', 
'.7z'           => 'download', 
'.tar.gz'       => 'download',
'.tar'          => 'download', 
'.gz'           => 'download', 
'.rar'          => 'download', 
'.iso'          => 'download', 
'.exe'          => 'download', 
'.msi'          => 'download', 
'.dwg'          => 'download', 
'.dmg'          => 'download', 
'.skp'          => 'download', 
'.psd'          => 'download', 
'.psb'          => 'download', 
'.pdd'          => 'download', 
'.psp'          => 'download', 
'.xcf'          => 'download', 
'.bz2'          => 'download', 
'.bzip'         => 'download', 
'.xpi'          => 'download',
'.ace'          => 'download', 
'.flac'         => 'download', 
'.flv'          => 'download', 
'.mkv'          => 'download', 
'.m4a'          => 'download',
'.mp3'          => 'download', 
'.wma'          => 'download', 
'.wmv'          => 'download', 
'.ogg'          => 'download', 
'.oda'          => 'download',
'.odv'          => 'download',  
'.bin'          => 'download', 
'.mp4'          => 'download', 
'.swf'          => 'download'
);

if (!isset($git_embed) && $git_embed != true) {
  $git_embed = false;
}

if (isset($_GET['dl'])) {
	if ($_GET['dl'] == 'targz') {
		write_targz(get_repo_path($_GET['p']));
	} elseif ($_GET['dl'] == 'plain') {
		write_plain();
    } elseif ($_GET['dl'] == 'image') {
        cache_image($_GET['p'], $_GET['b'], $_GET['n']);
	} elseif (in_array($_GET['dl'], $icondesc, true)) {
		write_img_png($_GET['dl']);
	} elseif ($_GET['dl'] == 'dlfile') {
		write_dlfile();
	} elseif ($_GET['dl'] == 'rss2') {
		write_rss2();
	} elseif (in_array($_GET['dl'], $arrowdesc, true)){
		draw_arrow($_GET['dl']);
	}
}

html_header();
html_style();
html_breadcrumbs();
html_pages();

if (isset($_GET['p'])) {
	html_spacer();
	html_summary($_GET['p']);
	html_spacer();
	if ($_GET['a'] == "commitdiff") {
		html_title("diff --git " . $_GET['p'] . " " . $_GET['h']);
		html_diff($_GET['p'], $_GET['h']);
	} elseif (isset($_GET['tr'])) {
		html_title("Files");
		html_browse($_GET['p']);
	}
} else {
	html_spacer();
	html_home();
}

html_title("Help");
if (isset($_GET['p'])) {
	html_help($_GET['p']);
} else {
	html_help("projectname.git ");
}

html_footer();
die();

// ******************************************************

function html_summary($proj) {
	global $nr_of_shortlog_lines;
	$repopath = get_repo_path($proj);
	html_summary_title();
	html_desc($repopath);
	if (!isset($_GET['t']) && !isset($_GET['b'])) {
		html_shortlog($proj, $nr_of_shortlog_lines);
	} else{
		html_shortlog($proj, 4);
	}
}

function html_browse($proj) {
	if (isset($_GET['b'])) {
		html_blob($proj, $_GET['b']);
	} else {
		if (isset($_GET['t'])) {
			$tree = $_GET['t'];
		} elseif (isset($_GET['tr'])) {
			$tree = $_GET['tr'];
		} else {
			$tree = "HEAD";
		}
		html_tree($proj, $tree);
	}
}

function html_help($proj) {
	global $CONFIG;
	echo "<div id=\"git-help\">\n";
	echo "<table>\n";
	echo "<tr><td>To clone: </td><td>git clone ";
	echo $CONFIG['git_method_prefix'];
	echo $proj . " yourpath</td></tr>\n";
	echo "<tr><td>To communicate: </td><td><a href=\"" . $CONFIG['communication_link'] . "\">Visit this page</a></td></tr>";
	echo "</table>\n";
	echo "</div>\n";
}

function html_blob($proj, $blob) {
	global $extGeSHi, $CONFIG;
	$repopath = get_repo_path($proj);
	$out = array();
	$name = $_GET['n'];
    $name_c = strtolower($name);
	$plain = html_ahref(array('p' => $proj, 'dl' => "plain", 'h' => $blob, 'n' => $name)) . "plain</a>";
	$ext =@ $extGeSHi[strrchr($name_c, ".")];
	echo "<div style=\"float:right;padding:7px;\">" . $plain . "</div>\n";
	//echo "$ext";
	echo "<div class=\"gitcode\">\n";
	if ($ext == "") {
		//echo "nonhighlight!";
		$cmd = "GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix']) . " git cat-file blob " . escapeshellarg($blob) . " 2>&1";
		exec($cmd, &$out);
		$out = "<pre>" . htmlspecialchars(implode("\n", $out)) . "</pre>";
		echo $out;
	} elseif($ext == "download") {
        //echo "download";
        echo "<div id=\"download_blob\"><span class=\"strong\">" . $name . "</span> | " . $blob . " | <a href=\"" . sanitized_url() . "dl=dlfile&p=" . $proj . "&h=" . $blob . "&n=" . $name . "\">download</a></div>";
    } elseif($ext == "image") {
        //echo "image";
        echo "<a href=\"" . sanitized_url() . "dl=image&p=" . $proj . "&b=" . $blob . "&n=" . $name . "\"><img src=\"" . sanitized_url() . "dl=image&p=" . $proj . "&b=" . $blob . "&n=" . $name . "\" id=\"image_blob\" /></a>";
    } else {
		//echo "highlight";
        $geshiCache = $CONFIG['cache_directory'] . $proj . "/blob-" . $blob;
		if(!file_exists($geshiCache)) {
		    $cmd = "GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix']) . " git cat-file blob " . escapeshellarg($blob) .
			    " 2>&1";
		    exec($cmd, &$out);
		    $out = implode("\n", $out);
            $out = geshi_highlight($out, $ext);
            $out = geshi_style() . $out;
            $fp = fopen($geshiCache, "w");
            fwrite($fp, $out);
            fclose($fp);
            chmod($geshiCache, 0666);
        } else {
            $fp = fopen($geshiCache, "r");
            $out = fread($fp, filesize($geshiCache) + 1024);
            fclose($fp);
        }
		echo $out;
	}
	echo "</div>\n";
}

function html_diff($proj, $commit) {
	global $CONFIG;
	$repopath = get_repo_path($proj);
	//$c = git_commit($proj, $commit);
	$c['parent'] = $_GET['hb'];
	$out = array();
    $geshiCache = $CONFIG['cache_directory'] . $proj . "/diff-" . $commit;
		if(!file_exists($geshiCache)) {
		    exec("GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix']) . " git diff " . escapeshellarg($c['parent']) . " " .
	        escapeshellarg($commit) . " 2>&1", &$out);
		    $out = implode("\n", $out);
            $out = geshi_highlight($out, 'diff');
            $out = geshi_style() . $out;
            $fp = fopen($geshiCache, "w");
            fwrite($fp, $out);
            fclose($fp);
            chmod($geshiCache, 0666);
        } else {
            $fp = fopen($geshiCache, "r");
            $out = fread($fp, filesize($geshiCache) + 1024);
            fclose($fp);
        }

	echo "<div class=\"gitcode\">\n";
	echo $out;
	echo "</div>\n";
}

function html_tree($proj, $tree) {
	global $extGeSHi;
	$t = git_ls_tree(get_repo_path($proj), $tree);

	echo "<div class=\"gitbrowse\">\n";
	echo "<table>\n";
	foreach ($t as $obj) {
		$plain = "";
		$dlfile = "";
		$icon = "";
		$perm = perm_string($obj['perm']);
		if ($obj['type'] == 'tree') {
			$objlink = html_ahref(array('p' => $proj, 'a' => "jump_to_tag", 't' => $obj['hash'])) . $obj['file'] . "</a>\n";
			$icon = "<img src=\"".sanitized_url()."dl=icon_folder\" style=\"border-width: 0px;\"/>";
		}
		elseif ($obj['type'] == 'blob') {
			$plain = html_ahref(array('p' => $proj, 'dl' => "plain", 'h' => $obj['hash'], 'n' => $obj['file'])) . "plain</a>";
			$dlfile = " | " . html_ahref(array('p' => $proj, 'dl' => "dlfile", 'h' => $obj['hash'], 'n' => $obj['file'])) . "file</a>";
			$objlink = html_ahref(array('p' => $proj, 'a' => "jump_to_tag", 'b' => $obj['hash'], 'n' => $obj['file']), "blob") . $obj['file'] . "</a>\n";
			$ext = @$extGeSHi[strrchr($obj['file'], ".")];
			if ($ext == "") {
				$icon = "<img src=\"" . sanitized_url() . "dl=icon_plain\" style=\"border-width: 0px;\"/>";
            } elseif ($ext == "image") {
                $icon = "<img src=\"" . sanitized_url() . "dl=icon_image\" style=\"border-width: 0px;\"/>";
			} else {
				$icon = "<img src=\"" . sanitized_url() . "dl=icon_color\" style=\"border-width: 0px;\"/>";
			}
		}
		echo "<tr><td>" . $perm . "</td><td>" . $icon . "</td></td><td>" . $objlink . "</td><td>" . $plain . $dlfile . "</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
}

function html_shortlog($proj, $lines) {
	global $CONFIG, $branches, $tags, $nr_of_shortlog_lines;
	$page = 0;
	$shortc["top"] = array();
	$shortc["bot"] = array();
	if (isset($_GET['pg'])) {
		$page = $_GET['pg'];
	}
	if ($page < 0) {
		$page = 0;
	}
	echo "</br><div class=\"git-home\"><div class=\"imgtable\">\n";
	echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	switch($_GET['a']) {
		case "commitdiff":
			$order = create_images_parents($proj, $page, $lines, $_GET['h'], $shortc);
			break;
		case "jump_to_tag":
			if (isset($_POST['tag']) && $_POST['tag'] != "") {
				$start = $_POST['tag'];
			} elseif (isset($_POST['branch']) && $_POST['branch'] != "") {
				$start = $_POST['branch'];
			} elseif (isset($_GET['tag']) && $_GET['tag'] != "") {
				$start = $_GET['tag'];
			} else {
				$start = "HEAD";
			}
			if ($start != "") {
				$order = create_images_starting($proj, $page, $lines, $start, $shortc);
				break;
			}
		default:
			$order = create_images($proj, $page, $lines, $shortc);
			break;
	}
	$treeid = "";
	if (isset($_GET['tr'])) {
		$treeid = $_GET['tr'];
	}
	//echo $treeid;
	echo "<tr><th>Date</th><th>Graph</th><th>Commiter</th><th>Summary</th><th>Actions</th></tr>\n";
	echo "<tr class=\"inl\"><td></td><td>";
	for ($i = 0; $i < count($shortc["top"]); $i++) {
		if ($shortc["top"][$i] != ".") {
			echo html_ahref(array('p' => $_GET['p'], 'a' => "jump_to_tag", 'tag' => $shortc["top"][$i])) . html_ref(array('dl' => "up"), "<img src=\"")."</a>";
		} else {
			echo html_ref(array('dl' => "none"), "<img src=\"");
		}
	}
	echo "</td><td></td><td></td><td></td></tr></div>\n";
	for ($i = 0; ($i < $lines) && ($order[$i]!= ""); $i++) {
		$c = git_commit($proj, $order[$i]);
		//var_dump($c);
		$date = date($CONFIG['git_date_format'], (int)$c['date']);
		$cid = $order[$i];
		$pid = $c['parent'];
		$mess = short_desc($c['message'], 40);
		$auth = short_desc($c['author'], 25);
		$tid = $c['tree'];
		// different ways of displaying diff
		if ($_GET['a'] == "commitdiff") {
			if ($_GET['h'] == $cid) {
				$diff = "diff";
			} elseif ($_GET['hb'] == $cid) {
				$diff = "pare";
			} else {
				$diff = html_ahref(array('p' => $_GET['p'], 'a' => "commitdiff", 'h' => $order[0], 'hb' => $cid, 'pg' => "", 'tr' => "")) . "pare</a>";
			}
		} elseif ($pid == "") {
			$diff = "diff";
		} else {
			$diff = html_ahref(array('p' => $_GET['p'], 'a' => "commitdiff", 'h' => $cid, 'hb' => $pid, 'pg' => "", 'tr' => "")) . "diff</a>";
		}
		// displaying tree
		if ($tid == $treeid) {
			$tree = "tree";
		} else {
			$tree = html_ahref(array('p' => $_GET['p'], 'a' => "jump_to_tag", 'tag' => $cid, 'tr' => $tid, 't' => $tid, 'pg' => "")) . "tree</a>";
		}
		echo "<tr><td>{$date}</td>";
		echo "<td>" . html_ahref(array('p' => $_GET['p'], 'a' => "jump_to_tag", 'tag' => $cid)) . "<img src=\"" . $CONFIG['repo_http_relpath'] . $CONFIG['cache_name'] . $proj . "/graph-" . $cid . ".png\" /></a></td>";
		echo "<td>{$auth}</td><td>";
		if (in_array($cid,$branches)) {
			foreach($branches as $symbolic => $hashic) {
				if ($hashic == $cid) {
					echo "<branches>" . $symbolic . "</branches> ";
				}
			}
		}
		if (in_array($cid,$tags)) {
			foreach($tags as $symbolic => $hashic) {
				if ($hashic == $cid) {
					echo "<tags>" . $symbolic . "</tags> ";
				}
			}
		}
		echo $mess;
		echo "</td><td>{$diff} | {$tree} | " . get_project_link($proj, "targz", $cid) . "</td></tr>\n"; 
		if ($_GET['a'] == "commitdiff") {
			echo "<tr class=\"inl\"><td>-</td></tr>\n";
		}
	}
	echo "<tr class=\"inl\" ><td></td><td>";
	for ($i = 0; $i < count($shortc["bot"]); $i++) {
		if ($shortc["bot"][$i] != ".") {
			echo html_ahref(array('p' => $_GET['p'], 'a' => "jump_to_tag", 'tag' => $shortc["bot"][$i])) . html_ref(array('dl' => "down"), "<img src=\"") . "</a>";
		} else {
			echo html_ref(array('dl' => "none"), "<img src=\"");
		}
	}
	echo "</td><td></td><td></td><td></td></tr>\n";
	$n = 0;
	$maxr = git_number_of_commits($proj);
	echo "</table><table>";
	echo "<tr height=\"20\"><td>";
	for ($j = -7; $n < 15; $j++) {
		$i = $page + $j * $j * $j * $lines/2;
		if ($i < 0) {
			continue;
		}
		if ($n>0) {
			echo " | ";
		}
		$n++;
		if ($i > $maxr) {
			$i = $maxr;
		}
		if ($i == $maxr) {
			echo "Total commits :$i";
			break;
		}
		if ($i == $page) {
			echo "<b>[{$i}]</b>\n";
		} else {
			echo html_ahref(array('p' => $_GET['p'], 'pg' => $i, 'tr' => "", 'tag' => "")) ."{$i}</a>\n";
		} if ($i == $maxr) {
			break;
		}
	}
	echo "</td></tr>\n";
	echo "</table></div></div>\n";
}

function html_summary_title() {
	global $branches, $tags, $nr_of_shortlog_lines;
	if ($_GET['a'] != "commitdiff") {
		echo html_ref(array('p' => $_GET['p'], 'a' => "jump_to_tag"),"<form method=post action=\"");
		echo "<div class=\"gittitle\">Summary :: ";
		echo "<select name=\"branch\">";
		echo "<option selected value=\"\">select a branch</option>";
		foreach(array_keys($branches) as $br) {
			echo "<option value=\"" . $br . "\">" . $br . "</option>";
		}
		echo "</select> or <select name=\"tag\">";
		echo "<option selected value=\"\">select a tag</option>";
		foreach(array_keys($tags) as $br) {
			echo "<option value=\"" . $br . "\">" . $br . "</option>";
		}
		echo "</select> and press <input type=\"submit\" name=\"branch_or_tag\" value=\"GO\">";
		echo " Lines to display <input type=\"text\" name=\"nr_of_shortlog_lines\" value=\"" . $nr_of_shortlog_lines . "\" size=\"3\"> <input type=\"submit\" name=\"branch_or_tag\" value=\"SET\"> \n";
		echo "</div></form>";
	} else {
		echo "<div class=\"gittitle\">Summary</div>\n";
	}
}

function git_parse($proj, $what) {
	global $CONFIG;
	$cmd1 = "GIT_DIR=" . get_repo_path($proj) . $CONFIG['repo_suffix'] . " git rev-parse  --symbolic --" . escapeshellarg($what) . "  2>&1";
	$out1 = array();
	$bran = array();
	exec($cmd1, &$out1);
	for($i = 0; $i < count($out1); $i++) {
		$cmd2="GIT_DIR=" . get_repo_path($proj) . $CONFIG['repo_suffix'] . " git rev-list --max-count=1 " . escapeshellarg($out1[$i]) . " 2>&1";
		$out2 = array();
		exec($cmd2, &$out2);
		$bran[$out1[$i]] = $out2[0];
		//echo "$out1[$i] $i $out2[0]<br>";
	}
	return $bran;
}

function html_desc($repopath) {
	global $CONFIG;
	$desc = file_get_contents($repopath . $CONFIG['repo_suffix'] . "/description");
	$owner = get_file_owner($repopath);
	$last =  get_last($repopath);
	echo "<div id=\"git-description\">\n";
	echo "<table>\n";
	echo "<tr><td>description</td><td>" . $desc . "</td></tr>\n";
	echo "<tr><td>owner</td><td>" . $owner . "</td></tr>\n";
	echo "<tr><td>last change</td><td>" . $last . "</td></tr>\n";
	echo "</table>\n";
	echo "</div>\n";
}

function html_home() {
	global $repos, $CONFIG;
	echo "<div class=\"git-home\">\n";
	echo "<table>\n<tr>";
	echo "<th>Project</th>";
	echo "<th>Description</th>";
	echo "<th>Owner</th>";
	echo "<th>Last Changed</th>";
	echo "<th>Download</th>";
	echo "<th>Hits</th>";
	echo "</tr>\n";
	foreach ($repos as $repo) {
		$today = 0; $total = 0; stat_get_count($repo, $today, $total);
		$desc = short_desc(file_get_contents($repo . $CONFIG['repo_suffix'] . "description"));
		$owner = get_file_owner($repo);
		$last =  get_last($repo);
		$proj = get_project_link($repo);
		$dlt = get_project_link($repo, "targz");
		echo "<tr><td>" . $proj . "</td><td>" . $desc . "</td><td>" . $owner . "</td><td>" . $last . "</td><td>" . $dlt ."</td><td> (" . $today . " / " . $total . ") </td></tr>\n";
	}
	echo "</table>";
	echo "</div>\n";
}

function get_git($repo) {
	global $CONFIG;
	if (file_exists($repo . $CONFIG['repo_suffix'])) {
		$gitdir = $repo . $CONFIG['repo_suffix'];
	} else {
		$gitdir = $repo;
	}
	return $gitdir;
}

function get_file_owner($repopath) {
	//$s = stat($path);
	//print_r($s);
	//$pw = posix_getpwuid($s['uid']);
	//echo("owner1");
	//return preg_replace("/[,;]/", "", $pw["gecos"]);
	global $CONFIG;
	$out = array();
	$cmd = "GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix']) . " git rev-list  --header --max-count=1 HEAD 2>&1 | grep -a committer | cut -d' ' -f2-3";
	$own = exec($cmd, &$out);
	return $own;
}

function get_last($repopath) {
	global $CONFIG;
	$out = array();
	$cmd = "GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix']) . " git rev-list  --header --max-count=1 HEAD 2>&1 | grep -a committer | cut -d' ' -f5-6";
	$date = exec($cmd, &$out);
	return date($CONFIG['git_date_format'], (int)$date);
}

function get_project_link($repo, $type = false, $tag="HEAD") {
	$path = basename($repo);
	if (!$type) {
		return "<a href=\"" . sanitized_url() . "p=" . $path . "\">" . $path . "</a>";
	} elseif ($type == "targz") {
		return html_ahref(array('p' => $path, 'dl'=>'targz', 'h' => $tag)) . "snapshot</a>";
	}
}

function git_commit($proj, $cid) {
	global $CONFIG;
	$out = array();
	$commit = array();

	if (strlen($cid) <= 0){
		return 0;
	}
	$cmd = "GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git rev-list --max-count=1 --pretty=format:\"";
	$cmd .= "parents %P%ntree %T%nauthor %an%ndate %at%nmessage %s%nendrecord%n\" " . $cid . " 2>&1";

	exec($cmd, &$out);

	foreach($out as $line) {
		// tking the data descriptor
		unset($d);
		$d = explode(" ", $line);
		$descriptor = $d[0];
		$d = array_slice($d, 1);
		switch($descriptor) {
			case "commit":
				$commit["commit_id"] = $d[0];
				break;
			case "parents":
				$commit["parent"] = $d[0];
				break;
			case "tree":
				$commit["tree"] = $d[0];
				break;
			case "author":
				$commit["author"] = implode(" ", $d);
				break;
			case "date":
				$commit["date"] = $d[0];
				break;
			case "message":
				$commit["message"] = implode(" ", $d);
				break;
			case "endrecord":
				break;
		}
	}
	return $commit;
}

function git_ls_tree($repopath, $tree) {
	global $CONFIG;
	$ary = array();

	$out = array();
	//Have to strip the \t between hash and file
	exec("GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix']) . " git ls-tree " . escapeshellarg($tree) . " 2>&1 | sed -e 's/\t/ /g'", &$out);

	foreach ($out as $line) {
		$entry = array();
		$arr = explode(" ", $line, 4);
		$entry['perm'] = $arr[0];
		$entry['type'] = $arr[1];
		$entry['hash'] = $arr[2];
		$entry['file'] = $arr[3];
		$ary[] = $entry;
	}
	return $ary;
}

function write_plain() {
	global $CONFIG;
	$repopath = get_repo_path($_GET['p']);
	$name = $_GET['n'];
	$hash = $_GET['h'];
	$out = array();
	exec("GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix'])  . " git cat-file blob " . escapeshellarg($hash) . " 2>&1", &$out);
	header("Content-Type: text/plain");
	echo implode("\n",$out);
	die();
}

function write_dlfile() {
	global $CONFIG;
	$repopath = get_repo_path($_GET['p']);
	$name = $_GET['n'];
	$hash = $_GET['h'];
	exec("GIT_DIR=" . escapeshellarg($repopath . $CONFIG['repo_suffix'])  . " git cat-file blob " . escapeshellarg($hash) . " 2>&1 > " . escapeshellarg("/tmp/" . $hash . "." . $name));
	$filesize = filesize("/tmp/" . $hash . "." . $name);
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false); // required for certain browsers
	header("Content-Transfer-Encoding: binary");
	//header("Content-Type: application/x-tar-gz");
	header("Content-Length: " . $filesize);
	header("Content-Disposition: attachment; filename=\"" . $name . "\";");
	//$str = system("GIT_DIR=$repo git-cat-file blob $hash 2>/dev/null");
	readfile("/tmp/" . $hash . "." . $name);
	die();
}

function hash_to_tag($hash) {
	global $tags;
	if (in_array($hash, $tags, true)) {
		return array_search($hash, $tags, true);
	}
	return $hash;
}

function write_targz($repo) {
	global $CONFIG;
	$p = basename($repo);
	$head = hash_to_tag($_GET['h']);
	$proj = explode(".", $p);
	$proj = $proj[0]; 
	exec("cd /tmp && git-clone " . escapeshellarg($repo) . " " . escapeshellarg($proj) . " && cd ".
		escapeshellarg($proj) . " && git-checkout " . escapeshellarg($head).
		" && cd /tmp && rm -Rf " . escapeshellarg("/tmp/ " . $proj . "/.git") . " && tar czvf " .
		escapeshellarg($proj . "-" . $head . ".tar.gz") . " " . escapeshellarg($proj));
	exec("rm -Rf " . escapeshellarg("/tmp/" . $proj));

	$filesize = filesize("/tmp/" . $proj . "-" . $head . ".tar.gz");
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Transfer-Encoding: binary");
	header("Content-Type: application/x-tar-gz");
	header("Content-Length: " . $filesize);
	header("Content-Disposition: attachment; filename=\"" . $proj . "-" . $head . ".tar.gz\";");
	readfile("/tmp/" . $proj . "-" . $head . ".tar.gz");
	die();
}

function write_rss2() {
	$proj = $_GET['p'];
	//$repo = get_repo_path($proj);
	$link = "http://" . $_SERVER['HTTP_HOST'] . sanitized_url() . "p=" . $proj;
	$c = git_commit($proj, "HEAD");

	header("Content-type: text/xml", true);

	echo '<?xml version="1.0" encoding="UTF-8"?>';
	?>
	<rss version="2.0"
		xmlns:content="http://purl.org/rss/1.0/modules/content/"
		xmlns:wfw="http://wellformedweb.org/CommentAPI/"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
	>

		<channel>
			<title><?php echo $proj ?></title>
			<link><?php echo $link ?></link>
			<description><?php echo $proj ?></description>
			<pubDate><?php echo date('D, d M Y G:i:s', $c['date'])?></pubDate>
			<generator>http://github.com/xanmanning/git-php</generator>
			<language>en</language>
			<?php for ($i = 0; $i < 10 && $c; $i++) { ?>
			<item>
				<title><?php echo $c['message'] ?></title>
				<link><?php echo $link?></link>
				<pubDate><?php echo date('D, d M Y G:i:s', $c['date'])?></pubDate>
				<guid isPermaLink="false"><?php echo $link ?></guid>
				<description><?php echo $c['message'] ?></description>
				<content><?php echo $c['message'] ?></content>
			</item>
			<?php
				$c = git_commit($proj, $c['parent']);
				$link = "http://" . $_SERVER['HTTP_HOST'] . sanitized_url() . "p=" . $proj . "&amp;a=commitdiff&amp;h=" . $c['commit_id'] . "&amp;hb=" . $c['parent'] . "&amp;tm=0";
				}
			?>
		</channel>
	</rss>
<?php
	die();
}

function perm_string($perms) {
	//This sucks
	switch ($perms) {
		case '040000':
			return 'drwxr-xr-x';
		case '100644':
			return '-rw-r--r--';
		case '100755':
			return '-rwxr-xr-x';
		case '120000':
			return 'lrwxrwxrwx';
		default:
			return '----------';
	}
}

function short_desc($desc, $size=25) {
	$trunc = false;
	$short = "";
	$d = explode(" ", $desc);
	foreach ($d as $str) {
		if (strlen($short) < $size) {
			$short .= $str . " ";
		} else {
			$trunc = true;
			break;
		}
	}

	if ($trunc)
	$short .= "...";

	return $short;
}

function zpr($arr) {
	print "<pre>" . print_r($arr, true) . "</pre>";
}

function highlight($code) {
	if (substr($code, 0, 2) != '<?') {
		$code = "<?\n" . $code . "\n?>";
		$add_tags = true;
	}
	$code = highlight_string($code, 1);

	if ($add_tags) {
		//$code = substr($code, 0, 26).substr($code, 36, (strlen($code) - 74));
		$code = substr($code, 83, strlen($code) - 140);    
		$code .= "</span>";
	}

	return $code;
}

function highlight_code($code) {
	define(COLOR_DEFAULT, '000');
	define(COLOR_FUNCTION, '00b'); //also for variables, numbers and constants
	define(COLOR_KEYWORD, '070');
	define(COLOR_COMMENT, '800080');
	define(COLOR_STRING, 'd00');

	// Check it if code starts with PHP tags, if not: add 'em.
	if (substr($code, 0, 2) != '<?') {
		$code = "<?\n" . $code . "\n?>";
		$add_tags = true;
	}

	$code = highlight_string($code, true);

	// Remove the first "<code>" tag from "$code" (if any)
	if (substr($code, 0, 6) == '<code>') {
		$code = substr($code, 6, (strlen($code) - 13));
	}

	// Replacement-map to replace deprecated "<font>" tag with "<span>"
	$xhtml_convmap = array(
		'<font' => '<span',
		'</font>' => '</span>',
		'color="' => 'style="color:',
		'<br />' => '<br/>',
		'#000000">' => '#' . COLOR_DEFAULT . '">',
		'#0000BB">' => '#' . COLOR_FUNCTION . '">',
		'#007700">' => '#' . COLOR_KEYWORD . '">',
		'#FF8000">' => '#' . COLOR_COMMENT . '">',
		'#DD0000">' => '#' . COLOR_STRING . '">'
	);

	// Replace "<font>" tags with "<span>" tags, to generate a valid XHTML code
	$code = strtr($code, $xhtml_convmap);

	//strip default color (black) tags
	$code = substr($code, 25, (strlen($code) -33));

	//strip the PHP tags if they were added by the script
	if ($add_tags) {
		$code = substr($code, 0, 26).substr($code, 36, (strlen($code) - 74));
	}

	return $code;
}

function git_number_of_commits($proj) {
	global $CONFIG;

	$cmd = "GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git rev-list --all --full-history 2>&1 | grep -c \"\" ";
	unset($out);
	$out = array();
	//echo "$cmd\n";
	$rrv = exec($cmd, &$out);
	return intval($out[0]);
}

// *****************************************************************************
// Graph tree drawing section
//
function create_cache_directory($repo) {
	global $CONFIG;
	$dirname = $CONFIG['cache_directory'].$repo;

	create_directory($dirname);
}

function analyze_hierarchy(&$vin, &$pin, &$commit, &$coord, &$parents, &$nr, &$childs) {
	// figure out the position of this node
	if (in_array($commit, $pin, true)) {
		// take reserved coordinate
		$coord[$nr] = array_search($commit, $pin, true);
		// free the reserved coordinate
		$pin[$coord[$nr]] = ".";
		$childs[$coord[$nr]] = ".";
	} else {
		// this commit appears to be a head
		if (! in_array(".", $pin, true)) {
			// make empty coord plce
			$pin[] = ".";
			$vin[] = ".";
			$childs[] = ".";
		}
		// take the first unused coordinate
		$coord[$nr] = array_search(".", $pin, true);
		// do not allocate this place in array as this is already freed place
	}
	//reserve place for parents
	$pc = 0;
	foreach($parents as $p) {
		if (in_array($p, $pin, true)) {
			// the parent alredy has place
			$childs[array_search($p, $pin, true)] = $commit; // register child
			$pc++;
			continue; 
		} 
		if ($pc == 0) {
			// try to keep the head straigth
			$pin[$coord[$nr]] = $p;
			$childs[$coord[$nr]] = $commit;
			$pc++;
			continue;
		}
		if (in_array(".", $pin, true)) { 
			// 1. find nearest free place in the left side
			$i = -1;
			for($i = $coord[$nr]-1; $pin[$i] != "." && $i >= 0; $i--);
			// 2. find neares free place in the right side
			if ($i < 0) {
				for($i = $coord[$nr]; $pin[$i] != "."; $i++);
			}
			//$x = array_search(".", $pin, true);
			$pin[$i] = $p;
			$childs[$i] = $commit;
		} else { // allcate new place into array
			$pin[] = $p;
			$vin[] = ".";
			$childs[] = $commit;
		}
	}
	//reduce image width if possible
	while(count($pin) > ($coord[$nr]+1)) {
		$valpin = array_pop($pin);
		$valvin = array_pop($vin);
		$valchi = array_pop($childs);
		if ($valpin == "." && $valvin == ".") {
			continue;
		}
		$pin[] = $valpin;
		$vin[] = $valvin;
		$childs[] = $valchi;
		break;
	}
}

function create_images_starting($proj, &$retpage, $lines, $commit_name, &$shortc) {
	global $CONFIG;
	$dirname = $CONFIG['cache_directory'] . $proj;
	create_cache_directory($proj);

	$cmd = "GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git rev-list --max-count=1 " . escapeshellarg($commit_name) . " 2>&1";
	unset($out);
	$out = array();

	//echo "$cmd\n";
	$rrv = exec($cmd, &$out);
	$commit_start = $out[0];
	//echo "$commit_start\n";

	$page = -1; // the counter of made lines
	$order = array(); // the commit sha-s
	$coord = array(); // holds X position in tree
	$pin = array("."); // holds reserved X positions in tree
	$cross = array(); // lists rows that participate on the drawing of the slice as xstart,ystart,xend,yend,xstart,ystart,xend,yend,...
	$count = array(); // holds number of open lines, if this becomes 0, the slice can be drawn
	$crossf = array(); // the floating open lines section
	$countf = 0; // the counter of unknown coordinates of floating section
	$nr = 0; // counts rows
	$top = 0; // the topmost undrawn slice
	$todo = array($commit);
	$todoc = 1;
	$shortc["top"] = array(); // shortcut commits on the top of the graph
	$shortc["bot"] = array(); // shortcut commits on the bottom of the graph
	$childs = array("."); // sliding place for shortc[top]
	do{
		unset($cmd);
		$cmd="GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git rev-list --all --full-history --topo-order ";
		$cmd .= "--max-count=1000 --skip=" . escapeshellarg($nr) . " --pretty=format:\"parents %P%nendrecord%n\" 2>&1";
		unset($out);
		$out = array();

		//echo "$cmd\n";
		$rrv = exec($cmd, &$out);
		//echo implode("\n",$out);

		// reading the commit tree
		$descriptor = "";
		$commit = "";
		$parents = array();
		foreach($out as $line) {
			if ($page >= $lines -1) {
				$shortc["bot"] = $pin;
				return $order; // break the image creation if more is not needed
			}
			// taking the data descriptor
			unset($d);
			$d = explode(" ", $line);
			$descriptor = $d[0];
			$d = array_slice($d, 1);
			switch($descriptor) {
				case "commit":
					$commit = $d[0];
					break;
				case "parents":
					$parents = $d;
					break;
				case "endrecord":
				if ($page >=0 || $commit == $commit_start) {
					$page++;
					$order[$page] = $commit;
					if ($page == 0) {
						$retpage = $nr;
						$shortc["top"] = $childs;
					}
				}
				$vin = $pin;
				analyze_hierarchy($vin, $pin, $commit, $coord, $parents, $nr, $childs);
				if ($page >= 0) {
					draw_slice($dirname, $commit, $coord[$nr], $nr, $parents, $pin, $vin);
				}
				merge_slice($coord[$nr], $parents, $pin);
				unset($vin);
				//take next row
				$nr = $nr +1;
				unset($descriptor);
				unset($commit);
				unset($parents);
				$parents = array();
				break;
			}
		}
	} while(count($out) > 0);
	unset($out);
	$rows = $nr;
	$cols = count($pin);
	unset($pin, $nr);
	//echo "number of items ".$rows."\n";
	//echo "width ".$cols."\n";

	return $order;
}

function create_images_parents($proj, &$retpage, $lines, $commit, &$shortc) {
	global $CONFIG;
	$dirname = $CONFIG['cache_directory'] . $proj;
	create_cache_directory($proj);

	$page = 0; // the counter of made lines
	$order = array(); // the commit sha-s
	$coord = array(); // holds X position in tree
	$pin = array("."); // holds reserved X positions in tree
	$cross = array(); // lists rows that participate on the drawing of the slice as xstart,ystart,xend,yend,xstart,ystart,xend,yend,...
	$count = array(); // holds number of open lines, if this becomes 0, the slice can be drawn
	$crossf = array(); // the floating open lines section
	$countf = 0; // the counter of unknown coordinates of floating section
	$nr = 0; // counts rows
	$top = 0; // the topmost undrawn slice
	$todo = array($commit);
	$todoc = 1;
	// in this function we do not fill the arrays
	$shortc["top"] = array(); // shortcut commits on the top of the graph
	$shortc["bot"] = array(); // shortcut commits on the bottom of the graph
	$childs = array("."); // sliding place fore shortc[top]
	do{
		unset($cmd);
		$cmd = "GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git rev-list --all --full-history --topo-order ";
		$cmd .= "--max-count=1000 --skip=" . escapeshellarg($nr) . " --pretty=format:\"parents %P%nendrecord%n\" 2>&1";
		unset($out);
		$out = array();

		//echo "$cmd\n";
		$rrv = exec($cmd, &$out);
		//echo implode("\n",$out);
            
		// reading the commit tree
		$descriptor = "";
		$commit = "";
		$parents = array();
		foreach($out as $line) {
			if (($page > $lines) or ($todoc <= 0)) {
				// break the image creation if more is not needed
				return $order;
			}
			// taking the data descriptor
			unset($d);
			$d = explode(" ", $line);
			$descriptor = $d[0];
			$d = array_slice($d, 1);
			switch($descriptor) {
				case "commit":
					$commit=$d[0];
					break;
				case "parents":
					$parents=$d;
					break;
				case "endrecord":
					if (in_array($commit, $todo, true)) {
						$order[$page] = $commit; 
						$todoc--;
						if ($page==0) {
							$todo = array_merge($todo, $parents);
							$retpage = $nr;
						}
						$page++;
						$todoc = $todoc + count($parents);
					}
					$vin = $pin;
					analyze_hierarchy($vin, $pin, $commit, $coord, $parents, $nr, $childs);
					if (in_array($commit, $todo, true)) {
						draw_slice($dirname, $commit, $coord[$nr], $nr, $parents, $pin, $vin);
					}
					merge_slice($coord[$nr], $parents, $pin);
					unset($vin);
					//take next row
					$nr = $nr +1;
					unset($descriptor);
					unset($commit);
					unset($parents);
					$parents = array();
					break;
			}
		}
	} while(count($out) > 0);
	unset($out);
	$rows = $nr;
	$cols = count($pin);
	unset($pin,$nr);
	//echo "number of items ".$rows."\n";
	//echo "width ".$cols."\n";

	return $order;
}

function create_images($proj, $page, $lines, &$shortc) {
	global $CONFIG;
	$dirname = $CONFIG['cache_directory'] . $proj;
	create_cache_directory($proj);

	$order = array(); // the commit sha-s
	$coord = array(); // holds X position in tree
	$pin = array("."); // holds reserved X positions in tree
	$cross = array(); // lists rows that participate on the drawing of the slice as xstart,ystart,xend,yend,xstart,ystart,xend,yend,...
	$count = array(); // holds number of open lines, if this becomes 0, the slice can be drawn
	$crossf = array(); // the floating open lines section
	$countf = 0; // the counter of unknown coordinates of floating section
	$nr = 0; // counts rows
	$top = 0; // the topmost undrawn slice
	$shortc["top"] = array(); // shortcut commits on the top of the graph
	$shortc["bot"] = array(); // shortcut commits on the bottom of the graph
	$childs = array("."); // sliding place fore shortc[top]
	do{
		unset($cmd);
		$cmd = "GIT_DIR=" . escapeshellarg($CONFIG['repo_directory'] . $proj . $CONFIG['repo_suffix']) . " git rev-list --all --full-history --topo-order ";
		$cmd .= "--max-count=1000 --skip=" . escapeshellarg($nr) ." --pretty=format:\"parents %P%nendrecord%n\" 2>&1";
		unset($out);
		$out = array();

		//echo "$cmd\n";
		$rrv = exec($cmd, &$out);
		//echo implode("\n",$out);
            
		// reading the commit tree
		$descriptor = "";
		$commit = "";
		$parents = array();
		foreach($out as $line) {
			if ($nr >= $page + $lines -1) {
				$shortc["bot"] = $pin;
				return $order; // break the image creation if more is not needed
			}
			// taking the data descriptor
			unset($d);
			$d = explode(" ", $line);
			$descriptor = $d[0];
			$d = array_slice($d, 1);
			switch($descriptor) {
				case "commit":
					$commit=$d[0];
					break;
				case "parents":
					$parents=$d;
					break;
				case "endrecord":
					if ($nr-$page >= 0) {
						$order[$nr-$page] = $commit;
						if ($nr == $page) {
							$shortc["top"] = $childs;
						}
					}
					$vin = $pin;
					analyze_hierarchy($vin, $pin, $commit, $coord, $parents, $nr, $childs);
					if ($nr >= $page) {
						draw_slice($dirname, $commit, $coord[$nr], $nr, $parents, $pin, $vin);
					}
					merge_slice($coord[$nr], $parents, $pin);
					unset($vin);
					//take next row
					$nr = $nr +1;
					unset($descriptor);
					unset($commit);
					unset($parents);
					$parents = array();
					break;
			}
		}
	} while(count($out) > 0);
	unset($out);
	$rows = $nr;
	$cols = count($pin);
	unset($pin,$nr);
	//echo "number of items ".$rows."\n";
	//echo "width ".$cols."\n";

	return $order;
}

// draw the graph slices
$dr_sl_brcol = array();
function merge_slice($x, $parents, $pin) {
	global $dr_sl_brcol;
	$columns = count($pin);

	$dr_sl_brcol[$x] = "."; // erase the bold branch of this column

	for($i = 0; $i < $columns; $i++) {
		if ($pin[$i] == $parents[0]) {
			$dr_sl_brcol[$i] = "#"; // the vertical becomes branch vertical
		}
	}
}

function draw_slice($dirname, $commit, $x, $y, $parents, $pin, $vin) {
	 global $tags, $branches, $dr_sl_brcol;

	 $w = 7; $wo = 3;
	 $h = 15; $ho = 7;
	 $r = 7; $rj = 8;

	 $columns = count($pin);
	 $lin = array_fill(0, $columns, '-');

	 $im = imagecreate($w * $columns, $h);
	 $cbg = imagecolorallocate($im, 255, 255, 255);
	 $ctr = imagecolortransparent($im, $cbg);
	 $cmg = imagecolorallocate($im, 0, 0, 220);
	 $cbl = imagecolorallocate($im, 0, 0, 0);
	 $crd = imagecolorallocate($im, 180, 0, 0);
	 $cgre= imagecolorallocate($im, 0, 180, 0);
     
	 $cci = imagecolorallocate($im, 150, 150, 150);
	 $ctg = imagecolorallocate($im, 255, 255, 0);
	 $cbr = imagecolorallocate($im, 255, 0, 0);

	 $brlinecol = $cgre;
	 $melinecol = $cmg;
	 $cline = $cmg;

	for($i=0; $i<$columns; $i++) {
		if ($dr_sl_brcol[$i] == "#") {
			$cline = $brlinecol;
		} else {
			$cline = $melinecol;
		}
		if ($vin[$i] == $commit) {
			// small vertical
			if ($dr_sl_brcol[$i] == "#") {
				imageline($im, $i * $w + $wo-1, $ho, $i * $w + $wo-1, 0, $cline);
			}
			imageline($im, $i * $w + $wo, $ho, $i * $w + $wo, 0, $cline);
		}
		if ($pin[$i] != ".") {
			// we have a parent
			if (in_array($pin[$i], $parents, true)) {
				// the parent is our parent
				// draw the horisontal for it
				if ($pin[$i] == $parents[0]) {
					// merge has thin line, main parent has double hor line
					$cline = $brlinecol;
					imageline($im, $i * $w + $wo, $ho, $x * $w + $wo, $ho, $cline);
				} else {
					$cline = $melinecol;
				}
				imageline($im, $i * $w + $wo, $ho-1, $x * $w + $wo, $ho-1, $cline);
				// draw the little vertical for it
				if ($pin[$i] == $parents[0] || $dr_sl_brcol[$i] == "#" && $i != $x) {
					$cline = $brlinecol;
					imageline($im, $i * $w + $wo-1, $ho, $i * $w + $wo-1, $h, $cline);
				}
				imageline($im, $i * $w + $wo, $ho, $i * $w + $wo, $h, $cline);
				// look if this is requested for the upper side
				if ($vin[$i] == $pin[$i]) {
					// small vertical for upper side
					if ($dr_sl_brcol[$i] == "#") {
						$cline = $brlinecol;
						imageline($im, $i * $w + $wo-1, $ho, $i * $w + $wo-1, 0, $cline);
					} else {
						$cline = $melinecol;
					}
					imageline($im, $i * $w + $wo, $ho, $i * $w + $wo, 0, $cline);
				}
				// mark the cell to have horisontal
				$k = $x;
				while($k != $i) {
					$lin[$k] = '#';
					if ($k > $i) {
						$k = $k-1;
					} else {
						$k = $k+1;
					}
				}
			}
		}
	}
	// draw passthrough lines
	for($i=0; $i<$columns; $i++) {
		if ($dr_sl_brcol[$i] == "#") {
			$cline = $brlinecol;
		} else {
			$cline = $melinecol;
		}
		if ($pin[$i] != "." && ! in_array($pin[$i],$parents,true)) {
			// it is not a parent for this node
			// check if we have horisontal for this column
			if ($lin[$i] == '#') {
				// draw pass-by junction
				if ($dr_sl_brcol[$i] == "#") {
					imageline($im, $i * $w + $wo-1, 0, $i * $w + $wo-1, ($h - $rj) / 2, $cline);
					imageline($im, $i * $w + $wo-1, $h-($h - $rj) / 2, $i * $w + $wo-1, $h, $cline);
					if ($i < $x) {
						imagearc($im, $i * $w + $wo, $ho, $rj, $rj+1, 90, 270, $cline);
					} else {
						imagearc($im, $i * $w + $wo, $ho, $rj, $rj+1, 270, 90, $cline);
					}
				}
				imageline($im, $i * $w + $wo, 0, $i * $w + $wo, ($h - $rj) / 2, $cline);
				imageline($im, $i * $w + $wo, $h-($h - $rj) / 2 -1, $i * $w + $wo, $h, $cline);
			} else {
				// draw vertical
				if ($dr_sl_brcol[$i] == "#") {
					imageline($im, $i * $w + $wo-1, 0, $i * $w + $wo-1, $h, $cline);
				}
				imageline($im, $i * $w + $wo, 0, $i * $w + $wo, $h, $cline);
			}
		}
	}

	$fillcolor = $ctr;
	$color = $cmg;

	if (in_array($commit, $tags)) {
		$fillcolor = $ctg;
	}
	if (in_array($commit, $branches)) {
		$color = $crd;
	}

	imagefilledellipse($im, $x * $w + $wo, $ho, $r, $r, $fillcolor);
	imageellipse($im, $x * $w + $wo, $ho, $r, $r, $color);
	$filename = $dirname . "/graph-" . $commit . ".png";
	imagepng($im, $filename);
	chmod($filename, 0777);
	//chgrp($filename, intval(filegroup($CONFIG['repo_directory'])));
	//echo "$filename\n";
}

function draw_arrow($type) {
	$w = 7; $wo = 3;
	$h = 10; $ho = 5;
	$r = 2; $rj = 8;

	$im = imagecreate($w, $h);
	$cbg = imagecolorallocate($im, 255, 255, 255);
	$ctr = imagecolortransparent($im, $cbg);
	$cmg = imagecolorallocate($im, 0, 0, 220);
	$cbl = imagecolorallocate($im, 0, 0, 0);
	$crd = imagecolorallocate($im, 180, 0, 0);
	$cgre= imagecolorallocate($im, 0, 180, 0);
    
	$cci = imagecolorallocate($im, 150, 150, 150);
	$ctg = imagecolorallocate($im, 255, 255, 0);
	$cbr = imagecolorallocate($im, 255, 0, 0);

	$cline = $cbl;

	if ($type == "none") {
		imageellipse($im, $wo, $ho, $r, $r, $cci);
		imagepng($im);
		die();
	} elseif ($type == "up") {
		imageline($im, 1, $ho, $wo, 1, $cline);
		imageline($im, $wo, 1, $w-1, $ho, $cline);
	} elseif ($type == "down") {
		imageline($im, 1, $ho, $wo, $h-2, $cline);
		imageline($im, $wo, $h-2, $w-1, $ho, $cline);
	} else{
		imagepng($im);
		die();
	}
	imageline($im, $wo, 1, $wo, $h-2, $cline);
	imagepng($im);
	die();
}
?>
