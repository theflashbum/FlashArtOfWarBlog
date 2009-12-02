<?php get_header(); ?>

		<div id="<?php if (is_home()) echo "home-main";?>" class="main">
	
	
	<?php query_posts($query_string . ''); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			
			<p class="post-info">Posted by <?php the_author(); ?> | Filed under <?php the_category(', ') ?></p>
		<div class="content">		
		<?php the_content('Read the rest of this entry &raquo;'); ?>
		</div>
		<?php the_tags('<p>Tags: ', ', ', '</p>'); ?>
			
			<p class="postmeta">
			<a href="<?php the_permalink() ?>" class="readmore">Read more</a> |
			<?php comments_popup_link('Comments (0)', 'Comments (1)', 
'Comments (%)', 'comments', 'Comments off'); ?> |				
			<span class="date"><?php the_time('F jS, Y') ?></span>	
			</p>

<?php endwhile; ?>
		
			
			<?php

			if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
			?>

	<?php else : ?>

		<h2>Not Found</h2>
		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>

		<!-- main ends -->	
		</div>
		<?php if (is_home()) {?>
		<div id="snippets">
			<h2><?php echo get_cat_name(19);?></h2>
			<?php
			 $lastposts = get_posts('numberposts=20&category=19');
			 foreach($lastposts as $post) :
			    setup_postdata($post);
			 ?>
			<div class="snippet"> 
				<h4><?php $category = get_the_category();
				echo get_cat_name($category[0]->cat_ID);?></h4>
			<a href="<?php the_permalink(); ?>" id="post-<?php the_ID(); ?>">
			 <?php the_content(); ?></a>
			</div>
			 <?php endforeach; ?>
			</div>
		<?php } ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>