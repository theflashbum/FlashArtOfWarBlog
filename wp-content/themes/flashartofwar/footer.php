	<!-- content-wrap ends-->	
	</div>
		
	<!-- footer starts here -->	
	<div id="footer-wrap"><div id="footer-content">
	
		<div class="col float-left space-sep">
			
			<h3>Most Popular</h3>
			<ul class="col-list">
			<?php $result = $wpdb->get_results("SELECT comment_count,ID,post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , 10");
			foreach ($result as $post) {
			setup_postdata($post);
			$postid = $post->ID;
			$title = $post->post_title;
			$commentcount = $post->comment_count;
			if ($commentcount != 0) { ?>
				<li><a href="<?php echo get_permalink($postid); ?>" title="< ?php echo $title ?>"><?php echo $title ?></a></li>
			<?php } } ?></ul>	
				
				
		</div>
		
		<div class="col float-left">
		
			<h3>Recent Comments</h3>
			
	
			<ul class="col-list">				
				<?php mdv_recent_comments(5); ?>				
			</ul>
				
		</div>		
	
		<div class="col2 float-right">
		
			<h3>About</h3>			
			
			<p>
			<img id="websnapr-thumb" src="<?php bloginfo('stylesheet_directory'); ?>/images/about-icon.gif" width="40" height="40" alt="firefox" class="float-left" />
			Welcome to The Flash Art Of War, my blog on Flash development. My name is <strong>Jesse Freeman</strong> and I have been an interactive designer/developer for over 10 years. I am the <strong>Lead Flash Developer</strong> at <a href="http://radicalmedia.com">Radical Media</a> and I have worked for <a href="http://www.mlb.com">MLB.com</a>, the <a href="http://www.newyorkjets.com">New York Jets</a>, <a href="http://www.interviewwithari.com">HBO</a>, Arista Records, Fox and <a href="http://www.bfreedesign.com">many more</a>.</p>

			<p>The goal of this site is to train you to do battle as I have! Everything on this site is free to use, I only ask that you drop me an email letting me know how it works out, or leave a comment.</p>

			
			<p>
			&copy; Copyright <?php echo date('Y');?> <strong>Jesse Freeman</strong><br /> 
			<?php bloginfo('name'); ?> was created by <a href="http://jessefreeman.com" target="_blank">Jesse Freeman</a> (AKA the <a href="http://flashbum.com" target="_blank">FlashBum</a>).
			</p>
			<p>
				<a href="http://www.linkedin.com/in/jessefreeman" ><img src="http://www.linkedin.com/img/webpromo/btn_liprofile_blue_80x15.gif" width="80" height="15" border="0" alt="View Jesse Freeman's profile on LinkedIn"></a>
				</p>
		
			<p>
				<!--Creative Commons License--><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/2.5/"><img alt="Creative Commons License" border="0" src="http://creativecommons.org/images/public/somerights20.png"/></a><br/><br/>This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/2.5/">Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License</a>.<!--/Creative Commons License--><!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
						<Work rdf:about="">
							<license rdf:resource="http://creativecommons.org/licenses/by-nc-sa/2.5/" />
					<dc:type rdf:resource="http://purl.org/dc/dcmitype/Text" />
						</Work>
						<License rdf:about="http://creativecommons.org/licenses/by-nc-sa/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><prohibits rdf:resource="http://web.resource.org/cc/CommercialUse"/><permits rdf:resource="http://web.resource.org/cc/DerivativeWorks"/><requires rdf:resource="http://web.resource.org/cc/ShareAlike"/></License></rdf:RDF> -->
				</p>
					<p>						
					Powered by <a href="http://wordpress.org">WordPress</a> | Valid <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> | 
				   	   <a href="http://validator.w3.org/check/referer">XHTML</a>							
					</p>
		</div>		
			
	</div></div>
	<div class="clearer"></div>
	<!-- footer ends here -->

<!-- wrap ends here -->
</div>
<?php wp_footer(); ?>
</body>
</html>