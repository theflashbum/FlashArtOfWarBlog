<?php
/*
Template Name: Tag Cloud
*/

?>

<?php get_header(); ?>
<div id="content" class="narrowcolumn">
<!-- ************************* BEGIN CONTENT ******************************* -->

<h3>Tag Cloud</h3>


<?php if (class_exists('SimpleTagging')) : ?> 
	<ul id ="tagcloud">
		<?php STP_Tagcloud(); ?>
	</ul>
<?php endif; ?>




<!-- ************************* END CONTENT ********************************* -->
</div> <!-- [content] -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>

