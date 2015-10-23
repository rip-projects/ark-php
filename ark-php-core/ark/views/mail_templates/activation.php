Your activation code
<html>
    <body>
        
        <a href="<?php echo site_url('user/activation').'?act='.$activation ?>">Aktivasi here</a>
        
        This is your activation code 
        <a href="<?php echo site_url('user/activation') ?>"><?php echo site_url('user/activation') ?></a>
        
        <?php echo $activation ?>
    </body>
</html>