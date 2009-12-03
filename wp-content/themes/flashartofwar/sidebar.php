		<!-- sidebar starts -->
		<div id="<?php if(is_home() && !is_paged()) echo "home-sidebar";?>" class="sidebar">

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
		
			<h3>Categories</h3>
			<ul>				
<?php wp_list_categories('title_li='); ?>
			</ul>

			<h3>Archives</h3>
			<ul>				
<?php wp_get_archives('title_li='); ?>
			</ul>	
				
			<h3>Links</h3>
			<ul>
                   <?php wp_list_bookmarks('categorize=0&title_li='); ?>		
			</ul>
			
			<h3>Meta</h3>
			<ul>
				<?php wp_register(); ?>				<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>

				<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>

					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
					<?php wp_meta(); ?>
			</ul>	
			
			<h3>Search Box</h3>	
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<?php endif; ?>
		<!-- sidebar ends -->
		</div>