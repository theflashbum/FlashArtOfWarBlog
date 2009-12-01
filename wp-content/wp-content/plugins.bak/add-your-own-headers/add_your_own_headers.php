<?php

/*
Plugin Name: Add Your Own Headers
Plugin URI: http://wp.uberdose.com/2007/03/30/add-your-own-headers/
Description: Plugin for inserting your own headers into posts.
Version: 0.1
Author: some guy
Author URI: http://wp.uberdose.com/
*/

/* Copyright (C) 2007 Dirk Zimmermann (dirk AT uberdose DOT com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA */
 
 class Add_Your_Own_Headers {
 	
 	var $version = "0.1";

	function wp_head() {
		global $post;
		$meta_string = null;
		
		echo "<!-- add_your_own_headers $this->version -->\n";
		
		// custom tags
        $custom_001 = stripslashes(get_post_meta($post->ID, "custom_header_001", true));
        $custom_002 = stripslashes(get_post_meta($post->ID, "custom_header_002", true));
		if (isset ($custom_001) && !empty($custom_001)) {
			$meta_string .= sprintf("%s\n", $custom_001);
		}
		if (isset ($custom_002) && !empty($custom_002)) {
			$meta_string .= sprintf("%s\n", $custom_002);
		}

		if ($meta_string != null) {
			echo $meta_string;
		}
	}
	
	function add_headers_textinput() {
	    global $post;
	    $custom_tag_001 = htmlspecialchars(get_post_meta($post->ID, 'custom_header_001', true));
	    $custom_tag_002 = htmlspecialchars(get_post_meta($post->ID, 'custom_header_002', true));
		?>
		<form name="dofollow" action="" method="post">
		<input value="ayoh_edit" type="hidden" name="ayoh_edit" />
		<table style="margin-bottom:40px; margin-top:30px;">
		<tr><th style="text-align:left;" colspan="2">Add Your Own Headers (by <a title="Homepage for Add Your Own Headers" href="http://wp.uberdose.com/2007/03/25/add-your-own-headers-01/">Add Your Own Headers</a>)</th></tr>
		<tr>
		<th scope="row" style="text-align:right;"><?php _e('Custom Header:') ?></th>
		<td><input value="<?php echo $custom_tag_001 ?>" type="text" name="custom_header_001" size="50"/></td>
		</tr>
		<tr>
		<th scope="row" style="text-align:right;"><?php _e('Another Custom Header:') ?></th>
		<td><input value="<?php echo $custom_tag_002 ?>" type="text" name="custom_header_002" size="50"/></td>
		</tr>
		</table>
		</form>
		<?php
	}

	function post_headers($id) {
	    $ayoh_edit = $_POST["ayoh_edit"];
	    if (isset($ayoh_edit) && !empty($ayoh_edit)) {
		    $custom_tag_001 = stripslashes($_POST["custom_header_001"]);
		    $custom_tag_002 = stripslashes($_POST["custom_header_002"]);

		    delete_post_meta($id, 'custom_header_001');
		    delete_post_meta($id, 'custom_header_002');

		    if (isset($custom_tag_001) && !empty($custom_tag_001)) {
			    add_post_meta($id, 'custom_header_001', $custom_tag_001);
		    }
		    if (isset($custom_tag_002) && !empty($custom_tag_002)) {
			    add_post_meta($id, 'custom_header_002', $custom_tag_002);
		    }
	    }
	}

}

$_ayoh = new Add_Your_Own_Headers();
add_action('wp_head', array($_ayoh, 'wp_head'));

add_action('simple_edit_form', array($_ayoh, 'add_headers_textinput'));
add_action('edit_form_advanced', array($_ayoh, 'add_headers_textinput'));
add_action('edit_page_form', array($_ayoh, 'add_headers_textinput'));

add_action('edit_post', array($_ayoh, 'post_headers'));
add_action('publish_post', array($_ayoh, 'post_headers'));
add_action('save_post', array($_ayoh, 'post_headers'));
add_action('edit_page_form', array($_ayoh, 'post_headers'));

?>
