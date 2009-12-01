<?php
/*
Plugin Name: WP lightbox 2
Plugin URI: http://zeo.unic.net.my/notes/lightbox2-for-wordpress/
Description: <a href="http://www.huddletogether.com/projects/lightbox2/">Lightbox JS v2</a> is a simple, unobtrusive script used to to overlay images on the current page written by Lokesh Dhakar. Add rel="lightbox" attribute to any link tag to activate the lightbox. This plugin integrate its feature into your WordPress blog.
Version: 0.6.3
Author: Safirul Alredha
Author URI: http://zeo.unic.net.my/
License: GPL
*/

define("IMAGE_FILETYPE", "(bmp|gif|jpeg|jpg|png)", true);

function wp_lightbox2_javascript() {
	if ( !function_exists('wp_enqueue_script') || is_admin() )
		return;
	wp_enqueue_script('prototype');
	wp_enqueue_script('scriptaculous-effects');
}

add_action('init', 'wp_lightbox2_javascript');

function wp_lightbox2_init() {
	$url = get_bloginfo('wpurl');
?>
	<!-- WP lightbox 2 Plugin version 0.6.3 -->
	<link rel="stylesheet" href="<?php echo $url; ?>/wp-content/plugins/wp-lightbox2/css/lightbox.css" type="text/css" media="screen" />
	<style type="text/css" media="screen">
		#prevLink, #nextLink { background: transparent url("<?php echo $url; ?>/wp-content/plugins/wp-lightbox2/images/blank.gif") no-repeat; }
		#prevLink:hover, #prevLink:visited:hover { background: url("<?php echo $url; ?>/wp-content/plugins/wp-lightbox2/images/prevlabel.gif") left 15% no-repeat; }
		#nextLink:hover, #nextLink:visited:hover { background: url("<?php echo $url; ?>/wp-content/plugins/wp-lightbox2/images/nextlabel.gif") right 15% no-repeat; }
	</style>
	<script type="text/javascript" src="<?php echo $url; ?>/wp-content/plugins/wp-lightbox2/js/lightbox.js"></script>
	<script type="text/javascript">
		var fileLoadingImage = "<?php echo $url; ?>/wp-content/plugins/wp-lightbox2/images/loading.gif";
		var fileBottomNavCloseImage = "<?php echo $url; ?>/wp-content/plugins/wp-lightbox2/images/closelabel.gif";
	</script>
<?php }

add_action('wp_head', 'wp_lightbox2_init');

function wp_lightbox2_replace($string) {
	$pattern = '/(<a(.*?)href="([^"]*.)'.IMAGE_FILETYPE.'"(.*?)><img)/ie';
  	$replacement = 'stripslashes(strstr("\2\5","rel=") ? "\1" : "<a\2href=\"\3\4\"\5 rel=\"lightbox\"><img")';
	return preg_replace($pattern, $replacement, $string);
}

add_filter('the_content', 'wp_lightbox2_replace');

function wp_lightbox2_add_quicktag() {
	if (strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php') || strpos($_SERVER['REQUEST_URI'], 'page.php')) {
?>
<script type="text/javascript">//<![CDATA[
	var toolbar = document.getElementById("ed_toolbar");
<?php
	edit_insert_button("lightbox 2", "wp_lightbox2_handler", "Lightbox 2 with Caption");
?>
	var state_my_button = true;

function wp_lightbox2_handler() {
	if (state_my_button) {
		var myURL = prompt('Enter the original image URL (required)', 'http://');
		var myCaption = prompt('Enter image caption');
		var myIMG = prompt('Enter the Image thumbnail (required)', 'http://');
		var myWidth = prompt('Enter Width of image thumbnail (required)');
		var myHeight = prompt('Enter Height of image thumbnail (required)');
		var myAlt = prompt('Enter a description of the image');
		var mySet = prompt('Insert group name for image set (optional)', '[groupname]');
		if (myURL && myIMG && myWidth && myHeight) {
			myValue = '<a href="'+myURL+'" rel="lightbox'+mySet+'" title="'+myCaption+'"><img src="'+myIMG+'" width="'+myWidth+'" height="'+myHeight+'" alt="'+myAlt+'" /></a>';
			edInsertContent(edCanvas, myValue); 
		}
	}
}
//]]></script>

<?php } }

if (!function_exists('edit_insert_button')) {
	//edit_insert_button: Inserts a button into the editor
	function edit_insert_button($caption, $js_onclick, $title = '')	{
	?>
	if (toolbar) {
		var theButton = document.createElement('input');
		theButton.type = 'button';
		theButton.value = '<?php echo $caption; ?>';
		theButton.onclick = <?php echo $js_onclick; ?>;
		theButton.className = 'ed_button';
		theButton.title = "<?php echo $title; ?>";
		theButton.id = "<?php echo "ed_{$caption}"; ?>";
		toolbar.appendChild(theButton);
	}
	
<?php } }

add_filter('admin_footer', 'wp_lightbox2_add_quicktag');

?>
