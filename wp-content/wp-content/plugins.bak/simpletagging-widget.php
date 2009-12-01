<?php
/*
Plugin Name: Simple Tagging Widget
Plugin URI: http://www.jovelstefan.de/simple-tagging-widget/
Description: Adds a sidebar widget to display Simple Tagging functions
Author: Stefan He&szlig;
License: GPL
Version: 0.4
Author URI: http://www.jovelstefan.de
*/

// This gets called at the plugins_loaded action
function widget_STP_init() {

	// check for sidebar existance
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	// Check for the required simple tagging class
	if ( !class_exists('SimpleTagging') )
		return;

	// This saves options and prints the widget's config form.
	function widget_STP_TC_control() {
		$options = $newoptions = get_option('widget_STP_TC');
		if ( $_POST['STP_TC-submit'] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['STP_TC-title']));
			$newoptions['separator'] = strip_tags(stripslashes($_POST['STP_TC-separator']));
			$newoptions['mincount'] = strip_tags(stripslashes($_POST['STP_TC-mincount']));
			$newoptions['maxcount'] = strip_tags(stripslashes($_POST['STP_TC-maxcount']));
			$newoptions['sortorder'] = strip_tags(stripslashes($_POST['STP_TC-sortorder']));
			$newoptions['showcount'] = strip_tags(stripslashes($_POST['STP_TC-showcount']));
			$newoptions['minsize'] = strip_tags(stripslashes($_POST['STP_TC-minsize']));
			$newoptions['maxsize'] = strip_tags(stripslashes($_POST['STP_TC-maxsize']));
			$newoptions['useglobal'] = strip_tags(stripslashes($_POST['STP_TC-useglobal']));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_STP_TC', $options);
		}
	?>
				<script type="text/javascript">
				controldims['simple-tagging-tag-cloudcontrol']['height'] = 320;
				</script>
				<p style="text-align:left; line-height: 100%;">
				<label for="STP_TC-title" style="line-height:25px;display:block;"><?php _e('Widget title:', 'widgets'); ?> <input type="text" id="STP_TC-title" name="STP_TC-title" value="<?php echo wp_specialchars($options['title'], true); ?>" /></label>
				<label for="STP_TC-useglobal" style="line-height:25px;"> <input type="checkbox" name="STP_TC-useglobal" <?php if ($options['useglobal'] == "on") echo "checked='checked'"; ?> /><?php _e(' Use global settings', 'widgets'); ?></label>
				<label for="STP_TC-separator" style="line-height:25px;display:block;"><?php _e('Tag Separator:', 'widgets'); ?> <input type="text" id="STP_TC-separator" name="STP_TC-separator" value="<?php echo wp_specialchars($options['separator'], true); ?>" /></label>
				<label for="STP_TC-mincount" style="line-height:25px;display:block;"><?php _e('Tag count required:', 'widgets'); ?> <input type="text" id="STP_TC-mincount" name="STP_TC-mincount" value="<?php echo wp_specialchars($options['mincount'], true); ?>" /></label>
				<label for="STP_TC-maxcount" style="line-height:25px;display:block;"><?php _e('Number of tags:', 'widgets'); ?> <input type="text" id="STP_TC-maxcount" name="STP_TC-maxcount" value="<?php echo wp_specialchars($options['maxcount'], true); ?>" /></label>
				<label for="STP_TC-showcount" style="line-height:25px;"> <input type="checkbox" name="STP_TC-showcount" <?php if ($options['showcount'] == "on") echo "checked='checked'"; ?> /><?php _e(' Show tag count', 'widgets'); ?></label><br />
				<label for="STP_TC-sortorder" style="line-height:25px;display:block;"><?php _e('Sort order:', 'widgets'); ?> <select id="STP_TC-sortorder" name="STP_TC-sortorder">
					<option><?php echo wp_specialchars($options['sortorder'], true); ?></option>
					<option value="Natural">Natural</option>
					<option value="Alpha">Alpha</option>
					<option value="Countup">Countup</option>
					<option value="Countdown">Countdown</option>
					<option value="Random">Random</option>
					</select>
				</label>
				<label for="STP_TC-minsize" style="line-height:25px;display:block;"><?php _e('Minimum size (px):', 'widgets'); ?> <input type="text" id="STP_TC-minsize" name="STP_TC-minsize" value="<?php echo wp_specialchars($options['minsize'], true); ?>" /></label>
				<label for="STP_TC-maxsize" style="line-height:25px;display:block;"><?php _e('Maximum size (px):', 'widgets'); ?> <input type="text" id="STP_TC-maxsize" name="STP_TC-maxsize" value="<?php echo wp_specialchars($options['maxsize'], true); ?>" /></label>
				<input type="hidden" name="STP_TC-submit" id="STP_TC-submit" value="1" />
				</p>
	<?php
	}

	// This prints the widget
	function widget_STP_TC($args) {
		extract($args);
		$options = get_option('widget_STP_TC');
		$title = $options['title'];
		$separator = $options['separator'];
		$mincount = $options['mincount'];
		$maxcount = $options['maxcount'];
		$sortorder = $options['sortorder'];
		$showcount = $options['showcount'];
		$useglobal = $options['useglobal'];
		$minsize = $options['minsize'];
		$maxsize = $options['maxsize'];
		echo $before_widget . $before_title . $title . $after_title;
		$linkformat = '<a style="%colorsize%" title="Tag: %tagname% (%count%)" href="%fulltaglink%">%tagname%';
		if ($showcount) $linkformat .= ' (%count%)</a>'; else $linkformat .= '</a>';
		?>
		<div id="tagcloudwidget" style="text-align:center">
			<?php if ($useglobal == "on") STP_Tagcloud(); else STP_Tagcloud($linkformat, $separator, null, $sortorder, $maxcount, $mincount, null, null, null, null, null, $maxsize, $minsize, 'px', null, null, null); ?>
		</div>
		<?php
		echo $after_widget;

	}


	// Tell Dynamic Sidebar about our new widget and its control
	register_sidebar_widget(array('Simple Tagging Tag Cloud', 'widgets'), 'widget_STP_TC');
	register_widget_control(array('Simple Tagging Tag Cloud', 'widgets'), 'widget_STP_TC_control');

}

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('plugins_loaded', 'widget_STP_init');

?>
