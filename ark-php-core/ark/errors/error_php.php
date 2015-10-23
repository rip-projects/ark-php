<?php if (function_exists('theme_url') && $GLOBALS['CFG']->item('theme')): ?>
  <link href="<?php echo theme_url('css/error.css') ?>" media="all" rel="stylesheet" type="text/css" />
<?php else: ?>
<style type="text/css">
/****************** error *******************************/


.error-container ::selection{ background-color: #E13300; color: white; }
.error-container ::moz-selection{ background-color: #E13300; color: white; }
.error-container ::webkit-selection{ background-color: #E13300; color: white; }

.error-container {
    margin: 10px;
    border: 1px solid #D0D0D0;
    -webkit-box-shadow: 0 0 8px #D0D0D0;
}

.error-container a {
    color: #003399;
    background-color: transparent;
    font-weight: normal;
}

.error-container h1 {
    color: #444;
    background-color: transparent;
    border-bottom: 1px solid #D0D0D0;
    font-size: 19px;
    font-weight: normal;
    margin: 0 0 14px 0;
    padding: 14px 15px 10px 15px;
}

.error-container code {
    font-family: Consolas, Monaco, Courier New, Courier, monospace;
    font-size: 12px;
    background-color: #f9f9f9;
    border: 1px solid #D0D0D0;
    color: #002166;
    display: block;
    margin: 14px 0 14px 0;
    padding: 12px 10px 12px 10px;
}

.error-container p {
    margin: 12px 15px 12px 15px;
}
</style>
<?php endif ?>
<div class="error-container">
    <h1>A PHP Error was encountered</h1>
    <p>Severity: <?php echo (isset($exception)) ? get_class($exception) : $severity; ?></p>
    <p>Message:  <?php echo (isset($exception)) ? $exception->getMessage() : $message; ?></p>
    <p>Filename: <?php echo (isset($exception)) ? $exception->getFile() : $filepath; ?></p>
    <p>Line Number: <?php echo (isset($exception)) ? $exception->getLine() : $line; ?></p>
    <?php if (isset($exception)): ?>
    <pre><code><?php echo $exception->getTraceAsString() ?></code></pre>
    <?php endif ?>
</div>