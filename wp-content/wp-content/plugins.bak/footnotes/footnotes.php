<?php
/*
Plugin Name: WP-Footnotes
Plugin URI: http://www.elvery.net/drzax/more-things/wordpress-footnotes-plugin/
Version: 1.4
Description: Allows a user to easily add footnotes to a post.
Author: Simon Elvery
Author URI: http://www.elvery.net/drzax/
*/

/*
 * This file is part of WP-Footnotes a plugin for Word Press
 * Copyright (C) 2007 Simon Elvery
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
 

// Some important constants
define('FOOTNOTE_OPEN', " ((");  //You can change this if you really have to, but I wouldn't recommend it.
define('FOOTNOTE_CLOSE', "))");  //Same with this one.

// Get the current settings or setup some defaults if needed
if (!$current_settings = get_option('swas_footnote_options')){
	$footnotes_options['superscript'] = true;
	$footnotes_options['smooth_scroll'] = false;
	
	$footnotes_options['pre_backlink'] = ' [';
	$footnotes_options['backlink'] = '&#8617;';
	$footnotes_options['post_backlink'] = ']';

	$footnotes_options['pre_identifier'] = '';
	$footnotes_options['list_style_type'] = 'decimal';
	$footnotes_options['post_identifier'] = '';

	$footnotes_options['pre_footnotes'] = '';
	$footnotes_options['post_footnotes'] = '';
	$footnotes_options['style_rules'] = 'ol.footnotes{font-size:0.7em; color:#666666;}';
	
	$footnotes_options['no_display_home'] = false;
	$footnotes_options['no_display_archive'] = false;
	$footnotes_options['no_display_date'] = false;
	$footnotes_options['no_display_category'] = false;
	$footnotes_options['no_display_search'] = false;
	$footnotes_options['no_display_feed'] = false;
	
	
	update_option('swas_footnote_options', $footnotes_options);
}

// Include number conversion if we need it
if ($current_settings['list_style_type'] != 'decimal') {require_once('num2char.php');}

/**
 * Searches the text and extracts footnotes. 
 * Adds the superscrip links and creats footnotes list.
 * @param $data string The content of the post.
 * @return string The new content with footnotes generated.
 */
function swas_footnote($data) {
	global $post, $current_settings;
	
	// Check for and setup the starting number
	$start_number = (preg_match("|<!\-\-startnum=(\d+)\-\->|",$data,$start_number_array)==1) ? $start_number_array[1] : 1;

	// Let's attempt this with Regex instead
	preg_match_all("/(".preg_quote(FOOTNOTE_OPEN)."|<footnote>)(.*)(".preg_quote(FOOTNOTE_CLOSE)."|<\/footnote>)/U", $data, $footnotes, PREG_SET_ORDER);

	// Check whether we are displaying them or not
	$display = true;
	if ($current_settings['no_display_home'] && is_home()) $display = false;
	if ($current_settings['no_display_archive'] && is_archive()) $display = false;
	if ($current_settings['no_display_date'] && is_date()) $display = false;
	if ($current_settings['no_display_category'] && is_category()) $display = false;
	if ($current_settings['no_display_search'] && is_search()) $display = false;
	if ($current_settings['no_display_feed'] && is_feed()) $display = false;
	
	// Create 'em
	if (count($footnotes)>0){
		// Look for ref: and replace in array before duplicates are removed.
		for ($i; $i<count($footnotes); $i++){
			if (substr($footnotes[$i][2],0,4) == 'ref:'){
				$ref = (int)substr($footnotes[$i][2],4);
				$footnotes[$i][2] = $footnotes[$ref-1][2];
			}
		}
		$footnotes = swas_remove_dups($footnotes, 2);
		
		if ($display) {
		
			$data = $data.$current_settings['pre_footnotes'].'<ol start="'.$start_number.'" class="footnotes">';
			foreach($footnotes as $identifier=>$note){
				$number = $start_number+$identifier;// What number is it? Used for back link and id.
				$data = $data.'<li id="footnote-'.$number.'-'.$post->ID.'" class="footnote">'.$note[2].$current_settings['pre_backlink'].'<a href="#footnote-link-'.$number.'-'.$post->ID.'" class="footnote-link footnote-back-link">'.$current_settings['backlink'].'</a>'.$current_settings['post_backlink'].'</li>';
				$link_text = ($current_settings['list_style_type'] != 'decimal') ? swas_convert_num($number, $current_settings['list_style_type']) : $number;
				$link_text = $current_settings['pre_link'].'<a href="#footnote-'.$number.'-'.$post->ID.'" id="footnote-link-'.$number.'-'.$post->ID.'" class="footnote-link footnote-identifier-link" title="'.strip_tags($footnotes[$number-1][2]).'">'.$link_text.'</a>'.$current_settings['post_link'];
				if ($current_settings['superscript']) $link_text = '<sup>'.$link_text.'</sup>';
				$data = str_replace($note[0], $link_text, $data);
				$data = str_replace(' ((ref:' . ($identifer+1) . '))', $link_text, $data);
				$data = str_replace('<footnote>' . ($identifer+1) . '</footnote>', $link_text, $data);
			}
			$data = $data.'</ol>'.$current_settings['post_footnotes'];
		}else{
			foreach($footnotes as $identifier=>$note){
				$data = str_replace($note[0], '', $data);
				$data = str_replace(' ((ref:' . ($identifer+1) . '))', '', $data);
				$data = str_replace('<footnote>' . ($identifer+1) . '</footnote>', '', $data);
			}	
		}
	}
    return $data;
}
function swas_smooth_scroll(){
	?>
<script type="text/javascript" language="javascript" src="http://yui.yahooapis.com/2.2.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo get_option('siteurl');?>/wp-content/plugins/footnotes/smooth_scroll.js"></script>
<?php
}

