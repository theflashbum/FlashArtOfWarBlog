<?php
/*
Template Name: Simple Tagging Search Results
*/
?>
<?php get_header(); ?>

	<div id="content" class="narrowcolumn">
		<h2 class="pagetitle">All tag results for &#8216;<?php STP_CurrentTagSet(); ?>&#8217;</h2>


	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time('F jS, Y') ?> <!-- by <?php the_author() ?> --></small>

				<div class="entry">
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

				<p class="postmetadata">Tags: <?php STP_PostTags(); ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

	<div id="sidebar">
		<ul>
			<li id="relatedtags"><h2>Related tags</h2>
			<ul>
			<?php STP_RelatedTags() ?>
			</ul>
			
			<ul style="margin-top:1em;">
			<?php STP_RelatedTagsRemoveTags() ?>
			</ul>
			</li>
		</ul>
	</div>

<?php // get_sidebar(); ?>

<?php get_footer(); ?>
