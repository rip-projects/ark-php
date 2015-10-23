<h1>Activate User</h1>

<p>Insert activation code</p>
<form method="get" action="<?php echo current_url() ?>">
    <input type="text" name="act" value="" />
    <input type="submit" />
</form>

<a href="<?php echo site_url('user/generate_activation_code') ?>" class="btn">Generate New Activation Code</a>