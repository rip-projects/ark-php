----------------------------------

Error PHP

Title:
A PHP Error was encountered
Severity:
<?php echo (isset($exception)) ? get_class($exception) : $severity ?>


Message:
<?php echo (isset($exception)) ? $exception->getMessage() : $message ?>


Filename:
<?php echo (isset($exception)) ? $exception->getFile() : $filepath ?>


Line Number:
<?php echo (isset($exception)) ? $exception->getLine() : $line ?>


<?php if (isset($exception)): ?>
<?php echo $exception->getTraceAsString() ?>
<?php endif ?>

----------------------------------
