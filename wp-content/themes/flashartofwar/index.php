<?php get_header(); ?>
	<div id="<?php if(is_home() && !is_paged()) echo "left-column-block";?>">
	<?php if(is_home() && !is_paged()): ?>
	<?php include (TEMPLATEPATH . '/featured.php'); ?>
	<?php endif;?>
		
		<div id="<?php if(is_home() && !is_paged()) echo "home-main";?>" class="main">
	
	
	<?php query_posts($query_string . ''); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			
			<p class="post-info"><span class="date"><?php the_time('F jS, Y') ?></span></p>
		<div class="content">
		
			<?php 
				the_content('Read the rest of this entry &raquo;');
			?>

		</div>
		<?php the_tags('<p>Tags: ', ', ', '</p>'); ?>
			
			<p class="postmeta"><?php comments_popup_link('Comments (0)', 'Comments (1)', 
			'Comments (%)', 'comments', 'Comments off'); ?> | Filed in <?php the_category(', ') ?><?php $external_link = get_post_meta(get_the_ID(), 'wpo_sourcepermalink', true);
					if($external_link)
					{
						echo ' | <a href="'.$external_link.'" target="_blank">View source</a>'; 
					}
				?>
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
		<?php if(is_home() && !is_paged()) {?>
		<div id="snippets">
			
			<h2><a href="<?php echo get_category_link( 19 ); ?>"><?php echo get_cat_name(19);?></a></h2>
			<?php
			 $lastposts = get_posts('numberposts=20&category=19');
			 foreach($lastposts as $post) :
			    setup_postdata($post);
			 ?>
			<div class="snippet"> 
				<h4><?php $category = get_the_category();
				echo get_cat_name($category[0]->cat_ID);?> | <span class="date"><?php the_time('M jS, Y') ?></span></h4>
			<a href="<?php the_permalink(); ?>" id="post-<?php the_ID(); ?>">
			 <?php the_content(); ?></a>
			</div>
			 <?php endforeach; ?>
			<p>
			<a href="<?php echo get_category_link( 19 ); ?>">Read More >></a>
			</p>
			</div>
		<?php } ?>
		</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>