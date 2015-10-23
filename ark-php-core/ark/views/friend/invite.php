<form method="post" action="<?php echo current_url() ?>">
    <table class="grid table table-hover table-striped table-condensed">
        <?php foreach ($emails as $email): ?>
            <tr>
                <td><input type="checkbox" name="emails[]" value="<?php echo $email['email'] ?>" /></td>
                <td>
                    <?php echo $email['name'] ?>
                </td>
                <td><?php echo $email['email'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <input type="submit" name="do" value="invite" />
</form>