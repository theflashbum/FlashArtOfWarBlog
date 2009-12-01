<?php
/*
Plugin Name: WP-SWFObject
Plugin URI: http://blog.unijimpe.net/wp-swfobject/
Description: Allow insert Flash Movies into WordPress blog using SWFObject library. For use this plugin: [SWF]pathtofile, width, height[/SWF].
Version: 2.0
Author: Jim Penaloza Calixto 
Author URI: http://blog.unijimpe.net
*/

define("wp_swf_version", "swf_version", true);
define("wp_swf_bgcolor", "swf_bgcolor", true);
define("wp_swf_wmode", "swf_wmode", true);
define("wp_swf_menu", "swf_menu", true);
define("wp_swf_quality", "swf_quality", true);
define("wp_swf_fullscreen", "swf_fullscreen", true);
define("wp_swf_align", "swf_align", true);
define("wp_swf_message", "swf_message", true);

define("wp_swf_version_default", "9.0.0", true);
define("wp_swf_bgcolor_default", "#FFFFFF", true);
define("wp_swf_wmode_default", "window", true);
define("wp_swf_menu_default", "false", true);
define("wp_swf_quality_default", "high", true);
define("wp_swf_fullscreen_default", "false", true);
define("wp_swf_align_default", "none", true);
define("wp_swf_message_default", "This movie requires Flash Player 9", true);

add_option(wp_swf_version, wp_swf_version_default, 'Version of Flash Player.');
add_option(wp_swf_bgcolor, wp_swf_bgcolor_default, 'Background Color for Flash Movie.');
add_option(wp_swf_wmode, wp_swf_wmode_default, 'WMode for Flash Movie.');
add_option(wp_swf_menu, wp_swf_menu_default, 'Option for Activate menu for Flash Movie.');
add_option(wp_swf_quality, wp_swf_quality_default, 'Default quality for Flash Movie.');
add_option(wp_swf_fullscreen, wp_swf_fullscreen_default, 'If Allow Fullscreen mode for Flash Movie.');
add_option(wp_swf_align, wp_swf_align_default, 'Align for Flash Movie.');
add_option(wp_swf_message, wp_swf_message_default, 'Message for missing player.');

$wpswf_version	= "2.0";									// version of plugin 
$wpswf_random	= substr(md5(uniqid(rand(), true)),0,4);	// create unique id for divs
$wpswf_number	= 0; 										// number of swf into page
$wpswf_array	= array();     								// array of swfs


