<div class="table-wrapper">
    <table class="listing" cellpadding="0" cellspacing="0">
        <tr>
            <th>file</th>
            <th>perm</th>
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
                    <?php echo $blob['perm']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>