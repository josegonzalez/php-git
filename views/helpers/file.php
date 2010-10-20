<?php
class FileHelper {

    var $extensions  = array(
        'aac', 'ai', 'aiff', 'avi',
        'bmp', 'c', 'cpp', 'css',
        'dat', 'dmg', 'doc', 'dotx', 'dwg', 'dxf',
        'eps', 'exe', 'flv', 'gif',
        'h', 'hpp', 'html', 'ics', 'iso',
        'java', 'jpg', 'key',
        'mid', 'mp3', 'mp4', 'mpg',
        'odf', 'ods', 'odt', 'otp', 'ots', 'ott',
        'pdf', 'php', 'png', 'ppt', 'psd', 'py',
        'qt', 'rar', 'rb', 'rtf', 'sql',
        'tga', 'tgz', 'tiff', 'txt',
        'wav', 'xls', 'xlsx', 'xml', 'yml', 'zip'
    );

    function image($filename) {
        $ending = strrchr($filename, '.');
        if ($ending === false) return '<img src="/img/32px/_blank.png" width="16" height="16" />';

        $ending = substr($ending, 1);

        if (in_array($ending, $this->extensions)) {
            return sprintf('<img src="/img/32px/%s.png" width="16" height="16" />', $ending);
        }
        return '<img src="/img/32px/_page.png" width="16" height="16" />';
    }

    function link($blob, $request) {
        $path  = (isset($request->params['filepath'])) ? $request->params['filepath'] . '/' : '';
        $path .= $blob['file'];
        if ($blob['type'] == 'tree') {
            return sprintf('<a href="/%s/tree/%s/%s">%s</a>',
                $request->params['project'],
                $request->params['branch'],
                $path,
                $blob['file']
            );
        }
        else if ($blob['type'] == 'blob') {
            return sprintf('<a href="/%s/blob/%s/%s">%s</a>',
                $request->params['project'],
                $request->params['branch'],
                $path,
                $blob['file']
            );
        }
        else if ($blob['type'] == 'file') {
            return sprintf('<a href="/%s/file/%s/%s">%s</a>',
                $request->params['project'],
                $request->params['branch'],
                $path,
                $blob['file']
            );
        }
    }

}