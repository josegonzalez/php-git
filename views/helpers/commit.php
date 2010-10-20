<?php
class CommitHelper {

    function link($hash, $request, $type = 'Commit') {
        return sprintf("<span>%s</span><a href=\"/%s/commit/%s\">%s</a>",
            $type,
            $request->params['project'],
            $hash,
            $hash
        );
    }

    function tree($tree, $request) {
        return sprintf("<span>Tree</span><a href=\"/%s/tree/%s\">%s</a>",
            $request->params['project'],
            $tree,
            $tree
        );
    }

    function parents($parents, $request) {
        $output = '';
        foreach ($parents as $parent) {
            $output .= $this->link($parent, $request, 'Parent') . "<br />";
        }
        return $output;
    }

    function download($hash, $request) {
        return sprintf("<span>Snapshot</span><a href=\"/%s/downloads/%s\">link</a>",
            $request->params['project'],
            $hash
        );
    }

}