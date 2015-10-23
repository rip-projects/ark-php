<fieldset>
    <legend>Message Detail</legend>
    <div>
        <label>Sender</label>
        <span><?php echo $data['sender'] ?></span>
    </div>
    <div>
        <label>Subject</label>
        <span><?php echo $data['subject'] ?></span>
    </div>
    <div>
        <label>Body</label>
        <span><?php echo $data['body'] ?></span>
    </div>
</fieldset>

<div class="action-buttons btn-group">
    <a href="javascript:history.back()" class="btn cancel">Back</a>
</div>