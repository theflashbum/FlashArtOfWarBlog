<div id="sidebar">
<h2>Categories</h2>
<ul>
<?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=0'); ?>
</ul>
</ul>
<h2>Archives</h2>
<ul>
 <?php wp_get_archives('type=monthly'); ?>
</ul>
<ul>
<?php get_links_list(); ?>

</div>