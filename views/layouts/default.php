<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>
            <?php $title = System::get('title'); ?>
            <?php if ($title) : ?>
                <?php echo $title; ?>
            <?php else : ?>
                <?php echo $this->_request->params['controller'] . '/' . $this->_request->params['action'] ?>
            <?php endif; ?>
        </title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta NAME="ROBOTS" CONTENT="NOFOLLOW" />
        <link rel="stylesheet" href="/css/gitstyle.css" type="text/css" />
    </head>
    <body>
        <div id="gitbody">
            <?php echo $this->element('breadcrumbs'); ?>
            <?php echo $this->content(); ?>
            <?php echo $this->element('title', array('title' => 'Help')); ?>
            <?php echo $this->element('help'); ?>
            <div class="gitfooter">
            <a href="http://www.kernel.org/pub/software/scm/git/docs/"><img src="/img/git_logo.png" style="border-width: 0px;"></a>
            </div>
        </div>
    </body>
</html>