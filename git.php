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
// +------------------------------------------------------------------------+ 

    global $title;
    global $repos;
    global $git_embed;
    global $git_css;
    global $git_logo;

    /* Add the default css */
    $git_css = true;

    /* Add the git logo in the footer */
    $git_logo = true;

    $title  = "git";
    $repo_index = "index.aux";

    //repos could be made by an embeder script
    if (!is_array($repos))
        $repos = array();

    if (file_exists($repo_index))   {
        $r = file($repo_index);
        foreach ($r as $repo)
            $repos[] = trim($repo);
    }
    else if((file_exists($repo_directory)) && (is_dir($repo_directory))){
        if ($handle = opendir($repo_directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    /* TODO: Check for valid git repos */
                    $repos[] = trim($repo_directory . $file);
                }
            }
            closedir($handle);
        } 
    }
    else    
        $repos = array(
            "/home/zack/scm/afa.git",
            "/home/zack/scm/nurikabe.git",
            "/home/zack/scm/sudoku.git",
            "/home/zack/scm/bartel.git",
            "/home/zack/scm/rpminfo.git",
            "/home/zack/scm/libshell.git",
            "/home/zack/scm/pycast.git",
            "/home/zack/scm/tectool.git",
            "/home/zack/scm/gastrack.app.git",
/*             "/home/zack/scm/libdb.git", */
/*             "/home/zack/scm/cnas.git", */
/*             "/home/zack/scm/cnas-sm.git", */
/*             "/home/zack/scm/cnas-logos.git", */
/*             "/home/zack/scm/cnas-release.git", */
/*             "/home/zack/scm/cnas-aimsim.git", */
            "/home/zack/scm/survey.git",
            "/home/zack/scm/tftp-hpa.git",
            "/home/zack/scm/git-php.git",
            "/home/zack/scm/git-drupal.git",
            "/home/zack/scm/gobot.git",
            "/home/zack/scm/zoogle.git",
            "/home/zack/scm/hello-servlet.git",
        );

    sort($repos);

    if (!isset($git_embed) && $git_embed != true)
        $git_embed = false;

    foreach ($_GET as $var=>$val)
    {
        $_GET[$var] = str_replace(";", "", $_GET[$var]);
    }

    if (isset($_GET['dl']))
        if ($_GET['dl'] == 'targz') 
            write_targz(get_repo_path($_GET['p']));
        else if ($_GET['dl'] == 'zip')
            write_zip(get_repo_path($_GET['p']));
        else if ($_GET['dl'] == 'git_logo')
            write_git_logo();
        else if ($_GET['dl'] == 'plain')
            write_plain();
        else if ($_GET['dl'] == 'rss2')
            write_rss2();

    html_header();

    html_style();

    html_breadcrumbs();

    if (isset($_GET['p']))  { 
        html_spacer();
        html_title("Summary");
        html_summary($_GET['p']);
        html_spacer();
        if ($_GET['a'] == "commitdiff")
            html_diff($_GET['p'], $_GET['h'], $_GET['hb']);
        else    {
            html_title("Files");
            html_browse($_GET['p']);
        }
    }
    else    {
        html_spacer();
        html_home();
    }

    html_footer();

    function html_summary($proj)    {
        $repo = get_repo_path($proj);
        html_desc($repo);
        if (!isset($_GET['t']) && !isset($_GET['b']))
            html_shortlog($repo, 6);
    }

    function html_browse($proj)   {

        if (isset($_GET['b']))
            html_blob($proj, $_GET['b']);
        else    {
            if (isset($_GET['t']))
                $tree = $_GET['t'];
            else 
                $tree = "HEAD";
             html_tree($proj, $tree); 
        }

    }

    function html_blob($proj, $blob)    {
        $repo = get_repo_path($proj);
        $out = array();
        $plain = "<a href=\"".sanitized_url()."p=$proj&dl=plain&h=$blob\">plain</a>";
        echo "<div style=\"float:right;padding:7px;\">$plain</div>\n";
        exec("GIT_DIR=$repo git-cat-file blob $blob", &$out);
        echo "<div class=\"gitcode\">\n";
        //echo highlight(implode("\n", $out));
        //echo highlight_code(implode("\n",$out));
        highlight_string(implode("\n",$out));
        //echo pretty_code(implode("\n", $out));
        echo "</div>\n";
    }

    function html_diff($proj, $commit, $parent)    {
        $repo = get_repo_path($proj);
        $out = array();
        exec("GIT_DIR=$repo git-diff $parent $commit", &$out);
        echo "<div class=\"gitcode\">\n";
        echo highlight_code(implode("\n",$out));
        echo "</div>\n";
    }

    function html_tree($proj, $tree)   {
        $t = git_ls_tree(get_repo_path($proj), $tree);

        echo "<div class=\"gitbrowse\">\n";
        echo "<table>\n";
        foreach ($t as $obj)    {
            $plain = "";
            $perm = perm_string($obj['perm']);
            if ($obj['type'] == 'tree')
                $objlink = "<a href=\"".sanitized_url()."p=$proj&t={$obj['hash']}\">{$obj['file']}</a>\n";
            else if ($obj['type'] == 'blob')    {
                $plain = "<a href=\"".sanitized_url()."p=$proj&dl=plain&h={$obj['hash']}\">plain</a>";
                $objlink = "<a class=\"blob\" href=\"".sanitized_url()."p=$proj&b={$obj['hash']}\">{$obj['file']}</a>\n";
            }

            echo "<tr><td>$perm</td><td>$objlink</td><td>$plain</td></tr>\n";
        }
        echo "</table>\n";
        echo "</div>\n";
    }

    function html_shortlog($repo, $count)   {
        echo "<table>\n";
        $c = git_commit($repo, "HEAD");
        for ($i = 0; $i < $count && $c; $i++)  {
            $date = date("D n/j/y G:i", (int)$c['date']);
            $cid = $c['commit_id'];
            $pid = $c['parent'];
            $mess = short_desc($c['message'], 110);
            $diff = "<a href=\"".sanitized_url()."p={$_GET['p']}&a=commitdiff&h=$cid&hb=$pid\">commitdiff</a>";
            echo "<tr><td>$date</td><td>{$c['author']}</td><td>$mess</td><td>$diff</td></tr>\n"; 
            $c = git_commit($repo, $c["parent"]);
        }
        echo "</table>\n";
    }

    function html_desc($repo)    {
        
        $desc = file_get_contents("$repo/description"); 
        $owner = get_file_owner($repo);
        $last =  get_last($repo);

        echo "<table>\n";
        echo "<tr><td>description</td><td>$desc</td></tr>\n";
        echo "<tr><td>owner</td><td>$owner</td></tr>\n";
        echo "<tr><td>last change</td><td>$last</td></tr>\n";
        echo "</table>\n";
    }

    function html_home()    {

        global $repos; 
        echo "<table>\n";
        echo "<tr><th>Project</th><th>Description</th><th>Owner</th><th>Last Changed</th><th>Download</th></tr>\n";
        foreach ($repos as $repo)   {
            $desc = short_desc(file_get_contents("$repo/description")); 
            $owner = get_file_owner($repo);
            $last =  get_last($repo);
            $proj = get_project_link($repo);
            $dlt = get_project_link($repo, "targz");
            $dlz = get_project_link($repo, "zip");
            echo "<tr><td>$proj</td><td>$desc</td><td>$owner</td><td>$last</td><td>$dlt | $dlz</td></tr>\n";
        }
        echo "</table>";
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
            echo "\t<link href=\"prettify.css\" type=\"text/css\" rel=\"stylesheet\" />\n";
            echo "\t<script type=\"text/javascript\" src=\"prettify.js\"></script>\n";
            echo "</head>\n";
            echo "<body onload=\"prettyPrint()\">\n";
        }
        /* Add rss2 link */
        if (isset($_GET['p']))  {
            echo "<link rel=\"alternate\" title=\"{$_GET['p']}\" href=\"".sanitized_url()."p={$_GET['p']}&dl=rss2\" type=\"application/rss+xml\" />\n";
        }
        echo "<div id=\"gitbody\">\n";
        
    }

    function write_git_logo()   {

        $git = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a\x00\x00\x00\x0d\x49\x48\x44\x52" .
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
        
        header("Content-Type: img/png");
        header("Expires: +1d");
        echo $git;
        die();
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


    function git_tree_head($gitdir) {
        return git_tree($gitdir, "HEAD");
    }

    function git_tree($gitdir, $tree) {

        $out = array();
        $command = "GIT_DIR=$gitdir git-ls-tree --name-only $tree";
        exec($command, &$out);
    }

    function get_git($repo) {

        if (file_exists("$repo/.git"))
            $gitdir = "$repo/.git";
        else
            $gitdir = $repo;
        return $gitdir;
    }

    function get_file_owner($path)  {
        $s = stat($path);
        $pw = posix_getpwuid($s["uid"]);
        return preg_replace("/[,;]/", "", $pw["gecos"]);
//        $out = array();
//        $own = exec("GIT_DIR=$path git-rev-list  --header --max-count=1 HEAD | grep -a committer | cut -d' ' -f2-3" ,&$out);
//        return $own;
    }

    function get_last($repo)    {
        $out = array();
        $date = exec("GIT_DIR=$repo git-rev-list  --header --max-count=1 HEAD | grep -a committer | cut -f5-6 -d' '", &$out);
        return date("D n/j/y G:i", (int)$date);
    }

    function get_project_link($repo, $type = false)    {
        $path = basename($repo);
        if (!$type)
            return "<a href=\"".sanitized_url()."p=$path\">$path</a>";
        else if ($type == "targz")
            return "<a href=\"".sanitized_url()."p=$path&dl=targz\">.tar.gz</a>";
        else if ($type == "zip")
            return "<a href=\"".sanitized_url()."p=$path&dl=zip\">.zip</a>";
    }

    function git_commit($repo, $cid)  {
        $out = array();
        $commit = array();

        if (strlen($cid) <= 0)
            return 0;

        exec("GIT_DIR=$repo git-rev-list  --header --max-count=1 $cid", &$out);

        $commit["commit_id"] = $out[0];
        $g = explode(" ", $out[1]);
        $commit["tree"] = $g[1];

        $g = explode(" ", $out[2]);
        $commit["parent"] = $g[1];

        $g = explode(" ", $out[3]);
        /* variable number of strings for the name */
        for ($i = 1; $g[$i][0] != '<' && $i < 5; $i++)   {
            $commit["author"] .= "{$g[$i]} ";
        }

        /* add the email */
        $commit["date"] = "{$g[++$i]} {$g[++$i]}";
        $commit["message"] = "";
        $size = count($out);
        for (; $i < $size-1; $i++)
            $commit["message"] .= $out[$i];
        return $commit;
    }

    function get_repo_path($proj)   {
        global $repos;
    
        foreach ($repos as $repo)   {
            $path = basename($repo);
            if ($path == $proj)
                return $repo;
        }
    }

    function git_ls_tree($repo, $tree) {
        $ary = array();
            
        $out = array();
        //Have to strip the \t between hash and file
        exec("GIT_DIR=$repo git-ls-tree $tree | sed -e 's/\t/ /g'", &$out);

        foreach ($out as $line) {
            $entry = array();
            $arr = explode(" ", $line);
            $entry['perm'] = $arr[0];
            $entry['type'] = $arr[1];
            $entry['hash'] = $arr[2];
            $entry['file'] = $arr[3];
            $ary[] = $entry;
        }
        return $ary;
    }

    /* TODO: cache this */
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

    function write_plain()  {
        $repo = get_repo_path($_GET['p']);
        $hash = $_GET['h'];
        header("Content-Type: text/plain");
        $str = system("GIT_DIR=$repo git-cat-file blob $hash");
        echo $str;
        die();
    }

    function write_targz($repo) {
        $p = basename($repo);
        $proj = explode(".", $p);
        $proj = $proj[0]; 
        exec("cd /tmp && git-clone $repo && rm -Rf /tmp/$proj/.git && tar czvf $proj.tar.gz $proj && rm -Rf /tmp/$proj");
        
        $filesize = filesize("/tmp/$proj.tar.gz");
        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // required for certain browsers
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: application/x-tar-gz");
        header("Content-Length: " . $filesize);
        header("Content-Disposition: attachment; filename=\"$proj.tar.gz\";" );
        echo file_get_contents("/tmp/$proj.tar.gz");
        die();
    }

    function write_zip($repo) {
        $p = basename($repo);
        $proj = explode(".", $p);
        $proj = $proj[0]; 
        exec("cd /tmp && git-clone $repo && rm -Rf /tmp/$proj/.git && zip -r $proj.zip $proj && rm -Rf /tmp/$proj");
        
        $filesize = filesize("/tmp/$proj.zip");
        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // required for certain browsers
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: application/x-zip");
        header("Content-Length: " . $filesize);
        header("Content-Disposition: attachment; filename=\"$proj.zip\";" );
        echo file_get_contents("/tmp/$proj.zip");
        die();
    }

    function write_rss2()   {
        $proj = $_GET['p'];
        $repo = get_repo_path($proj);
        $link = "http://{$_SERVER['HTTP_HOST']}".sanitized_url()."p=$proj";
        $c = git_commit($repo, "HEAD");

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
            <generator>http://code.google.com/p/git-php/</generator>
            <language>en</language>
            <?php for ($i = 0; $i < 10 && $c; $i++): ?>
            <item>
                <title><?php echo $c['message'] ?></title>
                <link><?php echo $link?></link>
                <pubDate><?php echo date('D, d M Y G:i:s', $c['date'])?></pubDate>
                <guid isPermaLink="false"><?php echo $link ?></guid>
                <description><?php echo $c['message'] ?></description>
                <content><?php echo $c['message'] ?></content>
            </item>
            <?php $c = git_commit($repo, $c['parent']);
                $link = "http://{$_SERVER['HTTP_HOST']}".sanitized_url()."p=$proj&amp;a=commitdiff&amp;h={$c['commit_id']}&amp;hb={$c['parent']}";
                  endfor;
            ?>
        </channel>
        </rss>
        <?php
        die();
    }

    function perm_string($perms)    {

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

    function short_desc($desc, $size=25)  {
        $trunc = false;
        $short = "";
        $d = explode(" ", $desc);
        foreach ($d as $str)    {
            if (strlen($short) < $size)
                $short .= "$str ";
            else    {
                $trunc = true;
                break;
            }
        }

        if ($trunc)
            $short .= "...";

        return $short;
    }

    function html_spacer($text = "&nbsp;")  {
        echo "<div class=\"gitspacer\">$text</div>\n";
    }

    function html_title($text = "&nbsp;")  {
        echo "<div class=\"gittitle\">$text\n";
        echo "</div>\n";
    }

    function html_breadcrumbs()  {
        echo "<div class=\"githead\">\n";
        $crumb = "<a href=\"".sanitized_url()."\">projects</a> / ";

        if (isset($_GET['p']))
            $crumb .= "<a href=\"".sanitized_url()."p={$_GET['p']}\">{$_GET['p']}</a> / ";
        
        if (isset($_GET['b']))
            $crumb .= "blob";

        if (isset($_GET['t']))
            $crumb .= "tree";

        if ($_GET['a'] == 'commitdiff')
            $crumb .= 'commitdiff';

        echo $crumb;
        echo "</div>\n";
    }

    function zpr ($arr) {
        print "<pre>" .print_r($arr, true). "</pre>";
    }

    function pretty_code($code) {
        echo "<code class=\"prettyprint\">\n";
        echo $code;
        echo "</code>\n";
    }

    function highlight($code) {

        if (substr($code, 0,2) != '<?')    {
            $code = "<?\n$code\n?>";
            $add_tags = true;
        }
        $code = highlight_string($code,1);

        if ($add_tags)  {
            //$code = substr($code, 0, 26).substr($code, 36, (strlen($code) - 74));
            $code = substr($code, 83, strlen($code) - 140);    
            $code.="</span>";
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
        if(substr($code, 0, 2) != '<?') {
            $code = "<?\n".$code."\n?>";
            $add_tags = true;
        }
        
        $code = highlight_string($code, true);

        // Remove the first "<code>" tag from "$code" (if any)
        if(substr($code, 0, 6) == '<code>') {
           $code = substr($code, 6, (strlen($code) - 13));
        }

        // Replacement-map to replace deprecated "<font>" tag with "<span>"
        $xhtml_convmap = array(
           '<font' => '<span',
           '</font>' => '</span>',
           'color="' => 'style="color:',
           '<br />' => '<br/>',
           '#000000">' => '#'.COLOR_DEFAULT.'">',
           '#0000BB">' => '#'.COLOR_FUNCTION.'">',
           '#007700">' => '#'.COLOR_KEYWORD.'">',
           '#FF8000">' => '#'.COLOR_COMMENT.'">',
           '#DD0000">' => '#'.COLOR_STRING.'">'
        );

        // Replace "<font>" tags with "<span>" tags, to generate a valid XHTML code
        $code = strtr($code, $xhtml_convmap);

        //strip default color (black) tags
        $code = substr($code, 25, (strlen($code) -33));

        //strip the PHP tags if they were added by the script
        if($add_tags) {
            
            $code = substr($code, 0, 26).substr($code, 36, (strlen($code) - 74));
        }

        return $code;
    }

    function html_style()   {
        global $git_css;
        
        if (file_exists("style.css"))
            echo "<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\" />\n";
        if (!$git_css)
            return;

        else    {
        echo "<style type=\"text/css\">\n";
        echo <<< EOF
            #gitbody    {
                margin: 10px 10px 10px 10px;
                border-style: solid;
                border-width: 1px;
                border-color: gray;
                font-family: sans-serif;
                font-size: 12px;
            }

            div.githead    {
                margin: 0px 0px 0px 0px;
                padding: 10px 10px 10px 10px;
                background-color: #d9d8d1;
                font-weight: bold;
                font-size: 18px;
            }

            #gitbody th {
                text-align: left;
                padding: 0px 0px 0px 7px;
            }

            #gitbody td {
                padding: 5px 0px 0px 7px;
            }

            tr:hover { background-color:#edece6; }

            div.gitbrowse a.blob {
                text-decoration: none;
                color: #000000;
            }

            div.gitcode {
                padding: 10px;
            }

            div.gitspacer   {
                padding: 1px 0px 0px 0px;
                background-color: #FFFFFF;
            }

            div.gitfooter {
                padding: 7px 2px 2px 2px;
                background-color: #d9d8d1;
                text-align: right;
            }

            div.gittitle   {
                padding: 7px 7px 7px 7px;
                background-color: #d9d8d1;
                font-weight: bold;
            }

            div.gitbrowse a.blob:hover {
                text-decoration: underline;
            }
            a.gitbrowse:hover { text-decoration:underline; color:#880000; }
            a.rss_logo {
                float:left; padding:3px 0px; width:35px; line-height:10px;
                    margin: 2px 5px 5px 5px;
                    border:1px solid; border-color:#fcc7a5 #7d3302 #3e1a01 #ff954e;
                    color:#ffffff; background-color:#ff6600;
                    font-weight:bold; font-family:sans-serif; font-size:10px;
                    text-align:center; text-decoration:none;
                }
            a.rss_logo:hover { background-color:#ee5500; }
EOF;

        echo "</style>\n";
        }
    }
?>
