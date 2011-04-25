<?php
class Git {

    static $repos;

    public static function loadRepositories() {
        if (!System::get('repo_directory')) return array();

        $repoDir = System::get('repo_directory');
        if (!file_exists($repoDir)) return array();
        if (!is_dir($repoDir)) return array();

        $repos = array();
        $valid = array();

        if ($handle = opendir($repoDir)) {
            while (false !== ($file = readdir($handle))) {
                $fullpath = $repoDir . $file;
                if ($file[0] != '.' && is_dir($fullpath)) {
                    $repoSuffix = System::get('repo_suffix');
                    if (is_dir($fullpath . $repoSuffix)) {
                        $headFile = "HEAD";
                        if (substr($repoSuffix, -1) != "/") {
                            $headFile = "/{$headFile}";
                        }
                        if (file_exists($fullpath . $repoSuffix . $headFile)
                        && self::getOwner($fullpath, $repoSuffix) != NULL) {
                            $valid[] = trim($file);
                            $repos[] = trim("{$fullpath}/");
                        }
                    }
                }
            }
            closedir($handle);
        }
        self::$repos = array_combine($valid, $repos);
        sort($repos);

        return array($repos, $valid);
    }

    public static function getOwner($path, $repoSuffix) {
        $out = array();
        $gitBinary = System::get('git_binary');
        $cmd = "GIT_DIR=" . escapeshellarg($path . $repoSuffix) . " {$gitBinary} rev-list  --header --max-count=1 HEAD 2>&1 | grep -a committer | cut -d' ' -f2-3";
        $own = exec($cmd, &$out);
        return $own;
    }

    function parse($proj, $what) {
        $gitBinary = System::get('git_binary');

        $cmd1 = "GIT_DIR=" . self::$repos[$proj] . System::get('repo_suffix') . " {$gitBinary} rev-parse  --symbolic --" . escapeshellarg($what) . "  2>&1";
        $out1 = array();
        $bran = array();
        exec($cmd1, &$out1);
        for($i = 0; $i < count($out1); $i++) {
            $cmd2="GIT_DIR=" . self::$repos[$proj] . System::get('repo_suffix') . " {$gitBinary} rev-list --max-count=1 " . escapeshellarg($out1[$i]) . " 2>&1";
            $out2 = array();
            exec($cmd2, &$out2);
            $bran[$out1[$i]] = $out2[0];
        }
        return $bran;
    }

    public static function stats($repo, $inc = false, $fbasename = 'counters') {
        $rtoday = 0;
        $rtotal = 0;
        $now = floor(time()/24/60/60); // number of days since 1970

        if (!is_dir(CACHE)) {
            mkdir(CACHE);
            chmod(CACHE, 0777);
        }

        $fname = CACHE . basename($repo);

        if (!is_dir($fname)) {
            mkdir($fname);
            chmod($fname, 0777);
        }

        $fname = CACHE . basename($repo) . "/" . $fbasename . "-" . basename($repo, ".git");
        $fd = 0;

        //$fp1 = sem_get(fileinode($fname), 1);
        //sem_acquire($fp1);

        if (file_exists($fname)) {
            $file = fopen($fname, "r+"); // open the counter file
        } else {
            $file = FALSE;
        }
        if ($file != FALSE) {
            fseek($file, 0); // rewind the file to beginning
            // read out the counter value
            fscanf($file, "%d %d %d", $fd, $rtoday, $rtotal);
            if($fd != $now) {
                $rtoday = 0;
                $fd = $now;
            }
            if ($inc) {
                $rtoday++;
                $rtotal++;
            }
            fclose($file);
        }
        // uncomment the next lines to erase the counters
        //$rtoday = 0;
        //$rtotal = 0;
        $file = fopen($fname, "w+"); // open or create the counter file
        // write the counter value
        fseek($file, 0); // rewind the file to beginning
        fwrite($file, "$fd $rtoday $rtotal\n");
        fclose($file);
        chmod($fname, 0666);
        return array('today' => $rtoday, 'total' => $rtotal);
    }

    public static function repoPath($proj) {
        foreach (self::$repos as $repo) {
            $path = basename($repo);
            if ($path == $proj) {
                return $repo;
            }
        }
        return false;
    }

    public static function shortlogs($proj) {
        return self::getLastNCommits($proj);
    }

    public static function commit($proj, $commit) {
        $options = array(
            'since' => $commit,
            'count' => 1
        );
        return self::getLastNCommits($proj, $options);
    }

    public static function blob($proj, $path) {
        if (is_file(self::$repos[$proj] . DS . $path)) {
            $File = new File(self::$repos[$proj] . DS .$path);
            return array(
                'content' => $File->read(),
                'ext' => $File->ext()
            );
        }
        return null;
    }

