<div class="bg_title">
    <div class="container_header_footer">
        <h1 style="text-transform: capitalize"><?php echo word_limiter($tag, 10) ?></h1>
    </div> 
</div>

<div class="content">
    <?php if (empty($posts)): ?>
        <div style="text-align: center; margin: 0;"><?php echo l('No record available') ?></div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><a href="<?php echo site_url('p/'.$post['post_name']) ?>"><?php echo $post['title'] ?></a></h3>
                <div class="date">
                    <p>
                        <span><?php echo mysql_human_date($post['updated_time']) ?></span>
                        <span style="margin-left:35px;"><?php echo mysql_human_time($post['updated_time']) ?></span>
                    </p>
                </div>
                <?php echo close_tags(word_limiter($post['body'])) ?>
                <a class="margin_left_15" style="font-weight:bold" href="<?php echo site_url('p/' . $post['post_name']) ?>">read more</a>
            </div>
        <?php endforeach ?>
    <?php endif ?>
</div>
