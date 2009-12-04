<div class="featured_post">

<?php $my_query = new WP_Query('category_name=featured&showposts=1');
while ($my_query->have_posts()) : $my_query->the_post();
$do_not_duplicate = $post->ID; ?>

<h2>Featured Post: <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

<p class="post-info"><span class="date"><?php the_time('F jS, Y') ?></span></p>
<div class="content">		
	<img class="websnapr-thumb" src="/wp-content/themes/flashartofwar/images/thumbnail.php"/>

<?php the_content('Read the rest of this entry &raquo;'); ?>
</div>
<?php the_tags('<p>Tags: ', ', ', '</p>'); ?>

<p class="postmeta"><?php comments_popup_link('Comments (0)', 'Comments (1)', 
'Comments (%)', 'comments', 'Comments off'); ?> | <?php the_category(', ') ?><?php $external_link = get_post_meta(get_the_ID(), 'wpo_sourcepermalink', true);
		if($external_link)
		{
			echo ' | <a href="'.$external_link.'" target="_blank">View source</a>'; 
		}
	?>
</p>


<?php endwhile; ?>
</div>