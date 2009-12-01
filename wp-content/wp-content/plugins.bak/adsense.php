<?php
/*
Plugin Name: Google Adsense widget
Plugin URI: http://mikesmullin.com/freelance/2006/04/01/google-adsense-plug-in-for-wordpress-sidebar-widgets/
Description: Monetize with AdSense in your sidebar widgets!
Version: 1.0
Author: Mike Smullin
Author URI: http://mikesmullin.com/
License: GPL
Last modified: 2006-04-01 4:04pm MST
*/

// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function widget_adsense_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// Options and default values for this widget
	function widget_adsense_options() {
		return array(
			'Title' => "",
			'google_ad_client' => "",
			'google_ad_width' => 160,
			'google_ad_height' => 600,
			'google_ad_format' => "160x600_as",
			'google_ad_type' => "text",
			'google_ad_channel' => "",
			'google_color_border' => "336699",
			'google_color_bg' => "FFFFFF",
			'google_color_link' => "0000FF",
			'google_color_url' => "008000",
			'google_color_text' => "000000",
		);
	}
	
	// This is the function that outputs the Google AdSense code.
	function widget_adsense($args) {
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store and retrieve its own options.
		// Here we retrieve any options that may have been set by the user
		// relying on widget defaults to fill the gaps.
		$options = array_merge(widget_adsense_options(), get_option('widget_adsense'));
		unset($options[0]); //returned by get_option(), but we don't need it

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $options['Title'] . $after_title; ?>

<!-- Google AdSense -->		
<script type="text/javascript"><!--
<?php 
foreach($options as $key => $value)
	echo "$key = \"$value\";\r\n";
?>//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<!-- /Google AdSense -->

<?		
		echo $after_widget;
	}

	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_adsense_control() {
		// Each widget can store and retrieve its own options.
		// Here we retrieve any options that may have been set by the user
		// relying on widget defaults to fill the gaps.
		$options = array_merge(widget_adsense_options(), get_option('widget_adsense'));
		unset($options[0]); //returned by get_option(), but we don't need it

		// If user is submitting custom option values for this widget
		if ( $_POST['adsense-submit'] ) {
			// Remember to sanitize and format use input appropriately.
			foreach($options as $key => $value)
				if(array_key_exists('adsense-'.$key, $_POST))
					$options[$key] = strip_tags(stripslashes($_POST['adsense-'.$key]));

			// Save changes
			update_option('widget_adsense', $options);
		}

		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		// Be sure you format your options to be valid HTML attributes
		// before displayihng them on the page.
		foreach($options as $key => $value): ?>
			<p style="text-align:left"><label for="adsense-<?=$key?>"><?=$key?>: <input style="width: 200px;" id="adsense-<?=$key?>" name="adsense-<?=$key?>" type="text" value="<?=htmlspecialchars($value, ENT_QUOTES)?>" /></label></p>
		<? endforeach;
		echo '<input type="hidden" id="adsense-submit" name="adsense-submit" value="1" />';
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget('Google AdSense', 'widget_adsense');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control('Google AdSense', 'widget_adsense_control', 220, 50 * count(widget_adsense_options()));
}

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_adsense_init');

?>