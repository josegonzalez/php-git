<div class="githead">
    <?php foreach ($breadcrumbs as $breadcrumb => $url) : ?>
        <?php if (!$url) : ?>
            <?php echo $breadcrumb; ?> /
            <?php continue; ?>
        <?php endif; ?>
        <a href="<?php echo $url; ?>"><?php echo $breadcrumb; ?></a> /
    <?php endforeach; ?>
</div>