function wpswfConfig() {
	// get config options into array var
    static $config;
    if ( empty($config) ) {
        $config['wp_swf_message'] = get_option(wp_swf_message);
        $config['wp_swf_wmode'] = get_option(wp_swf_wmode);
        $config['wp_swf_menu'] = get_option(wp_swf_menu);
        $config['wp_swf_quality'] = get_option(wp_swf_quality);
        $config['wp_swf_version'] = get_option(wp_swf_version);
		$config['wp_swf_fullscreen'] = get_option(wp_swf_fullscreen);
		$config['wp_swf_align'] = get_option(wp_swf_align);
        $config['wp_swf_bgcolor'] = get_option(wp_swf_bgcolor);
    }
    return $config;
}
function wpswfParse($text) {
    return preg_replace_callback('|\[swf\](.+?),\s*(\d+)\s*,\s*(\d+)\s*(,(.+?))?\[/swf\]|i', 'wpswfObject', $text);
}
function wpswfObject($match) {
    global $wpswf_random, $wpswf_number, $wpswf_array;
	$wpswf_config = wpswfConfig();
	$wpswf_number++;
	
    if (is_feed() || $doing_rss) {
		// for feed insert using tag <object>
		$writeswf.= "\n<object type=\"application/x-shockwave-flash\" width=\"".$match[2]."\" height=\"".$match[3]."\">\n";
		$writeswf.= "<param name=\"movie\" value=\"".$match[1]."\" />\n";
		if ($match[4] != "") {
			$writeswf.= "<param name=\"flashvars\" value=\"".trim(substr($match[4],1))."\" />\n";
		}
		$writeswf.= "<embed src=\"".$match[1]."\" type=\"application/x-shockwave-flash\" ";
		$writeswf.= "width=\"".$match[2]."\" height=\"".$match[3]."\" ";
		if ($match[4] != "") {
			$writeswf.= "flashvars=\"".trim(substr($match[4],1))."\" ";
		}
		$writeswf.= ">\n";
		$writeswf.= "</object>\n";
	} else {
		// for web insert SWFObject
		if ($wpswf_config['wp_swf_align'] != "none" && $wpswf_config['wp_swf_align'] != "") {
			$writeswf.= "<div style=\"text-align:".$wpswf_config['wp_swf_align'].";\">";
			$writeswf.= "<div id=\"swf".$wpswf_random.$wpswf_number."\" style=\"width:".$match[2]."px; height:".$match[3]."px; line-height:".$match[3]."px;\">".$wpswf_config['wp_swf_message']."</div>";
			$writeswf.= "</div>";
		} else { 
			$writeswf.= "<div id=\"swf".$wpswf_random.$wpswf_number."\">".$wpswf_config['wp_swf_message']."</div>";
		}
		$wpswf_fvars = "";
		if ($match[4] != "") {
			$aleParam = ereg_replace("amp;","",$match[4]);
			parse_str(trim(substr($aleParam,1)), $params);
			foreach ($params as $param => $value) {
				if ($wpswf_fvars == "") {
					$wpswf_fvars .= $param . ": \"".$value."\"";
				} else {
					$wpswf_fvars .= ", ". $param . ": \"".$value."\"";
				}
			}
		}
		$wpswf_array[$wpswf_number] = array('file'=>$match[1], 'width'=>$match[2], 'height'=>$match[3], 'flashvars'=>"{".$wpswf_fvars."}"); 
	}
	return $writeswf;
}
function wpswfAddfooter() {
	global $wpswf_random, $wpswf_number, $wpswf_array;
	$wpswf_config = wpswfConfig();
	
	echo "\n\t<script type=\"text/javascript\">\n";
	$wpswf_params = "wmode: \"".$wpswf_config['wp_swf_wmode']."\", ";
	$wpswf_params.= "menu: \"".$wpswf_config['wp_swf_menu']."\", ";
	$wpswf_params.= "quality: \"".$wpswf_config['wp_swf_quality']."\", ";
	$wpswf_params.= "bgcolor: \"".$wpswf_config['wp_swf_bgcolor']."\"";
	if ($wpswf_config['wp_swf_fullscreen'] == "true") {
		$wpswf_params.= ", allowFullScreen: \"".$wpswf_config['wp_swf_fullscreen']."\"";
	}
	foreach ($wpswf_array as $key => $value) {
		echo "\t\tswfobject.embedSWF(\"".$value['file']."\", \"swf".$wpswf_random.$key."\", \"".$value['width']."\", \"".$value['height']."\", \"".$wpswf_config['wp_swf_version']."\", \"\", ".$value['flashvars'].", {".$wpswf_params."}, {});\n";
	}
	echo "\t</script>\n";
}
function wp_swfobject_echo($swffile, $swfwidth, $swfheigth, $swfvars = "") {
    echo wpswfObject( array( null, $swffile, $swfwidth, $swfheigth, "&".$swfvars) );
}
function wpswfOptionsPage() {
	global $wpswf_version;
	if (isset($_POST['wp_swf_update'])) {
		check_admin_referer();
		// Update version
		$swf_version = $_POST[wp_swf_version];
		if ($swf_version == '')
			$swf_version = wp_swf_version_default;
		update_option(wp_swf_version, $swf_version);
		// Update bgcolor
		$swf_bgcolor = $_POST[wp_swf_bgcolor];
		if ($swf_bgcolor == '')
			$swf_bgcolor = wp_swf_bgcolor_default;
		update_option(wp_swf_bgcolor, $swf_bgcolor);
		// Update wmode
		$swf_wmode = $_POST[wp_swf_wmode];
		update_option(wp_swf_wmode, $swf_wmode);
		// Update menu
		$swf_menu = $_POST[wp_swf_menu];
		update_option(wp_swf_menu, $swf_menu);
		// Update quality
		$swf_quality = $_POST[wp_swf_quality];
		update_option(wp_swf_quality, $swf_quality);
		// Update fullscreen
		$swf_fullscreen = $_POST[wp_swf_fullscreen];
		update_option(wp_swf_fullscreen, $swf_fullscreen);
		// Update align
		$swf_align = $_POST[wp_swf_align];
		update_option(wp_swf_align, $swf_align);
		// Update bgcolor
		$swf_message = $_POST[wp_swf_message];
		update_option(wp_swf_message, $swf_message);
		
		// echo message updated
		echo "<div class='updated'><p><strong>WP-SWFObject options updated</strong></p></div>";
	}
?>
		<form method="post" action="options-general.php?page=wp-swfobject.php">
		<div class="wrap">
			<h2>WP-SWFObject <sup style='color:#D54E21;font-size:12px;'><?php echo $wpswf_version; ?></sup></h2>
			
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_version; ?>">Flash Player Version:</label>
						</th>
						<td>
							<?php
							echo "<input type='text' size='16' maxlength='12' ";
							echo "name='".wp_swf_version."' ";
							echo "id='".wp_swf_version."' ";
							echo "value='".get_option(wp_swf_version)."' />\n";
							?>
							Enter number of flash version required for flash player.
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_bgcolor; ?>">Background Color:</label>
						</th>
						<td>
							<?php
							echo "<input type='text' size='16' maxlength='7' ";
							echo "name='".wp_swf_bgcolor."' ";
							echo "id='".wp_swf_bgcolor."' ";
							echo "value='".get_option(wp_swf_bgcolor)."' />\n";
							?>
							Enter HEX number for background color for flash movie.
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_wmode; ?>">Window Mode:</label>
						</th>
						<td>
							<?php
							echo "<select name='".wp_swf_wmode."' id='".wp_swf_wmode."'>\n";

							echo "<option value='window'";
							if(get_option(wp_swf_wmode) == "window")
								echo " selected='selected'";
							echo ">Window</option>\n";
							
							echo "<option value='opaque'";
							if(get_option(wp_swf_wmode) == "opaque")
								echo " selected='selected'";
							echo ">Opaque</option>\n";
							
							echo "<option value='transparent'";
							if(get_option(wp_swf_wmode) == "transparent")
								echo " selected='selected'";
							echo ">Transparent</option>\n";
							
							echo "</select>\n";
							?>
							Select wmode for movie, by defaul is <strong>window</strong>.
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_menu; ?>">Show Menu:</label>
						</th>
						<td>
							<?php
							echo "<select name='".wp_swf_menu."' id='".wp_swf_menu."'>\n";

							echo "<option value='true'";
							if(get_option(wp_swf_menu) == "true")
								echo " selected='selected'";
							echo ">True</option>\n";
							
							echo "<option value='false'";
							if(get_option(wp_swf_menu) == "false")
								echo " selected='selected'";
							echo ">False</option>\n";
							
							echo "</select>\n";
							?>
							Select option for show/hide menu.
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_bgcolor; ?>">Quality Movie:</label>
						</th>
						<td>
							<?php
							echo "<select name='".wp_swf_quality."' id='".wp_swf_quality."'>\n";

							echo "<option value='low'";
							if(get_option(wp_swf_quality) == "low")
								echo " selected='selected'";
							echo ">Low</option>\n";
							
							echo "<option value='autolow'";
							if(get_option(wp_swf_quality) == "autolow")
								echo " selected='selected'";
							echo ">Autolow</option>\n";
							
							echo "<option value='autohigh'";
							if(get_option(wp_swf_quality) == "autohigh")
								echo " selected='selected'";
							echo ">Autohigh</option>\n";
							
							echo "<option value='medium'";
							if(get_option(wp_swf_quality) == "medium")
								echo " selected='selected'";
							echo ">Medium</option>\n";
							
							echo "<option value='high'";
							if(get_option(wp_swf_quality) == "high")
								echo " selected='selected'";
							echo ">High</option>\n";
							
							echo "<option value='best'";
							if(get_option(wp_swf_quality) == "best")
								echo " selected='selected'";
							echo ">Best</option>\n";
							
							echo "</select>\n";
							?>
							Select quality for flash movie, by default is <strong>hight</strong>.
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_fullscreen; ?>">Allow Fullscreen:</label>
						</th>
						<td>
							<?php
							echo "<select name='".wp_swf_fullscreen."' id='".wp_swf_fullscreen."'>\n";

							echo "<option value='true'";
							if(get_option(wp_swf_fullscreen) == "true")
								echo " selected='selected'";
							echo ">True</option>\n";
							
							echo "<option value='false'";
							if(get_option(wp_swf_fullscreen) == "false")
								echo " selected='selected'";
							echo ">False</option>\n";
							
							echo "</select>\n";
							?>
							Allow Fullscreen (You must have version >= 9,0,28,0 of Flash Player).
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_align; ?>">Align:</label>
						</th>
						<td>
							<?php
							echo "<select name='".wp_swf_align."' id='".wp_swf_align."'>\n";

							echo "<option value='none'";
							if(get_option(wp_swf_align) == "none")
								echo " selected='selected'";
							echo ">None</option>\n";
							
							echo "<option value='left'";
							if(get_option(wp_swf_align) == "left")
								echo " selected='selected'";
							echo ">Left</option>\n";
							
							echo "<option value='center'";
							if(get_option(wp_swf_align) == "center")
								echo " selected='selected'";
							echo ">Center</option>\n";
							
							echo "<option value='right'";
							if(get_option(wp_swf_align) == "right")
								echo " selected='selected'";
							echo ">Right</option>\n";
														
							echo "</select>\n";
							?>
							Align for Flash Movies into Post.
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="<?php echo wp_swf_version; ?>">Message Require Flash:</label>
						</th>
						<td>
							<?php
							echo "<input type='text' size='50' ";
							echo "name='".wp_swf_message."' ";
							echo "id='".wp_swf_message."' ";
							echo "value='".get_option(wp_swf_message)."' />\n";
							?>
							Enter message for warning missing player.
						</td>
					</tr>
					</table>
					<p class="submit">
					  <input name="wp_swf_update" value="Save Changes" type="submit" />
					</p>
					<table>
					<tr>
						<th width="30%" valign="top" style="padding-top: 10px; text-align:left;" colspan="2">
							More Information and Support
						</th>
					</tr>
					<tr>
						<td colspan="2">
						  <p>Check our links for updates and comment there if you have any problems / questions / suggestions. </p>
					      <ul>
					        <li><a href="http://blog.unijimpe.net/wp-swfobject/">Plugin Home Page</a></li>
			                <li><a href="http://forum.unijimpe.net/?CategoryID=4">Plugin Forum Support</a> </li>
			                <li><a href="http://blog.unijimpe.net/">Author Home Page</a></li>
				            <li><a href="http://code.google.com/p/swfobject/">SWFObject 2.0 Home Page</a> </li>
				        </ul></td>
				  </tr>
				</table>
			
		</div>
		</form>
<?php
}
function wpswfAddMenu() {
	add_options_page('WP-SWFObject Options', 'WP-SWFObject', 8, basename(__FILE__), 'wpswfOptionsPage');
}
function wpswfAddheader() {
	global $wpswf_version;
	echo "\n<!-- WP-SWFObject ".$wpswf_version." by unijimpe -->";
	echo "\n<script src=\"".get_settings('siteurl')."/wp-content/plugins/wp-swfobject/swfobject.js\" type=\"text/javascript\"></script>\n";
}

add_filter('the_content', 'wpswfParse');
add_action('wp_head', 'wpswfAddheader');
add_action('wp_footer', 'wpswfAddfooter', 10);
add_action('admin_menu', 'wpswfAddMenu');
?>