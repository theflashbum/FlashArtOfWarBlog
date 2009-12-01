<?php
/*
	Plugin Name: Post Avatar
	Plugin URI: http://www.garinungkadol.com/downloads/post-avatar/
	Description: Attach a picture to posts easily by selecting from a list of uploaded images. Similar to Livejournal Userpics. Developed with <a href="http://wordpress.gaw2006.de">Dominik Menke</a>
	Author: Vicky Arulsingam
	Version: 1.2.2 
	Author URI: http://garinungkadol.com
*/

//! Additions/new comments marked with "//!"
//! Code is poetry: added PHPDoc-Style

/**
 * OPTIONS
 */
$dir = ABSPATH . get_option('gklpa_mydir');
$siteurl = get_settings('siteurl');
$gkl_myAvatarDir = str_replace('/', DIRECTORY_SEPARATOR, $dir); // Updated absolute path to images folder (takes into account Win servers)
$gkl_AvatarURL = trailingslashit($siteurl) . get_option('gklpa_mydir'); // URL to images folder
$gkl_ShowAvatarInPost = get_option('gklpa_showinwritepage'); // Show image in Write Page?
$gkl_ScanRecursive = get_option('gklpa_scanrecursive'); // Recursive scan of the images?
$gkl_ShowInContent = get_option('gklpa_showincontent'); // Show avatar automatically in content?


/**
 * Load Text-Domain
 */
load_plugin_textdomain('gklpa', 'wp-content/plugins/post-avatar/languages/');

if ( isset($_POST['gkl_postavatar_options']) && function_exists('check_admin_referer'))
	check_admin_referer('gkl_postavatar_form');

/**
 * Get list of directory
 *
 * @param string $dir
 * @param boolean $recursive
 * @return array
 */
function gkl_readdir($dir, $recursive = true) {
	global $gkl_myAvatarDir;

	// Cut of the myAvatarDir from the output
	$dir2 = $gkl_myAvatarDir .'/';

	// Init
	$array_items = array();
	$handle = opendir($dir);

	while (false !== ($file = readdir($handle))) {
		// Bad for recursive to scan the current folder again and again and again...
		// ...also bad to scan the parent folder
		if ( $file != '.' && $file != ".." ) {
			// if is_file
			if (!is_dir($dir .'/'. $file)) {
				$file = $dir .'/'. $file;
				// Cut of the myAvatarDir from the output
				$array_items[] = str_replace($dir2, '', $file);
			} else {
				// if (is_dir && recusive scan) scan dir
				if ($recursive) {
					$array_items = array_merge($array_items, gkl_readdir($dir .'/'. $file, $recursive));
				}
				$file = $dir .'/'. $file;
				// Cut of the myAvatarDir from the output
				$array_items[] = str_replace($dir2, '', $file);
			}
		}
	}
	closedir($handle);

	// Limit list to only images
	$array_items = preg_grep('/.jpg$|.jpeg$|.gif$|.png$/', $array_items);
	asort($array_items);

	return $array_items;
}

/**
 * Show form selector in Write Page
 *
 */
