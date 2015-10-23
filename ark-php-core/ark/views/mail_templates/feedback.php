[Goit-Feedback] <?php echo $data['subject'] ?>.
<html>
    <body>
        <p>Hi <?php echo $data['name'] ?>, you have sent the following feedback to Goitgoit.</p>
        <div style="border:1px solid #brown;padding-left:20px;margin:0 0 10px 0; padding: 10px;">
            <div><label>Nama : </label> <?php echo $data['name'] ?></div>
            <div><label>Judul :</label> <?php echo $data['subject'] ?></div>
            <div><label>Jenis :</label> <?php echo $data['feedback_type_mail'] ?></div>
            <div><label>Pesan :</label> <?php echo nl2br($data['message']) ?></div>
        </div>
        <p>Thank you for your feedback, we will process it as soon as possible. :)</p>
        <p><strong>The Goitgoit Team</strong></p>
    </body>    
</html>