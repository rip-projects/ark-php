<div class="bg_title">
    <div class="container_header_footer">
        <h1 title="<?php echo $post['title'] ?>"><?php echo word_limiter($post['title'], 10) ?></h1>
        <h3>Posted at <?php echo $post['updated_time'] ?> <!-- <span style="color:#FFF">39 Comments</span--> </h3>
    </div> 
</div>

<div class="content">
    <?php echo $post['body'] ?>
</div>