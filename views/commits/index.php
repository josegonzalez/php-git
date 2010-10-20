<div class="gitspacer">&nbsp;</div>
    <?php echo $this->element('summary_title'); ?>
<div class="gitspacer">&nbsp;</div>
<?php echo $this->element('description', compact('description', 'owner', 'last_change')); ?>
<div class="table-wrapper">
    <table class="commitlog" cellpadding="0" cellspacing="0">
        <?php $i = 0; foreach ($shortlogs as $log) : ?>
            <tr<?php if ($i++%2==0) echo ' class="altrow"'?>>
                <td><?php echo $this->Gravatar->image($log['email']); ?></td>
                <td>
                    <?php echo $log['subject']; ?><br />
                    <?php echo $log['author']; ?> (author)<br />
                    <?php echo date('M j, Y', $log['timestamp']); ?>
                </td>
                <td class="actions">
                    <?php echo $this->Commit->link($log['hash'], $this->_request); ?><br />
                    <?php echo $this->Commit->tree($log['tree'], $this->_request); ?><br />
                    <?php echo $this->Commit->parents($log['parents'], $this->_request); ?>
                    <?php echo $this->Commit->download($log['hash'], $this->_request); ?><br />
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>