/**
 * Initialising new array to the first element of the given array.
 * Check whether current element in initial array has already been added to new array.
 * If yes break to save us some time. If no, then add current element to new array.
 * From: http://au3.php.net/manual/en/function.array-unique.php#68339
 */
function swas_remove_dups($array, $row_element) {   
   $new_array[0] = $array[0];
   foreach ($array as $current) {
       $add_flag = 1;
       foreach ($new_array as $tmp) {
           if ($current[$row_element]==$tmp[$row_element]) {
               $add_flag = 0; break;
           }
       }
       if ($add_flag) $new_array[] = $current;
   } return $new_array;
} // end function remove_dups

function swas_footnotes_options_page() { 
	$current_settings = get_option('swas_footnote_options');
	if ($_POST['action']){?>
		<div class="updated"><p><strong>Options saved.</strong></p></div>
 	<?php } ?>
    <div class="wrap" id="footnote-options">
		
		<h2>WP-Footnotes Options</h2>
		<div id="donation" style="float:right; width:30%; background-color:#C1DFFF; border:1px solid #99CCFF; padding:3px;">
			<h4>Bug Reports / Feature Requests</h4>
			<p>You should report any bugs you find and submit feature requests to <a href="http://bugs.elvery.net">the bug tracker</a>.</p>
			<h4>Donations</h4>
			<p>If you want to share the love, or if you want to see your feature request moved to the top of my to-do list, hit me up with a donation. It would be most appreciated.</p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
				<img alt="" border="0" src="https://www.paypal.com/en_AU/i/scr/pixel.gif" width="1" height="1" style="display:block; margin:auto;" />
				<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
				">
				</form>
		</div>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>">
			<input type="hidden" name="action" value="save_options" />
			<fieldset class="options">
				<legend>Note Identifier Options</legend>
				<table>
					<tr><th>Before</th><th>Style</th><th>After</th></tr>
					<tr>
						<td><input type="text" name="pre_identifier" size="3" value="<?php echo $current_settings['pre_identifier']; ?>" /></td>
						<td>
							<select name="list_style_type">
								<option value="decimal" <?php if ($current_settings['list_style_type'] == 'decimal') echo 'selected="selected"'; ?>>1,2,3</option>
								<option value="lower-alpha" <?php if ($current_settings['list_style_type'] == 'lower-alpha') echo 'selected="selected"'; ?>>a,b,c</option>
								<option value="upper-alpha" <?php if ($current_settings['list_style_type'] == 'upper-alpha') echo 'selected="selected"'; ?>>A,B,C</option>
								<option value="lower-roman" <?php if ($current_settings['list_style_type'] == 'lower-roman') echo 'selected="selected"'; ?>>i,ii,iii</option>
								<option value="upper-roman" <?php if ($current_settings['list_style_type'] == 'upper-roman') echo 'selected="selected"'; ?>>I,II,III</option>
							</select>
						</td>
						<td><input type="text" name="post_identifier" size="3" value="<?php echo $current_settings['post_identifier']; ?>"  /></td>
					</tr>
				</table>
				<label for="superscript"><input type="checkbox" name="superscript" id="superscript" <?php if($current_settings['superscript'] == true) echo 'checked'; ?> /> Make note identifier superscript.</label>
			</fieldset>
			<fieldset class="options">
				<legend>Back-link Options</legend>
				<p>These options affect how the back-links after each footnote look. A good back-link character is &amp;#8617; (&#8617). If you want to remove the back-links all together, you can effectively do so by making all these settings blank.</p>
				<table>
					<tr><th>Before</th><th>Link</th><th>After</th></tr>
					<tr>
						<td><input type="text" name="pre_backlink" size="3" value="<?php echo $current_settings['pre_backlink']; ?>" /></td>
						<td><input type="text" name="backlink" size="10" value="<?php echo $current_settings['backlink']; ?>"  /></td>
						<td><input type="text" name="post_backlink" size="3" value="<?php echo $current_settings['post_backlink']; ?>"  /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="options">
				<legend>General</legend>
				<label for="smooth_scroll"><input type="checkbox" name="smooth_scroll" id="smooth_scroll" <?php if($current_settings['smooth_scroll'] == true) echo 'checked'; ?> /> Use Javascript to scroll smoothly between the note identifier and the footnote.</label><br />
				<small>Note, this part of WP-Footnotes uses a hosted version of the <a href="http://developer.yahoo.com/yui/">Yahoo! User Interface (YUI)</a> utilities.</small> <br /><br />
				<label for="pre_footnotes">Anything to be displayed <strong>before</strong> the footnotes at the bottom of the post can go here:</label><br />
				<textarea rows="3" cols="60" name="pre_footnotes"><?php echo $current_settings['pre_footnotes']; ?></textarea><br />
				<label for="post_footnotes">Anything to be displayed <strong>after</strong> the footnotes at the bottom of the post can go here:</label><br />
				<textarea rows="3" cols="60" name="post_footnotes"><?php echo $current_settings['post_footnotes']; ?></textarea><br />
				<label for="style_rules">Some CSS to style the footnotes (or anything else on the page for that matter):</label><br />
				<textarea rows="3" cols="60" name="style_rules"><?php echo $current_settings['style_rules']; ?></textarea><br />
				<p>Do not display footnotes at all when the page being shown is:</p>
				<ul style="list-style-type:none;">
					<li><label for="no_display_home"><input type="checkbox" name="no_display_home" id="no_display_home" <?php if($current_settings['no_display_home'] == true) echo 'checked'; ?> /> the home page</label></li>
					<li><label for="no_display_search"><input type="checkbox" name="no_display_search" id="no_display_search" <?php if($current_settings['no_display_search'] == true) echo 'checked'; ?> /> search results</label></li>
					<li><label for="no_display_feed"><input type="checkbox" name="no_display_feed" id="no_display_feed" <?php if($current_settings['no_display_feed'] == true) echo 'checked'; ?> /> a feed (RSS, Atom, etc)</label></li>
					<li><label for="no_display_archive"><input type="checkbox" name="no_display_archive" id="no_display_archive" <?php if($current_settings['no_display_archive'] == true) echo 'checked'; ?> /> an archive page of any kind</label></li>
					<li>
						<ul style="list-style-type:none;">
							<li><label for="no_display_category"><input type="checkbox" name="no_display_category" id="no_display_category" <?php if($current_settings['no_display_category'] == true) echo 'checked'; ?> /> a category archive</label></li>
							<li><label for="no_display_date"><input type="checkbox" name="no_display_date" id="no_display_date" <?php if($current_settings['no_display_date'] == true) echo 'checked'; ?> /> a date based archive page</label></li>
						</ul>
					</li>
				</ul>
			
			</fieldset>
			<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>
		</form>
	</div>
	<div class="wrap">
		<h2>Licensing Info</h2>
		<p>WP-Footnotes, Copyright &copy; 2007 Simon Elvery</p>
		<p>WP-Footnotes is licensed under the <a href="http://www.gnu.org/licenses/gpl.html">GNU GPL</a>. WP-Footnotes comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under certain conditions. See the <a href="http://www.gnu.org/licenses/gpl.html">license</a> for details.</p>
		<p>The smooth scroll component uses <a href="http://developer.yahoo.net/about/">Web Services by Yahoo!</a> and some code written by <a href="http://techfoolery.com/author/ross/">Ross Harmes</a>.</p>
	</div>
<?php 
}

