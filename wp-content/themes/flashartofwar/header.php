<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="http://feeds.feedburner.com/flashartofwar/NJVU" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>

<script type="text/javascript" charset="utf-8">
  var is_ssl = ("https:" == document.location.protocol);
  var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
  document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript" charset="utf-8">
  var feedback_widget_options = {};

  feedback_widget_options.display = "overlay";  
  feedback_widget_options.company = "flash_art_of_war";
  feedback_widget_options.placement = "right";
  feedback_widget_options.color = "#222";
  feedback_widget_options.style = "idea";

  var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
</script>


</head>

<body>

<!-- wrap starts here -->
<div id="wrap">

	<!--header -->
	<div id="header">			
				
		<h1 id="logo-text"><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>		
		<p id="intro">
		<?php bloginfo('description'); ?> 
		</p>	
		
		<div  id="nav">
			<ul>
<li<?php if ( is_home() or is_archive() or is_single() or is_paged() or is_search() or (function_exists('is_tag') and is_tag()) ) { echo ' class="current_page_item"'; } ?>><a href="<?php echo get_option('home'); ?>">Home</a></li>
<?php wp_list_pages('title_li=&depth=1'); ?>
			</ul>		
		</div>	
		
		<form method="get" id="quick-search" action="<?php bloginfo('url'); ?>/">
			<p>
			<label for="qsearch">Search:</label>
			<input class="tbox" id="qsearch" type="text" name="s" value="Search..." onfocus="if (this.value == 'Search...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search...';}" />
			<input class="btn" type="submit" value="Submit" />
			</p>
		</form>			
				
	<!--header ends-->					
	</div>
	
	<!-- content-wrap starts -->
	<div id="content-wrap">