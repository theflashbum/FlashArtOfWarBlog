<?php 
/* 
Plugin Name: Featured Posts 
Plugin URI: http://impnerd.com/featured-posts
Description: Display a featured post on your index.php or category pages.
Version: 0.2.1
Author: Gary R. Hess
Author URI: http://impnerd.com/

--GNU License
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  

See the GNU General Public License for more details:
http://www.gnu.org/licenses/gpl.txt

*/

$posts_settings = get_option('posts_settings');

// Create a custom excerpt and count by words instead of characters.
function fp_custom_excerpt($text) {
	global $post, $wpdb, $posts_settings;
        if (!empty($text)) {
                $text = get_the_content('');
                $text = apply_filters('the_content', $text);
                $text = str_replace('</XMLCDATA>', ']]&gt;', $text);
                $text = strip_tags($text);
                $words = explode(' ', $text, $posts_settings['ex_length'] + 1);
                if (count($words) > $posts_settings['ex_length']) {
                        array_pop($words);
                        $text = implode(' ', $words);
                }
        }
        return $text . '...';
}

// Display on page.
function featuredposts() {
	global $post, $wpdb, $posts_settings;

	$post = new WP_Query('p='.$posts_settings['posts_id'].'');

	$post->the_post();

	if ($posts_settings['i_display'] == 'yes') {
	$headerimages =& get_children('post_type=attachment&post_mime_type=image&post_parent=' . $posts_settings['posts_id']);
	if ($headerimages) {
		$thumb_w = get_option('thumbnail_size_w');
		$thumb_h = get_option('thumbnail_size_h');
		if ($thumb_w < $posts_settings['i_width'] || $thumb_h < $posts_settings['i_height']) {
			$image_url = wp_get_attachment_image_src(array_shift(array_keys($headerimages)), $size='full');
			$image_url = $image_url[0];
		}
		else {
			$image_url = wp_get_attachment_image_src(array_shift(array_keys($headerimages)), $size='thumbnail');
			$image_url = $image_url[0];
		}
		echo '<div class="featuredposts">
		<img src="'.$image_url.'" class="featuredimg" alt="" width="'.$posts_settings['i_width'].'" height="'.$posts_settings['i_height'].'" /><h2><a href="', the_permalink() ,'" rel="bookmark" title="',the_title(),'">',the_title(),'</a></h2>';
		if($posts_settings['fp_desc_custom'] == 'yes'){echo $posts_settings['fp_desc'];} else{ echo fp_custom_excerpt($post); }
		echo '<br /><br /><a href="',the_permalink(),'" rel="bookmark" title="',the_title(),'">Read the story &raquo;</a></div><hr />';
	}
	}
	else {
		echo '<div class="featuredposts"><h2><a href="',the_permalink(),'" rel="bookmark" title="',the_title(),'">',the_title(),'</a></h2>';
		if($posts_settings['fp_desc_custom'] == 'yes'){echo $posts_settings['fp_desc'];} else{ echo fp_custom_excerpt($post); }
		echo '<br /><br /><a href="',the_permalink(),'" rel="bookmark" title="',the_title(),'">Read the story &raquo;</a></div><hr />';
	}
}

// Add a settings link within wp-admin
function featured_posts_add_pages() {
    add_options_page('Choose your featured post', 'Featured Posts', 8, 'featuredpostsoptions', 'featured_posts_options_page');
}

// What to display on settings page.
function featured_posts_options_page() {
	global $posts_settings, $_POST;
	if (!empty($_POST)) {
		if (isset($_POST['posts_id'])) { 
			$posts_settings['posts_id'] = $_POST['posts_id'];
	}
		if (isset($_POST['ex_length'])) { 
			$posts_settings['ex_length'] = $_POST['ex_length'];
	}
		if (isset($_POST['i_width'])) { 
			$posts_settings['i_width'] = $_POST['i_width'];
	}
		if (isset($_POST['i_height'])) { 
			$posts_settings['i_height'] = $_POST['i_height'];
	}
		if (isset($_POST['fp_desc'])) { 
			$posts_settings['fp_desc'] = $_POST['fp_desc'];
	}
		$posts_settings['i_display'] = ($_POST['i_display'] == 'yes') ? 'yes' : 'no';
		$posts_settings['fp_desc_custom'] = ($_POST['fp_desc_custom'] == 'yes') ? 'yes' : 'no';
		update_option('posts_settings',$posts_settings);
		echo '<div id="message"class="updated fade"><p>Your settings have been saved.</p></div>';
	}	
	echo '<div class="wrap">
	<h2>Set Up Your Featured Posts</h2>
	<p>This plugin makes it easy to add featured posts to your site.</p>
	<form action="" method="post">
<p><label for="posts_id">Post ID: <input type="text" name="posts_id" value="' . htmlentities($posts_settings['posts_id']) . '" size="10" /></label></p>
	<p><label for="ex_length"># of words to show in excerpt: <input type="text" name="ex_length" value="' . htmlentities($posts_settings['ex_length']) . '" size="3" /></label></p>
	<p><label for="i_display">Display image? <input type="checkbox" name="i_display" value="yes"'; if ($posts_settings['i_display'] == 'yes') { echo ' checked'; }; echo ' /></label></p>
	<p><label for="i_width">Width of image: <input type="text" name="i_width" size="3" value="' . htmlentities($posts_settings['i_width']) . '" /></label><label for="i_height">Height of image: <input type="text" name="i_height" size="3" value="' . htmlentities($posts_settings['i_height']) . '" /></label></p>
	<p><label for="fp_desc_custom">Use a custom description? <input type="checkbox" name="fp_desc_custom" value="yes"'; if ($posts_settings['fp_desc_custom'] == 'yes') { echo ' checked'; }; echo ' /></label></p>
	<p><label for="fp_desc">Custom description:<br /><textarea name="fp_desc" rows="1" cols="40">' . htmlentities($posts_settings['fp_desc']) . '</textarea></label></p>
	<p><input type="submit" value="Save" /></p>
	<p>Paste this code <b>outside</b> the loop to display:  &lt;?php if function_exists(\'featuredposts\') { featuredposts(); } ?&gt;</p>
	</form>
	<p>If you like this plugin, please consider linking to <a href="http://impnerd.com/">impNERD.com</a></p></div>';

}

// Add CSS link to header.
function headfeaturedposts() {
	echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-content/plugins/featured-posts/featured-posts.css" type="text/css" media="screen" />';
}

add_action('admin_menu', 'featured_posts_add_pages');
add_action('wp_head','headfeaturedposts');

register_activation_hook( __FILE__, fp_activate);
register_deactivation_hook( __FILE__, fp_deactivate);

// Add option setting to database when this plugin is activated.
function fp_activate() {
	$posts_settings = array('posts_id' => 1,'ex_length' => 55,'i_display' => 'yes','i_width' => 233,'i_height' => 175,'fp_desc_custom' => 'no','fp_desc' => '');
	if (!get_option('posts_settings')){
		add_option('posts_settings' , $posts_settings);
	} else {
		update_option('posts_settings' , $posts_settings);
	}
}

// Delete option setting from database when this plugin is deactivated.
function fp_deactivate() {
	delete_option('posts_settings');
}
?>
