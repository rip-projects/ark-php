<meta http-equiv="refresh" content="3;url=<?php echo $_SERVER['HTTP_REFERER'] ?>" />

<fieldset>
    <legend><?php echo l('System') ?></legend>
    <p><?php echo l('Cache cleaned!')  ?></p>
    <p><?php echo  l('This page will be redirected back to referer soon...')?></p>
</fieldset>

<div class="action-buttons btn-group">    
    <a href="<?php echo $_SERVER['HTTP_REFERER'] ?>" class="btn">Back</a>
</div>