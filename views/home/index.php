<div class="table-wrapper">
    <table class="listing" cellpadding="0" cellspacing="0">
        <tr>
            <th>Project</th>
            <th>Description</th>
            <th>Owner</th>
            <th>Last Changed</th>
            <th>Download</th>
            <th>Hits</th>
        </tr>
        <?php foreach ($repos as $repo) : ?>
            <tr>
                <td>
                    <?php echo $repo['link']; ?>
                </td>
                <td>
                    <?php echo $this->Text->truncate($repo['description']); ?>
                </td>
                <td>
                    <?php echo $repo['owner']; ?>
                </td>
                <td>
                    <?php echo $repo['last_change']; ?>
                </td>
                <td>
                    <?php echo $repo['download']; ?>
                </td>
                <td>
                    (<?php echo $repo['today']; ?> / <?php echo $repo['total']; ?>)
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>