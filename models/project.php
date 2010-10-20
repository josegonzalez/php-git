<?php
class Project {

    var $_config;

    function __construct($config) {
        $this->_config = $config;
    }

    function findAll() {
        list($repositories, $valid) = Git::loadRepositories($this->_config);

        $repos = array();
        foreach ($repositories as $repository) {
            $repo = array(
                'link'          => $this->link($repository),
                'description'   => file_get_contents("{$repository}{$this->_config['repo_suffix']}description"),
                'owner'         => $this->fileOwner($repository),
                'last_change'   => $this->lastChange($repository),
                'download'      => $this->link($repository, array('download' => true)),
            );
            $stats = $this->getStats($repository, 0, 0);
            $repo['today'] = $stats['today'];
            $repo['total'] = $stats['total'];
            $repos[] = $repo;
        }
        return $repos;
    }

    function link($repo, $options = array()) {
        $options = array_merge(array(
            'download' => false,
            'tag' => 'HEAD',
        ), $options);

        $path = basename($repo);
        if ($options['download']) {
            return sprintf('<a href="/%s/downloads/%s">snapshot</a>', $path, $options['tag']);
        }
        return sprintf('<a href="/%s">%s</a>', $path, $path);
    }

    function fileOwner($repo) {
        $out = array();
        $cmd = "GIT_DIR=" . escapeshellarg($repo . $this->_config['repo_suffix']) . " {$this->_config['git_binary']} rev-list  --header --max-count=1 HEAD 2>&1 | grep -a committer | cut -d' ' -f2-3";
        $own = exec($cmd, &$out);
        return $own;
    }

    function lastChange($repo) {
        $out = array();
        $cmd = "GIT_DIR=" . escapeshellarg($repo . $this->_config['repo_suffix']) . " {$this->_config['git_binary']} rev-list  --header --max-count=1 HEAD 2>&1 | grep -a committer | cut -d' ' -f5-6";
        $date = exec($cmd, &$out);
        return date($this->_config['git_date_format'], (int) $date);
    }

    function repoPath($proj) {
        return Git::repoPath($proj);
    }

    function getTags($proj) {
        return Git::parse($this->_config, $proj, 'tags');
    }

    function getBranches($proj) {
        return Git::parse($this->_config, $proj, 'branches');
    }

    function getStats($repository, $inc = false, $fbasename = 'counters') {
        return Git::stats($repository, $inc, $fbasename);
    }

    function getOwner($proj) {
        $path = Git::repoPath($proj);
        return self::fileOwner($path);
    }

    function getLastChange($proj) {
        $path = Git::repoPath($proj);
        return self::lastChange($path);
    }

    function getShortlog($proj) {
        return Git::shortlogs($this->_config, $proj);
    }

    function getDiff($proj, $commit) {
        return Git::diff($this->_config, $proj, $commit);
    }

    function getTree($proj, $filepath = 'HEAD') {
        if ($filepath != 'HEAD') $filepath = "HEAD:{$filepath}";
        return Git::lsTree($this->_config, $proj, $filepath);
    }

    function getCommit($proj, $commit) {
        $commit = Git::commit($this->_config, $proj, $commit);
        if (is_array($commit) && count($commit) == 1) return current($commit);
        return $commit;
    }

    function getDescription($proj) {
        $path = Git::repoPath($proj);
        return file_get_contents("{$path}{$this->_config['repo_suffix']}/description");
    }

}