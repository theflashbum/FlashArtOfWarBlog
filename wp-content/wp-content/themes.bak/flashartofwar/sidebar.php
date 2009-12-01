<ul id="sidebar">
<li>
	<h2>Inspiration</h2>
	<? yarq_display(); ?>
</li>
<?php if ( !function_exists('dynamic_sidebar')
        || !dynamic_sidebar() ) : ?>
 
<?php endif; ?>

</ul>
</div>