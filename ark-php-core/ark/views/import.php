<form enctype="multipart/form-data" action="<?php echo current_url() ?>" method='post' class="ajaxform">
    <!-- <input type="hidden" name="HTTP_X_REQUESTED_WITH" value="XMLHttpRequest" /> -->
    <fieldset>
        <legend>Import <?php echo strtoupper($CI->uri->rsegments[3]) ?></legend>
         <p>
            <?php echo l('Data CSV must be in form of:') ?>
        </p>
        <code>
            <?php echo implode(', ', $config['import_names']) ?>
        </code>
        <p>Please Download this <a href="<?php echo site_url($CI->_get_uri('import_example')) ?>">example</a></p>
        <div>
            <input type='file' name='userfile' />
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type='submit' value='Import' />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel">Cancel</a>
    </div>
</form>