function gkl_avatar_insert() {
	global $gkl_myAvatarDir, $gkl_AvatarURL, $gkl_ShowAvatarInPost, $gkl_ScanRecursive;

	// Get current post's avatar
	$post_id = $_GET['post'];
	$CurrAvatar = get_post_meta($post_id, 'postuserpic');
	$selected = ltrim( $CurrAvatar[0], '/' );

	//! Get AvatarList
	if ($gkl_ScanRecursive == 1)
		$recursive = true;
	else
		$recursive = false;
	$AvatarList = gkl_readdir($gkl_myAvatarDir, $recursive);
?>
<div class="dbx-box-wrapper">
	<fieldset id="postuserpic" class="dbx-box">
		<div class="dbx-handle-wrapper">
			<h3 class="dbx-handle"><?php _e('Post Avatar', 'gklpa'); ?></h3>
		</div>
		<div class="dbx-content-wrapper">
			<div class="dbx-content">
				<h3><?php _e('Select an avatar for this post', 'gklpa'); ?></h3>
				<table cellspacing="3" cellpadding="3" width="100%" align="left">
					<tr valign="top">
						<th width="20%"><?php _e('Select', 'gklpa'); ?></th>
						<td width="80%" align="center">
							<select name="postuserpic">
								<option value="no_avatar.png" onclick="chPostAvatar(this)"><?php _e('No Avatar selected', 'gklpa'); ?></option>
<?php
	foreach ($AvatarList as $file) {
		if ($file == 'no_avatar.png')
			continue;

		$checked = ( $file == $selected ) ? ' selected="selected"' : '';
		$oncklick = ( $gkl_ShowAvatarInPost == 1 ) ? ' onclick="chPostAvatar(this)"' : '';
		echo '<option value="/'. $file .'"'. $checked . $oncklick .'>'. $file .'</option>'."\n";
	}
?>
							</select>
						</td>
					</tr>
					<?php
	// Display current avatar in Write Post page
	//! New layout - old one was... ehh... cunfused ;-)
	if ( $gkl_ShowAvatarInPost == 1 ) {
		?><tr>
			<th width="20%" align="center">
				<?php _e('Preview', 'gklpa'); ?>
			</th>
			<td width="80%" align="center">
		<?php

			if ( !empty($CurrAvatar) ) {
				if ( file_exists($gkl_myAvatarDir . $CurrAvatar[0]) ) {
					$CurrAvatarLoc = $gkl_AvatarURL . $CurrAvatar[0];
					echo '<img id="postavatar" src="'. $CurrAvatarLoc .'" alt="Avatar" border="0" />';
				} else {
					echo '<img id="postavatar" src="'. get_settings('siteurl') .'/wp-content/plugins/post-avatar/images/missing_avatar.png" alt="'. __('Avatar Does Not Exist', 'gklpa') .'" border="0" />';
				}
			} else {
				echo '<img id="postavatar" src="'. get_settings('siteurl') .'/wp-content/plugins/post-avatar/images/no_avatar.png" alt="'. __('No Avatar selected', 'gklpa') .'" border="0" />';
			}

		?></td>
		</tr><?php
	}
	?>
				</table>
			</div>
		</div>
	</fieldset>
	<input type="hidden" name="postuserpic-key" id="postuserpic-key" value="<?php echo wp_create_nonce('postuserpic') ; ?>" />
</div>

<?php
}

/**
 * Update post avatar
 *
 * @param integer $postid
 */
function gkl_avatar_edit($postid) {
	global $gkl_myAvatarDir;

	if( !isset($postid) )
		$postid = $_POST['post_ID'];

	// authorization
	if ( !current_user_can('edit_post', $postid) )
		return $postid;
		
	// origination and intention
	if ( !wp_verify_nonce($_POST['postuserpic-key'], 'postuserpic') )
		return $postid;

	$meta_value = $_POST['postuserpic'];
	$CheckAvatar = $gkl_myAvatarDir . $meta_value;

	// Verify avatar exists
	if ( !empty($meta_value) && !file_exists($CheckAvatar) ) {
		unset($meta_value);
	}

	if( isset($meta_value) && !empty($meta_value) && $meta_value != 'no_avatar.png' ) {
		delete_post_meta($postid, 'postuserpic');
		add_post_meta($postid, 'postuserpic', $meta_value);
	} else {
		delete_post_meta($postid, 'postuserpic');
	}
}

/**
 * Display post avatar within The Loop
 *
 * @param string $class
 * @param string $before
 * @param string $after
 */