    public static function lsTree($proj, $tree) {
        $gitBinary = System::get('git_binary');

        $lsTreePath = ($tree != '') ? "HEAD:{$tree}" : 'HEAD';

        $out = array();
        //Have to strip the \t between hash and file
        $cmd = sprintf("GIT_DIR=%s%s %s ls-tree %s 2>&1 | sed -e 's/\t/ /g'",
            self::$repos[$proj],
            System::get('repo_suffix'),
            $gitBinary,
            ($tree != '') ? "HEAD:{$tree}" : 'HEAD'
        );
        exec($cmd, &$out);

        $results = array();
        foreach ($out as $line) {
            $result = array_combine(
                array('perm', 'type', 'hash', 'file'),
                explode(" ", $line, 4)
            );
            if (empty($tree)) {
                $path = $result['file'];
            } elseif (substr($tree, -1) == DS) {
                $path = $tree . $result['file'];
            } else {
                $path = $tree . DS . $result['file'];
            }
            $cmd = sprintf("GIT_DIR=%s%s %s log --pretty=format:%%H%%x00%%an%%x00%%ai%%x00%%s -1 -- %s",
                self::$repos[$proj],
                System::get('repo_suffix'),
                $gitBinary,
                $path
            );
            exec($cmd, $info);
            if (empty($info)) {
                $results[] = $result;
                continue;
            }

            list($revision, $author, $date, $message) = explode(chr(0), $info[0]);
            $results[] = array_merge($result, compact('revision', 'author', 'date', 'message'));
            unset($info);
        }
        return $results;
    }

    public static function diff($proj, $commit) {
        $gitBinary = System::get('git_binary');

        $out = array();
        $cmd = "GIT_DIR=" . self::$repos[$proj] . System::get('repo_suffix') . " {$gitBinary} show {$commit} --format=\"%b\" 2>&1";
        exec($cmd, &$out);

        $diff = false;
        $summary = array();
        $file = array();
        $results = array();
        foreach ($out as $line) {
            if (empty($line)) continue;
            if ($diff) {
                if (substr($line, 0, 4) === 'diff') {
                    $results[] = array(
                        'file' => implode("\n", $file),
                        'summary' => implode("\n", $summary),
                    );
                    $file       = array();
                    $summary    = array();
                    $summary[]  = $line;
                    $diff       = false;
                } else {
                    $file[]     = $line;
                }
            } else {
                if (substr($line, 0, 3) === '@@ ') {
                    $diff       = true;
                    $file[]     = $line;
                } else {
                    $summary[]  = $line;
                }
            }
        }
        $results[] = array(
            'file' => implode("\n", $file),
            'summary' => implode("\n", $summary),
        );
        return $results;
    }

    private static function getLastNCommits($proj, $options = array()) {
        $gitBinary = System::get('git_binary');
        $options = array_merge(array(
            'since' => 'HEAD',
            'until' => 'HEAD',
            'count' => 10,
            'dry'   => false,
            'params' => array()
        ), $options);

        if ($options['count'] == 1) {
            $query = $options['since'];
        } else {
            $query = implode('..', array($options['since'], $options['until']));
            if (in_array($query, array('..', 'HEAD..HEAD'))) $query = '--all';
        }

        // --full-history --topo-order --skip=0

        $params     = array();
        $params[]   = "max-count={$options['count']}";
        foreach ($options['params'] as $param) {
            $params[]= $param;
        }
        $params     = implode(' --', $params);
        if (!empty($params)) $params = "--{$params}";

        $format     = array();
        $format[]   = 'parents %P';
        $format[]   = 'tree %T';
        $format[]   = 'author %aN';
        $format[]   = 'email %aE';
        $format[]   = 'timestamp %at';
        $format[]   = 'subject %s';
        $format[]   = 'endrecord%n';
        $format     = implode('%n', $format);
        $cmd = "GIT_DIR=" . self::$repos[$proj] . System::get('repo_suffix') . " {$gitBinary} rev-list {$query} {$params} --pretty=format:\"{$format}\"";
        if ($options['dry']) return $cmd;
        $out = array();
        exec($cmd, &$out);

        $commit = array();
        $results = array();
        foreach ($out as $line) {
            $line = trim($line);
            if (empty($line)) {
                $results[] = array_merge(array(
                    'parents' => array()
                ), $commit);
                $commit = array();
                continue;
            }
            if ($line == 'endrecord') {
                // Commit exists, we can generate extra data here
                continue;
            }

            $descriptor = strstr($line, ' ', true);
            $info = trim(strstr($line, ' '));
            if ($descriptor == 'commit') {
                $commit['hash'] = $info;
            } else if ($descriptor == 'parents') {
                $commit['parents'] = explode(' ', $info);
            } else if ($descriptor == 'tree') {
                $commit['tree'] = $info;
            } else if ($descriptor == 'author') {
                $commit['author'] = $info;
            } else if ($descriptor == 'email') {
                $commit['email'] = $info;
            } else if ($descriptor == 'timestamp') {
                $commit['timestamp'] = $info;
            } else if ($descriptor == 'subject') {
                $commit['subject'] = $info;
            }
        }
        return $results;
    }

}