function swas_add_options() {
	// Add a new menu under Options:
	add_options_page('Footnotes', 'Footnotes', 8, __FILE__, 'swas_footnotes_options_page');
}

function swas_save_options() {
	$footnotes_options['superscript'] = (array_key_exists('superscript', $_POST)) ? true : false;
	$footnotes_options['smooth_scroll'] = (array_key_exists('smooth_scroll', $_POST)) ? true : false;		

	$footnotes_options['pre_backlink'] = $_POST['pre_backlink'];
	$footnotes_options['backlink'] = $_POST['backlink'];
	$footnotes_options['post_backlink'] = $_POST['post_backlink'];
	
	$footnotes_options['pre_identifier'] = $_POST['pre_identifier'];
	$footnotes_options['list_style_type'] = $_POST['list_style_type'];
	$footnotes_options['post_identifier'] = $_POST['post_identifier'];

	$footnotes_options['pre_footnotes'] = stripslashes($_POST['pre_footnotes']);
	$footnotes_options['post_footnotes'] = stripslashes($_POST['post_footnotes']);
	$footnotes_options['style_rules'] = stripslashes($_POST['style_rules']);
	
	$footnotes_options['no_display_home'] = (array_key_exists('no_display_home', $_POST)) ? true : false;
	$footnotes_options['no_display_archive'] = (array_key_exists('no_display_archive', $_POST)) ? true : false;
	$footnotes_options['no_display_date'] = (array_key_exists('no_display_date', $_POST)) ? true : false;
	$footnotes_options['no_display_category'] = (array_key_exists('no_display_category', $_POST)) ? true : false;
	$footnotes_options['no_display_search'] = (array_key_exists('no_display_search', $_POST)) ? true : false;
	$footnotes_options['no_display_feed'] = (array_key_exists('no_display_feed', $_POST)) ? true : false;
	
	update_option('swas_footnote_options', $footnotes_options);
}

function swas_upgrade_post($data){
	$data = str_replace('<footnote>',FOOTNOTE_OPEN,$data);
	$data = str_replace('</footnote>',FOOTNOTE_CLOSE,$data);
	return $data;
}

function insert_styles(){
	global $current_settings;
	?>
	<style type="text/css">
		ol.footnotes li{list-style-type:<?php echo $current_settings['list_style_type']; ?>;}
		<?php echo $current_settings['style_rules'];?>
	</style>
	<?php
}

add_filter('the_content', 'swas_footnote');
add_action('admin_menu', 'swas_add_options'); 		// Insert the Admin panel.
add_action('wp_head', 'insert_styles');
if ($current_settings['smooth_scroll']) add_action('wp_head', 'swas_smooth_scroll');
	



if ($_POST['action'] == 'save_options'){
	swas_save_options();
}
?>