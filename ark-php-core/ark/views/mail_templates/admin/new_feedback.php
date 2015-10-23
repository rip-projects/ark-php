[GoitFEEDBACK] <?php echo $data['subject'] ?>.
<html>
    <body>
        <p>Hai Admin, ada feedback baru, harap diperhatikan.</p>
        <div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0; padding: 10px;">
            <div><label>Nama : </label> <?php echo $data['name'] ?></div>
            <div><label>Judul :</label> <?php echo $data['subject'] ?></div>
            <div><label>Jenis :</label> <?php echo $data['feedback_type_mail'] ?></div>
            <div><label>Pesan :</label> <?php echo nl2br($data['message']) ?></div>
        </div>
        <p>Harap direspons segera.</p>
    </body>    
</html>