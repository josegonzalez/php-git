# Git-PHP

## Summary
Git-PHP is a port of gitweb to PHP. It includes various functionality, such as submitting diffs, repository browsing, simple search, and diff highlighting. It is based upon Zack Bartel and Peeter Vois's original work, with updates to handle the latest git installs (1.6.4.4 as September 25, 2009), as well an update to the stylesheet to make it feel more like github.com. It definitely still needs tender love and care to make it more MVC like, but it'll get there. Please read the commit messages for more information on changes implemented.

## Requirements
* git
* enscript


## Installation
1.  edit config.php
2.  In a terminal window, change directories to your $repo_directory and execute the following:
    *  ``mkdir .temp/cache .temp/bundles .temp/secrets ; chmod -R 777 .cache .bundles .secrets``

### Supposed directory structure:
	$repo_directory-\
			|
			+-.temp/$cache_name
			|
			+-.temp/$secret_name
			|
			+-.temp/$bundle_name
			|
			+-project1 / .git
			|
			+-project2 / .git
			|
			...


### Notes
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