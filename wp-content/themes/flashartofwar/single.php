<?php get_header(); ?>
				
		<div id="main">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			
			<p class="post-info">Posted by <?php the_author(); ?> | Filed under <?php the_category(', ') ?></p>
				
		<?php the_content('Read the rest of this entry &raquo;'); ?>

<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

<?php the_tags('<p>Tags: ', ', ', '</p>'); ?>
				
			<p class="postmeta">		
			<a href="<?php the_permalink() ?>" class="readmore">Permalink</a> |
			<span class="date"><?php the_time('F jS, Y') ?></span><?php edit_post_link('Edit', ' | ', ''); ?>		</p>

<?php comments_template(); ?>

<?php endwhile; else : ?>

		<h2>Not Found</h2>
		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>

		<!-- main ends -->	
		</div>
		
<?php get_sidebar(); ?>
<?php get_footer(); ?>