function gkl_postavatar($class = "", $before = '<div class="postavatar">', $after = '</div>') {
	global $post, $gkl_AvatarURL, $gkl_myAvatarDir;
	$post_id = $post->ID;
	$CurrAvatar = get_post_meta($post_id,'postuserpic');
	$CheckAvatar = $gkl_myAvatarDir . $CurrAvatar[0];

	// Display nothing if value is empty or file does not exist
	if ( empty($CurrAvatar) || !file_exists($CheckAvatar) ) {

	} else {
		// Show post avatar
		$post_title = sanitize_title($post->post_title);

		$CurrAvatarLoc = $gkl_AvatarURL . $CurrAvatar[0];

		if ( $CurrAvatarLoc != $gkl_AvatarURL ) {
			$CurrAvatarLoc = str_replace('/', DIRECTORY_SEPARATOR, $gkl_myAvatarDir . ltrim($CurrAvatar[0],'/'));
			$dim = getimagesize("$CurrAvatarLoc");
			$width = $dim[0];
			$height = $dim[1];
			$CurrAvatarLoc = $gkl_AvatarURL . ltrim($CurrAvatar[0],'/');
			
			if (empty($before) && empty($after) && !empty($class))
				echo '<img class="'. $class .'" src="'. $CurrAvatarLoc .'" style="width:'. $width .'px; height:'. $height .'px; border:none;" alt="'. $post_title. '" />'."\n";
			else
				echo $before .'<img src="'. $CurrAvatarLoc .'" style="width:'. $width .'px; height:'. $height .'px; border:none;" alt="'. $post_title. '" />'. $after ."\n";
		}
	}
}

/**
 * Create Options Page
 *
 */
function postavatar_admin() {
	if ( function_exists('add_options_page') ) {
		add_options_page('Post Avatar Options', 'Post Avatar', 8, basename(__FILE__), 'postavatar_admin_form');
	}
}

/**
 * Manage Options Form
 *
 */
function postavatar_admin_form() {
	// Add default Post Avatar settings
	add_option('gklpa_mydir', 'wp-content/uploads/icons/', 'Location of images folder', 'yes');
	add_option('gklpa_showinwritepage', 1, 'Show image in Write Post Page?', 'yes');
	add_option('gklpa_scanrecursive', 1, 'Recursive scan of the images?', 'yes');
	add_option('gklpa_showincontent', 1, 'Show avatar with post content', 'yes');

	$gklpa_mydir = get_option('gklpa_mydir');
	$gklpa_showinwritepage = get_option('gklpa_showinwritepage');
	$gklpa_scanrecursive = get_option('gklpa_scanrecursive');
	$gklpa_showincontent = get_option('gklpa_showincontent');

	// Update Post Avatar settings
	if ( isset($_POST['submit']) ) {
		$gklpa_mydir = trailingslashit(rtrim($_POST['gklpa_mydir'], '/'));
		$gklpa_showinwritepage = $_POST['gklpa_showinwritepage'];
		$gklpa_scanrecursive = $_POST['gklpa_scanrecursive'];
		$gklpa_showincontent = $_POST['gklpa_showincontent'];

		update_option('gklpa_mydir', $gklpa_mydir);
		update_option('gklpa_showinwritepage', $gklpa_showinwritepage);
		update_option('gklpa_scanrecursive', $gklpa_scanrecursive);
		update_option('gklpa_showincontent', $gklpa_showincontent);
	}
?>
<div class=wrap>
	<h2><?php _e('Post Avatar Options', 'gklpa'); ?></h2>
	<form name="gkl_postavatar" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=gkl-postavatar.php&amp;updated=true">
		<input type="hidden" name="gkl_postavatar_options" value="1" /> <?php if ( function_exists('wp_nonce_field') ) wp_nonce_field('gkl_postavatar_form'); ?>
		<fieldset name="gklpa_mydir">
		<legend><?php _e('Enter Post Avatar options here', 'gklpa') ?></legend>
			<table width="100%" align="center" cellpadding="5" cellspacing="5">
				<tr>
					<td width="30%" align="right" valign="top"><strong><?php _e('Path to Images Folder:', 'gklpa'); ?></strong></td>
					<td width="70%" align="left" valign="top"><input name="gklpa_mydir" type="text" id="gklpa_mydir" value="<?php echo $gklpa_mydir; ?>" size="50" /></td>
				</tr>
				<tr>
					<td width="30%" align="right" valign="top"> </td>
					<td width="70%" align="left" valign="top"><input name="gklpa_showinwritepage" type="checkbox" id="gklpa_showinwritepage" value="1" <?php checked('1', get_settings('gklpa_showinwritepage')); ?> /> <label for="gklpa_showinwritepage"><?php _e('Show image in Write Post Page?', 'gklpa'); ?></label></td>
				</tr>
				<tr>
					<td width="30%" align="right" valign="top"> </td>
					<td width="70%" align="left" valign="top"><input name="gklpa_scanrecursive" type="checkbox" id="gklpa_scanrecursive" value="1" <?php checked('1', get_settings('gklpa_scanrecursive')); ?> /> <label for="gklpa_scanrecursive"><?php _e('Scan the images directory and its sub-directories?', 'gklpa'); ?></label></td>
				</tr>
				<tr>
					<td width="30%" align="right" valign="top"> </td>
					<td width="70%" align="left" valign="top"><input name="gklpa_showincontent" type="checkbox" id="gklpa_showincontent" value="1" <?php checked('1', get_settings('gklpa_showincontent')); ?> /> <label for="gklpa_showintemplate"><?php _e('Show avatar in post? Disable to use template tag', 'gklpa'); ?></label></td>
				</tr>
			</table>
		</fieldset>
		<div class="submit"><input type="submit" name="submit" value="<?php _e('Update Options') ?> &raquo;" /></div>
	</form>
</div><?php
}

