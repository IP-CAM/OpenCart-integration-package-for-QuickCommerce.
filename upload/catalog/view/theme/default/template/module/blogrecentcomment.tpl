<div class="bt-box block boss_block-recent-comments">
    <div class="box-heading block-title">
        <span><?php echo $heading_title; ?></span>
    </div>
    <div class="box-content block-content">
        <?php if($articles){?>
        <ol>
            <?php foreach ($articles as $article) { ?>
            <li class="item recent-comment-item">
                <div class=""><a class="article-title" href="<?php echo $article['href']; ?>"><?php echo $article['name']; ?></a></div>
				<small class="time-stamp">
					<?php $date = new DateTime($article['date_added']);?>
					<?php echo $date->format('M d, Y');?></small>
				<span>&nbsp;&nbsp; / &nbsp;&nbsp;</span>	
				<span class="comment-by"><?php echo $text_comment; ?>&nbsp;<span><?php echo $article['author']; ?></span></span>  
				<span>&nbsp; / &nbsp;</span>
				<span class="comment-count"><span><?php echo $article['number']; ?> </span><a href="<?php echo $article['href']; ?>"><?php echo $number_comments;?></a></span> 
                <div class="recent-comment-content">
                    <?php echo $article['comment']; ?>
                </div> 
				<div class="recent-comment-name">
                    <?php echo $article['comment_name']; ?>
                </div> 
            </li>
            <?php } ?>
        </ol>
        <?php } else {?>
        <?php echo 'There are no comments.'; ?>
        <?php } ?>
    </div>

</div>
