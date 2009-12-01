<?php get_header(); ?>

		<div id="main">
			
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
			include('wp-pagenavi.php');
			if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
			?>

	<?php else : ?>

		<h2>Not Found</h2>
		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>

		<!-- main ends -->	
		</div>
		
<?php get_sidebar(); ?>
<?php get_footer(); ?>