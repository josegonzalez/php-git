<div class="table-wrapper">
    <table class="listing" cellpadding="0" cellspacing="0">
        <tr>
            <th>file</th>
            <th>date</th>
            <th>message</th>
        </tr>
        <?php foreach ($tree as $blob) : ?>
            <tr>
                <td>
                    <?php if ($blob['type'] == 'tree') : ?>
                        <img src="/img/16px/folder.png" />
                    <?php else : ?>
                        <?php echo $this->File->image($blob['file']); ?>
                    <?php endif; ?>
                    <?php echo $this->File->link($blob, $request); ?>
                </td>
                <td>
                    <?php echo $this->Commit->date($blob['date']); ?>
                </td>
                <td>
                    <?php echo $blob['message']; ?> [<?php echo $blob['author']; ?>]
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>