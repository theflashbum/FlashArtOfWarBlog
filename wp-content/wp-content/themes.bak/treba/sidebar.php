<div id="sidebar">
<h2>Pages</h2>
<ul>
<li><a href="<?php echo get_settings('home'); ?>/">Home</a></li>
<?php wp_list_pages('title_li='); ?> 
</ul>
<h2>Categories</h2>
<ul>
<?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=0'); ?>
</ul>
<h2>Archives</h2>
<ul>
 <?php wp_get_archives('type=monthly'); ?>
</ul>
<?php if (function_exists('wp_theme_switcher')){echo '<h2>Themes</h2>'; wp_theme_switcher();} ?>
<ul>
<?php get_links_list(); ?>
</ul>
</div>