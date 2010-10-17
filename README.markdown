# Git-PHP

## Summary
Git-PHP is a port of gitweb to PHP. It includes various functionality, such as submitting diffs, repository browsing, simple search, and diff highlighting. It is based upon Zack Bartel, Peeter Vois and Jose Diaz-Gonzalez' original work, with updates to handle the latest git installs (1.6.4.4 as September 25, 2009). It definitely still needs tender love and care to make it more MVC like, but it'll get there. Please read the commit messages for more information on changes implemented.

Icons now included from http://www.famfamfam.com/lab/icons/silk/

## Requirements
* git
* GeSHi
* gd2


## Installation
1.  edit config.php
2.  In a terminal window, change directories to your git-php install directory and execute the following:
    *  ``mkdir .temp ; mkdir .temp/cache .temp/bundles .temp/secrets ; cd .temp ; chmod -R 0777 cache bundles secrets``

### Supposed directory structure:
	git-php-\
		|
			+-.temp\
					|
					+-cache
					|
					+-bundles
					|
					+-secrets
			
	$repo_directory-\
			|
			+-project1\.git
			|
			+-project2\.git
			|
			...

### Notes
#### Maintenance:
Should you require a cleanup of the cache, delete everything in the .temp/cache folder.
    *  ``cd .temp/cache ; rm -r *``

#### Code/Variables:
(just for understanding code; doing cleanup)

``$repos`` = all found $repo_path (security.php)

``$proj`` = name of a Project (=its directories name)
		with no leading or trailing slash !!

``$repopath`` = $repo_directory . $proj . "/"

``is_dir ( $repo_path . "/.git" ) == true``

#### URL parameters:
	p: $proj
	tr: any branch or tag or HEAD in a git-repo.
