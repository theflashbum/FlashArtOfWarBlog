<?php
/*
Plugin Name: Lightbox JS v2.03.3 Plugin
Plugin URI: http://www.4mj.it/lightbox-js-v20-wordpress/
Feed URI: 
Description: Used to overlay images on the current page. Lightbox JS v2.03.2 by <cite><a href="http://www.huddletogether.com/projects/lightbox2/" title="Lightbox JS v2.0 ">Lokesh Dhakar</a>.</cite>
Version: 1.7
Author: Peppe Argento
Author URI: http://www.4mj.it
*/
// styles
function lightbox_styles() {
	$lightbox_path = get_option('siteurl')."/wp-content/plugins/lightbox/";
	$lightboxscript.= "<script type=\"text/javascript\"> lb_path = \"$lightbox_path\"; </script>\n";
	$lightboxscript.= "<link rel=\"stylesheet\" href=\"".$lightbox_path."lightbox.css\" type=\"text/css\" media=\"screen\" />\n";
	$lightboxscript.= "<script type=\"text/javascript\" src=\"".$lightbox_path."prototype.js\"></script>\n";
	$lightboxscript.= "<script type=\"text/javascript\" src=\"".$lightbox_path."scriptaculous.js?load=effects\"></script>\n";
	$lightboxscript.= "<script type=\"text/javascript\" src=\"".$lightbox_path."lightbox.js\"></script>\n";
	print($lightboxscript);
}
/*function lightbox_create($content){
	return preg_replace('/<a(.*?)href=(.*?).(jpg|jpeg|png|gif|bmp|ico)"(.*?)>/i', '<a$1href=$2.$3" $4 rel="lightbox[roadtrip]">', $content);
}
*/
add_action('wp_head', 'lightbox_styles');
// add_filter('the_content', 'lightbox_create', 2);
?>