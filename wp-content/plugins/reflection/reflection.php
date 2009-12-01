<?php
/*
Plugin Name: Reflection
Plugin URI: http://code.google.com/p/bitpress/wiki/Reflection
Description: Apply reflection effect to images with 'reflection' as one of the HTML attribute class values.
Version: 0.2
Author: Cliffano Subagio
Author URI: http://blog.cliffano.com
*/

class BitpressReflection {

	function BitpressReflection() {
		add_action('init', array(&$this, 'init'), 888);	
		add_action('wp_head', array(&$this, 'head'), 888);
		add_action('wp_footer', array(&$this, 'footer'), 888);
		add_action('admin_menu', array(&$this, 'menu'), 888);
	}

	function init() {
		if (!empty($_REQUEST['bitpress_reflection_submit'])) {
			update_option('bitpress_reflection_bgcolor', $_POST['bitpress_reflection_bgcolor']);
			update_option('bitpress_reflection_height', $_POST['bitpress_reflection_height']);
			header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=reflection.php&updated=true');
			die();
		}
	}
	
	function head() {
		echo '<script type="text/javascript" src="' . get_option('siteurl') . '/wp-content/plugins/reflection/' . 'reflection-raphael.js"></script>';
		echo '<script type="text/javascript" src="' . get_option('siteurl') . '/wp-content/plugins/reflection/' . 'reflection.js"></script>';
	}
	
	function footer() {
		if (get_option('bitpress_reflection_bgcolor') != null &&
				trim(get_option('bitpress_reflection_bgcolor')) != "" &&
				get_option('bitpress_reflection_height') != null &&
				trim(get_option('bitpress_reflection_height')) != "") {
			echo '<script type="text/javascript">new BitpressImageMgr("' . wp_specialchars(get_option('bitpress_reflection_bgcolor')) . '", "' . wp_specialchars(get_option('bitpress_reflection_height')) . '").process(document.images);</script>';
		} else {
			echo '<script type="text/javascript">new BitpressImageMgr().process(document.images);</script>';
		}
	}

	function menu() {
		add_options_page(
			__('Reflection', 'bitpress_reflection')
			, __('Reflection', 'bitpress_reflection')
			, 8 
			, basename(__FILE__)
			, 'bitpress_reflection_menu_form'
		);
	}
}

function bitpress_reflection_menu_form() {
	print('
		<div class="wrap">
			<h2>' . __('Reflection', 'bitpress_reflection') . '</h2>
			<form method="post">
				<fieldset>
					<p>
						<b>Gradient Background Color:</b>
						<input type="text" id="bitpress_reflection_bgcolor" name="bitpress_reflection_bgcolor" value="' . get_option('bitpress_reflection_bgcolor') . '" size="7"/>
						<br/>
						Specify <a href="http://www.w3schools.com/HTML/html_colornames.asp">HTML Color Names</a> like white, black, navy, etc.
						Or <a href="http://www.w3schools.com/Html/html_colors.asp">HTML Color Hex</a> like #fff, #000, #000080 (note that you have to prefix the hex value with # sign).
						<br/>
					</p>
					<p>
						<b>Gradient Height:</b>
						<input type="text" id="bitpress_reflection_height" name="bitpress_reflection_height" value="' . get_option('bitpress_reflection_height') . '" size="5"/>
						<br/>
						Specify pixels (e.g. 50px, 88px) or percentage (e.g. 80%, 35%), please note that the px or % sign is required.
						This value determines the height of the gradient reflection, pixels value indicates fixed height, while percentage value is a percentage of the image height.
					</p>
				</fieldset>
				<p>
					<input type="submit" value="' . __('Update Reflection Options', 'bitpress_reflection') . '"/>
					<input type="hidden" name="bitpress_reflection_submit" value="true"/>
				</p>
			</form>
		</div>
		');
}

$bitpress_reflection = new BitpressReflection();
?>