<div class="gitspacer">&nbsp;</div>
    <?php echo $this->element('summary_title'); ?>
<div class="gitspacer">&nbsp;</div>
<div class="table-wrapper">
    <div class="gitcode"><?php echo $this->Geshi->highlight($data['content'], $data['ext']); ?></div>
</div>
