Archetype PHP Greeting
<html>
    <body>
        <p>Welcome <?php echo $data['first_name'] ?> <?php echo $data['last_name'] ?>,</p>
        <p>You're successfully registered as our member. You can access your account here <?php echo base_url() ?></p>
        <p>Sign in as : <?php $data['username'] ?></p>
        <p>Don't forget to track news and development from us on blog.</p>
        <p>Thank you</p>
        <p><strong>The Xinix Team</strong></p>
        <p>Website ini dibangun oleh <a href="http://xinix.co.id">http://xinix.co.id</a>
    </body>
</html>
