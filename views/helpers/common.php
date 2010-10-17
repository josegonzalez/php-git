<?php
class CommonHelper {

    function html_summary_title($project, $branches) {
        global $branches, $tags, $nr_of_shortlog_lines;
        if ($_GET['a'] != "commitdiff") {
            echo html_ref(array('project' => $project, 'a' => "jump_to_tag"),"<form method=post action=\"");
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
    

    function html_ref($arguments, $prefix) {
        global $keepurl;

        if(!is_array($keepurl)) {
            $keepurl = array();
        }

        $diff = array_diff_key($keepurl, $arguments);
        $ahref = $prefix . sanitized_url();
        $a = array_keys($diff);
        foreach($a as $d) {
            if($diff[$d] != "") {
                $ahref .= "$d={$diff[$d]}&";
            }
        }
        $a = array_keys($arguments);
        foreach($a as $d) {
            if($arguments[$d] != "") {
                $ahref .= "$d={$arguments[$d]}&";
            }
        }
        $now = floor(time()/15/60); // one hour
        $ahref .= "tm=" . $now;
        $ahref .= "\">";
        return $ahref;
    }

}