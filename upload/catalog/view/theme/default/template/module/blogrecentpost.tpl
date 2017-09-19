<div class="bt-box block boss_block-recent-post">
    <div class="box-heading block-title">
        <span><?php echo $heading_title; ?></span>
    </div>
    <div class="box-content block-content">
        <?php if($articles){?>
        <ol>
            <?php foreach ($articles as $article) { ?>
            <li class="item">
                <div class="time-stamp">
                    <?php $date = new DateTime($article['date_added']);?>
                    <div class="day"><?php echo $date->format('j');?></div>
                    <div class="mth"><?php echo $date->format('M');?></div>
				</div> 
				<div class="right_recent">	
					<a class="article-title" href="<?php echo $article['href']; ?>"><?php echo $article['name']; ?></a>
					<div class="recent-comment-content">
						<?php echo $article['title']; ?>
					</div>
					<span class="post-by"><?php echo $text_postby; ?>&nbsp;<span><?php echo $article['author']; ?></span></span>								
				</div>
            </li>
            <?php } ?>
        </ol>
        <?php } else {?>
        <?php echo 'There are no Articles.'; ?>
        <?php } ?>
    </div>

</div>
