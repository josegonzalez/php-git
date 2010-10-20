<div class="gitspacer">&nbsp;</div>
    <?php echo $this->element('summary_title'); ?>
<div class="gitspacer">&nbsp;</div>
<div class="table-wrapper">
    <table class="commitlog" cellpadding="0" cellspacing="0">
        <tr>
            <td><?php echo $this->Gravatar->image($commit['email']); ?></td>
            <td>
                <?php echo $commit['subject']; ?><br />
                <?php echo $commit['author']; ?> (author)<br />
                <?php echo date('M j, Y', $commit['timestamp']); ?>
            </td>
            <td class="actions">
                <?php echo $this->Commit->link($commit['hash'], $this->_request); ?><br />
                <?php echo $this->Commit->tree($commit['tree'], $this->_request); ?><br />
                <?php echo $this->Commit->parents($commit['parents'], $this->_request); ?>
                <?php echo $this->Commit->download($commit['hash'], $this->_request); ?><br />
            </td>
        </tr>
    </table>
</div>
<?php foreach ($diffs as $diff) : ?>
    <div class="diffsummary"><?php echo nl2br($diff['summary']); ?></div>
    <div class="gitcode"><?php echo $this->Geshi->highlight($diff['file']); ?></div>
<?php endforeach; ?>