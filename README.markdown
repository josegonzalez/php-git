# Summary
This is only a rough unsorted collection of informations. The original git-php had no README at all :-(

### Requirements
* git
* enscript


### Installation
1.  edit config.php
2.  In a terminal window, change directories to your $repo_directory and execute the following:
    *  ```mkdir .cache .bundles .secrets ; chmod -R 777 .cache .bundles .secrets```

### Supposed directory structure:
	$repo_directory-\
			|
			+-$cache_name
			|
			+-$secret_name
			|
			+-$bundle_name
			|
			+-project1 / .git
			|
			+-project2 / .git
			|
			...


### Notes
#### Code/Variables:
(just for understanding code; doing cleanup)

```$repos``` = all found $repo_path (security.php)

```$proj``` = name of a Project (=its directories name)
		with no leading or trailing slash !!

```$repopath``` = $repo_directory . $proj . "/"

```is_dir ( $repo_path . "/.git" ) == true```

#### URL parameters:
	p: $proj
	tr: any branch or tag or HEAD in a git-repo.