/**
 * Checks, whether one of two strings are substrings of PHP_SELF
 *
 * @return boolean
 */
function gkl_check_phpself() {
	if (substr_count($_SERVER['PHP_SELF'], '/wp-admin/post.php') == 1 
		|| substr_count($_SERVER['PHP_SELF'], '/wp-admin/page.php') == 1 
		|| substr_count($_SERVER['PHP_SELF'], '/wp-admin/page-new.php') == 1 || substr_count($_SERVER['PHP_SELF'], '/wp-admin/post-new.php') == 1 
		|| substr_count($_SERVER['PHP_SELF'], '/wp-admin/edit.php') == 1)
		return true;
	else
		return false;
}

/**
 * Prints js- and css-code in the admin-head-area
 *
 */
function gkl_admin_head() {
	global $gkl_AvatarURL, $gkl_ShowAvatarInPost, $siteurl;

	//! $_SERVER['PHP_SELF'] - only works if installed in root of domain/subdomain. If installed in sub-folder - does not work because $_SERVER['PHP_SELF'] shows up as /foldername/wp-admin/post.php;
	//! Fix: check if PHP_SELF contains substring
	if ( $gkl_ShowAvatarInPost == 1 && gkl_check_phpself() ) {

	//! Created external scriptfile -> so it's easier to extend the script, e.g. with slideshow-effects
?><script type="text/javascript">
//<![CDATA[
var gkl_site = "<?php echo $siteurl; ?>";
var gkl_avatar = "<?php echo $gkl_AvatarURL; ?>";
//]]>
</script>
<script type="text/javascript" src="../wp-content/plugins/post-avatar/head/gkl-postavatar.js"></script>
<?php
	}

	//! Lets design the module a little bit, let it look like the other modules
	if ( gkl_check_phpself() )
		gkl_postavatar_showcss();
}

/*
 * Display css where needed
 */
function gkl_postavatar_showcss() {
	echo '<link rel="stylesheet" type="text/css" href="'. get_option('siteurl') . '/wp-content/plugins/post-avatar/head/gkl-postavatar.css" />';
}


/*
 * Filter to include post avatar in the_content()
 *
 * @param text $content
 * @return text $content
 */
function gkl_postavatar_filter($content) {
	global $post, $gkl_AvatarURL, $gkl_myAvatarDir;

	gkl_postavatar();
	
	return $content;
}

// Hook it up
add_action('admin_menu', 'postavatar_admin');
add_action('simple_edit_form', 'gkl_avatar_insert');
add_action('edit_form_advanced','gkl_avatar_insert');
add_action('edit_page_form','gkl_avatar_insert');
add_action('edit_post', 'gkl_avatar_edit');
add_action('save_post', 'gkl_avatar_edit');
add_action('publish_post', 'gkl_avatar_edit');
add_action('admin_head', 'gkl_admin_head');

// Display avatar without template tag
if ($gkl_ShowInContent == 1){
	add_filter('the_content', 'gkl_postavatar_filter');
	add_filter('wp_head', 'gkl_postavatar_showcss');
}
?>