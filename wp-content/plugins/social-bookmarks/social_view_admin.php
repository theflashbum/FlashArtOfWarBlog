<?php
/*
 * social_view_admin.php
 *
 * The Back-end interface of the application
 */
 
include_once('social_object.php');
include_once('social_db.php');

class social_view_admin extends social_object
{

	// The user's settings
	var $current_settings;
	
	// Social Sites
	var $social_places;
	
	// An array that holds the names of the admin pages
	var $admin_pages;
	
	function social_view_admin(&$current_settings, &$sites)
	{
 		parent::social_object();
		
		$this->current_settings = $current_settings;

		$this->social_places = $sites;

		$this->admin_pages = array('social-bookmarks/social_view_admin.php', 
									'social_general', 
									'social_sites',
									'social_about');
		
		$this->attach_view();
	}
	
	function attach_view()
	{
		if($this->platform['type'] == 'WP' and function_exists(add_action))
		{
			// Adds the CSS to the header
			add_action('admin_head', array(&$this, 'include_header'));
			
			// Add Admin Menu
			add_action('admin_menu', array(&$this, 'admin_menu'));
		}
	}
	
	// Manage Admin Options
	function admin_menu()
	{
		if($this->platform['type'] == 'WP')
		{
	
			// Top-level menu
			add_menu_page('Social Bookmarks', 'Social', 8, __FILE__, array(&$this, 'render_admin_home'));
			
			// Menu Items

			// Add the home submenu
//			add_submenu_page(__FILE__, 'Social Bookmarks - Home', 'Home', 8,  'social_home', array(&$this, 'render_admin_home'));
		
			// Add the General options submenu
			add_submenu_page(__FILE__, 'Social Bookmarks - Options', 'Options', 8,  $this->admin_pages[1], array(&$this, 'options_group_general'));

			// Add the sites submenu
			add_submenu_page(__FILE__, 'Social Bookmarks - Bookmarks', 'Bookmarks', 8,  $this->admin_pages[2], array(&$this, 'options_group_sites'));

			// Add the debug submenu
//			add_submenu_page(__FILE__, 'Social Bookmarks - Info', 'Info', 8,  'social_debug', array(&$this, 'debug_section'));

			// Add the debug submenu
			add_submenu_page(__FILE__, 'Social Bookmarks - About', 'About', 8, $this->admin_pages[3], array(&$this, 'render_admin_about'));

			if(in_array($_REQUEST['page'], $this->admin_pages))
			{
				wp_enqueue_script('jquery-ui-draggable');
				wp_enqueue_script('postbox');
				wp_enqueue_script('dashboard');
			}
		}    	
/*
		// Add admin page to the Options Tab of the admin section
		if($this->platform['type'] == 'WP' and function_exists(add_options_page))
		{
			add_options_page('Social Bookmarks Options', 'Social Bookmarks', 8, __FILE__, array(&$this, 'plugin_options'));
		}
*/
	}	

	function include_header()
	{
		if(in_array($_REQUEST['page'], $this->admin_pages))
		{
			wp_admin_css('css/dashboard');
		}
	}
	
	// Render the home page for the admin section
	function render_admin_home()
	{
		$my_domain = 'social-bookmarks';

		if (is_admin ()) 
		{
			if( function_exists( 'add_meta_box' )) 
			{
				add_meta_box( 'social-bookmarks_welcome', __( 'Welcome to Social Bookmarks', $my_domain ), array(&$this,'welcome_contents'), $my_domain);
				add_meta_box( 'social-bookmarks_packs', __( 'Site Packs', $my_domain ), array(&$this,'site_packs_contents'), $my_domain);
				add_meta_box( 'social-bookmarks_rss', __( 'Latest News', $my_domain ), array(&$this,'rss_contents'), $my_domain);
			}
		}		

		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

		print('<div class="wrap">');
		print('<h2>Social Bookmarks</h2>');
		print('<div id="dashboard-widgets-wrap">');
		print('<div id=\'dashboard-widgets\' class=\'metabox-holder\'>');
		do_meta_boxes($my_domain,'advanced', null);
		print('</div></div></div>');		
	}

	function welcome_contents()
	{
		print("<p>In this section you configure the <em>Social Bookmarks</em> plugin. 
		Under the <em><a href=\"admin.php?page=social_general\">Options</a></em> section, you will find all the plugin options that you customize to match your site needs.
		In the <em><a href=\"admin.php?page=social_sites\">Bookmarks</a></em> section, you can enable and disable the sites that will appear on your posts/pages.</p>");
		
		print("<p><ul>");
		print("<li><a href=\"http://www.dountsis.com/projects/social-bookmarks\">Plugin Site</a> - The home of the plugin.<li>");
		print("<li><a href=\"http://bugtracker.dountsis.com/\">Bugtracker</a> - If you have spotted a problem with the plugin or have an idea for a new feature, then please submit it in this site and do not forget to include your WordPress version, server operating system and description of your issue.<li>");
		print("</ul></p>");
	}
	
	function site_packs_contents()
	{
		print("<p><em>Site Packs</em> are collection of social bookmarking sites that can be added to the <em>Social Bookmarks</em> plugin.");
		print(" You can download <em>Site Packs</em> from the <a href=\"http://www.dountsis.com/downloads?cat=2\" title=\"Sites Pack at Dountsis.com\">Social Bookmarks repository</a>.</p>");
		print("<p>Finally, you can create your own <em>Site Packs</em> if you want to. Simply download the <a href=\"http://www.dountsis.com/downloads?cat=2\" title=\"Template Pack at Dountsis.com\">Template Pack</a> and follow the simple instructions provided in the pack.</p>");
	}

	function rss_contents()
	{
		require_once(ABSPATH . WPINC . '/rss.php');
		$rss = @fetch_rss('http://www.dountsis.com/feed');
		
		if ( isset($rss->items) && 0 != count($rss->items) )
		{
			$rss->items = array_slice($rss->items, 0, 4);
			print("<ul>");
			foreach ($rss->items as $item)
			{
			 	print('<li><a class="rsswidget" title="" href="'. wp_filter_kses($item['link']) .'">'. wp_specialchars($item['title']) .'</a>');
		  		print('<span class="rss-date">&nbsp;'. date("F jS, Y", strtotime($item['pubdate'])).'</span>'); 
				print('<div class="rssSummary"><strong>'. human_time_diff(strtotime($item['pubdate'], time())) .' ago</strong> - '. $item['description'] .'</div></li>');
			}
			print("</ul>");
		}
	}
	
	// Render the home page for the admin section
	function render_admin_about()
	{
		$my_domain = 'social-bookmarks';

		if (is_admin ()) 
		{
			if( function_exists( 'add_meta_box' )) 
			{
				add_meta_box( 'social-bookmarks_about', __( 'About this plugin', $my_domain ), array(&$this,'about_contents'), $my_domain);
				add_meta_box( 'social-bookmarks_faq', __( 'Frequently Asked Questions (FAQ)', $my_domain ), array(&$this,'faq_contents'), $my_domain);

			}
		}		

		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

		print('<div class="wrap">');
		print('<h2>Social Bookmarks - About</h2>');
		print('<div id="dashboard-widgets-wrap">');
		print('<div id=\'dashboard-widgets\' class=\'metabox-holder\'>');
		do_meta_boxes($my_domain,'advanced', null);
		print('</div></div></div>');
	}
	
	function about_contents()
	{
		$html .= file_get_contents($this->location_url.'social_about.html');
		
		print($html);	
	}

	function faq_contents()
	{
		$html .= file_get_contents($this->location_url.'social_faq.html');
		
		print($html);	
	}
	
	// Admin page
	function plugin_options()
	{
		$html = '<div class="wrap">';
		$html .= '<h2>Social Bookmarks</h2>';
		
  		// General Plugin Options
  		$this->options_group_general();
		
  		// Sites Option
 		$this->options_group_sites();
  		
  		// Debug Screen
// 		$this->debug_section();
		
		$html .= '</div>';
		
		print($html);
	}

	function options_group_general()
	{
		$my_domain = 'social-bookmarks';

		if (is_admin ()) 
		{
			if( function_exists( 'add_meta_box' )) 
			{
				add_meta_box( 'social-bookmarks_options', __( 'Options', $my_domain ), array(&$this,'options_contents'), $my_domain);
			}
		}		

		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

		print('<div class="wrap">');
		print('<h2>Social Bookmarks - Options</h2>');
		print('<div id="dashboard-widgets-wrap">');
		print('<div id=\'dashboard-widgets\' class=\'metabox-holder\'>');
		do_meta_boxes($my_domain,'advanced', null);
		print('</div></div></div>');		
	}

	function options_contents()
	{
   		$html = '<form style="padding-left:25px;" method="post">';

		$html .= $this->option_open_links_target('sbb_target');
	
		// Label Title
		$html .= $this->option_title_text('sbb_label');
		
		// Tooltip label text
		$html .= $this->option_tooltip_text('sbb_tooltip_text');
				
		// Number of Lines		
		$html .= $this->option_lines_number('sbb_lines');
		
		// Where to display the application
		$html .= $this->option_display_places('sbb_display');
		
		// Exclude these pages
		$html .= $this->option_exclude_pages('sbb_pages_excluded');
		
		// Enable AJAX front-end interface
		$html .= $this->option_ajax_dropdown_enable('sbb_ajax_dropdown_enabled');

		// Show or Hide the Site on page load (front-end)
		$html .= $this->option_ajax_dropdown_fullsize('sbb_ajax_dropdown_hide');
		
		// Speed of the AJAX dropdown
		$html .= $this->option_ajax_dropdown_speed('sbb_ajax_dropdown_speed');		
		
		// Save General options	
		// Hidden var to assist identfying the form POST
		$html .= '<input type="hidden" name="sbb_general" value="sites" />';
		$html .= '<p class="submit"><input type="submit" class="button-primary" value="Update Options &raquo;"></p>';
		$html .= '</form>';
		$html .= '</fieldset>';
		$html .= '</div>';
		
		print($html);	
	}
	
	function options_group_sites()
	{
		$my_domain = 'social-bookmarks';

		if (is_admin ()) 
		{
			if( function_exists( 'add_meta_box' )) 
			{
				add_meta_box( 'social-bookmarks_sites', __( 'Sites', $my_domain ), array(&$this,'sites_contents'), $my_domain);
			}
		}		

		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

		print('<div class="wrap">');
		print('<h2>Social Bookmarks - Sites</h2>');
		print('<div id="dashboard-widgets-wrap">');
		print('<div id=\'dashboard-widgets\' class=\'metabox-holder\'>');
		do_meta_boxes($my_domain,'advanced', null);
		print('</div></div></div>');
	}
	
	function sites_contents()
	{
		$html = '<p>Select the social bookmarking sites that you want to display on your site:</p>';
		$html .= '<form id="sites" style="padding-left:25px;" method="post">';	
	
		$user_option = explode('|', $this->current_settings['sbb_sites']);
		$i = 0;
		$html_left = $html_right = '';
		foreach($this->social_places as $site => $settings)
		{
			$site_name = $settings['name'];

			$img_src = $this->location_url."images/".$settings['img'];

			$site_img = "<img src=\"$img_src\" title=\"$site_name\" alt=\"$site_name\" />\n"; // XHTML Compliance (removed align)

			$html_sites = "<p>\n";
			if(in_array($site, $user_option))
			{
				$html_sites .= "<input type=\"checkbox\" name=\"$site\" value=\"Y\" checked />\n";
			}
			else
			{
				$html_sites .= "<input type=\"checkbox\" name=\"$site\" value=\"Y\" />\n";
			}
			$html_sites .= " $site_img <em>$site_name</em>";		
			$html_sites .= "</p>\n";

			if(social_common::is_even($i))
			{
				$html_left .= $html_sites;
			}
			else
			{
				$html_right .= $html_sites;
			}

			$i++;
		}
		$html .= '<div style="float:left;width:50%;">';
		$html .= $html_left;
		$html .= '</div>';

		$html .= '<div style="float:right;width:50%;">';
		$html .= $html_right;
		$html .= '</div>';

		// Hidden var to assist identfying the form POST
		$html .= '<p>&nbsp;</p>';
		$html .= '<input type="hidden" name="sbb_sites" value="sbb_sites" />';
		$html .= '<p class="submit"><input type="submit" class="button-primary" value="Update Options  &raquo;"></p>';
		$html .= '</form>';
		$html .= '</div>';
		$html .= '</fieldset>';
		
		print($html);
	}

	function debug_section()
	{
		$my_domain = 'social-bookmarks';

		if (is_admin ()) 
		{
			if( function_exists( 'add_meta_box' )) 
			{
				add_meta_box( 'social-bookmarks_debug', __( 'Info', $my_domain ), array(&$this,'debug_contents'), $my_domain);
			}
		}		

		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

		print('<div class="wrap">');
		print('<h2>Social Bookmarks - Info</h2>');
		print('<div id="dashboard-widgets-wrap">');
		print('<div id=\'dashboard-widgets\' class=\'metabox-holder\'>');
		do_meta_boxes($my_domain,'advanced', null);
		print('</div></div></div>');
	}
	
	function debug_contents()
	{	
    	$html .= '<h4>Database Settings</h4>';
		foreach($this->current_settings as $key => $value)
		{
			$html .= "<strong>$key:</strong> $value <br />";
		}
    	
		$html .= "<br/><h4>Locations</h4>";
		$html .= 'Directory: '.dirname(__FILE__);
		$html .= '<br /> URL: '. $this->location_url;
/*		
    	if($_POST)
    	{
			print('<p>POST Array</p>');
			print('<pre>');
			print_r($_POST);
			print('<pre>');
    	}
*/
  /*  	
    	print("<p>Sites</p>");
    	print('<pre>');
    	print_r($this->social_places);
    	print('<pre>');    	
 */
    	$html .= '</div>';
    	
    	print($html);
	}
	
	/**
	 * Drop-down list that allows a selection so
	 * that the links will open in a new window or 
	 * on the current one.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */
	function option_open_links_target($option_value)
	{
		// Site _target
		$html = "<p>";
		$html .= "Open links in ";
		$html .= "<select name=\"$option_value\" >\n";
		if($this->current_settings[$option_value] == 'current')
		{
			$html .= "<option value=\"current\" selected>current</option>\n";
			$html .= "<option value=\"new\">new</option>\n";
		}
		else
		{
			$html .= "<option value=\"current\">current</option>\n";
			$html .= "<option value=\"new\" selected>new</option>\n";
		}
		$html .= "</select>\n";
		$html .= " window.\n";
		$html .= "</p>";
		
		return $html;
	}

	/**
	 * Textfield for the label displayed on the front-end.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */	
	function option_title_text($option_value)
	{
		$html = '<p>';
		$html .= 'Display Title: <input type="text" name="'.$option_value.'" value="'.$this->current_settings[$option_value].'" />';
		$html .= '</p>';
		
		return $html;
	}

	/**
	 * Textfield for the tooltip displayed on the front-end.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */	
	function option_tooltip_text($option_value)
	{
		$html = '<p>';
		$html .= 'Tooltip Title: <input type="text" name="'.$option_value.'" value="'.$this->current_settings[$option_value].'" />';
		$html .= '</p>';
		
		return $html;
	}

	/**
	 * Drop-down list for the number of link rows.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */
	function option_lines_number($option_value)
	{
		$html = "<p>";
		$html .= "Display links in ";
		$html .= "<select name=\"$option_value\" >\n";
		switch($this->current_settings[$option_value])
		{
			case 1:
				$html .= "<option value=\"1\" selected>1</option>\n";
				$html .= "<option value=\"2\">2</option>\n";
				$html .= "<option value=\"3\">3</option>\n";
				$html .= "<option value=\"4\">4</option>\n";
				$html .= "<option value=\"5\">5</option>\n";
				break;
			case 2:
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"2\" selected>2</option>\n";
				$html .= "<option value=\"3\">3</option>\n";
				$html .= "<option value=\"4\">4</option>\n";
				$html .= "<option value=\"5\">5</option>\n";
				break;
			case 3:
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"2\">2</option>\n";
				$html .= "<option value=\"3\" selected>3</option>\n";
				$html .= "<option value=\"4\">4</option>\n";
				$html .= "<option value=\"5\">5</option>\n";
				break;
			case 4:
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"2\">2</option>\n";
				$html .= "<option value=\"3\">3</option>\n";
				$html .= "<option value=\"4\" selected>4</option>\n";
				$html .= "<option value=\"5\">5</option>\n";
				break;
			case 5:
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"2\">2</option>\n";
				$html .= "<option value=\"3\">3</option>\n";
				$html .= "<option value=\"4\">4</option>\n";
				$html .= "<option value=\"5\" selected>5</option>\n";
				break;
			default:
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"2\" selected>2</option>\n";
				$html .= "<option value=\"3\">3</option>\n";
				$html .= "<option value=\"4\">4</option>\n";
				$html .= "<option value=\"5\">5</option>\n";
				break;
		}

		$html .= "</select>\n";
		$html .= " line(s).\n";
		$html .= "</p>";
		
		return $html;
	}
	
	/**
	 * Drop-down list on where to display the application.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */	
	function option_display_places($option_value)
	{
		// Position the plugin in the blog
		// possible options: 1:index.php, 
		// 					2: single page only 
		//					3: Single & index.php 
		//					4: Nowhere (useful for custom display using render_social() )
		//					5: Only in pages (not posts)
		
		$html = "<p>";
		$html .= "Display the plugin ";
		$html .= "<select name=\"$option_value\" >\n";
		switch($this->current_settings[$option_value])
		{
			case 1:
				$html .= "<option value=\"1\" selected>in the blog listing (index.php)</option>\n";
				$html .= "<option value=\"2\">when a single post is viewed</option>\n";
				$html .= "<option value=\"3\">in both single post &amp; blog listing</option>\n";
				$html .= "<option value=\"5\">only in pages</option>\n";
				$html .= "<option value=\"4\">nowhere</option>\n";
				break;
			case 2:
				$html .= "<option value=\"1\">in the blog listing (index.php)</option>\n";
				$html .= "<option value=\"2\" selected>when a single post is viewed</option>\n";
				$html .= "<option value=\"3\">in both single post &amp; blog listing</option>\n";
				$html .= "<option value=\"5\">only in pages</option>\n";
				$html .= "<option value=\"4\">nowhere</option>\n";
				break;
			case 3:
				$html .= "<option value=\"1\">in the blog listing (index.php)</option>\n";
				$html .= "<option value=\"2\">when a single post is viewed</option>\n";
				$html .= "<option value=\"3\" selected>in both single post &amp; blog listing</option>\n";
				$html .= "<option value=\"5\">only in pages</option>\n";
				$html .= "<option value=\"4\">nowhere</option>\n";
				break;
			case 4:
				$html .= "<option value=\"1\">in the blog listing (index.php)</option>\n";
				$html .= "<option value=\"2\">when a single post is viewed</option>\n";
				$html .= "<option value=\"3\">in both single post &amp; blog listing</option>\n";
				$html .= "<option value=\"5\">only in pages</option>\n";
				$html .= "<option value=\"4\" selected>nowhere</option>\n";
				break;
			case 5:
				$html .= "<option value=\"1\">in the blog listing (index.php)</option>\n";
				$html .= "<option value=\"2\">when a single post is viewed</option>\n";
				$html .= "<option value=\"3\">in both single post &amp; blog listing</option>\n";
				$html .= "<option value=\"5\" selected>only in pages</option>\n";
				$html .= "<option value=\"4\">nowhere</option>\n";
				break;
			default:
				$html .= "<option value=\"1\">in the blog listing (index.php)</option>\n";
				$html .= "<option value=\"2\" selected>when a single post is viewed</option>\n";
				$html .= "<option value=\"3\">in both single post &amp; blog listing</option>\n";
				$html .= "<option value=\"5\">only in pages</option>\n";
				$html .= "<option value=\"4\">nowhere</option>\n";
				break;
		}

		$html .= "</select>\n";
		$html .= ".\n";
		$html .= "</p>";
		
		return $html;
	}

	/**
	 * List of the pages to be excluded from the use of the application.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */	
	function option_exclude_pages($option_value)
	{
		$db =& new social_db();
		
		$html = '<p>Do not display the links on the selected pages below:</p>';
		$html .='<select id="'.$option_value.'" name="sbb_pages_excluded[]" size="2" style="height: 100px;" multiple="true">';
		
		$site_pages = $db->get_pages();
		$exclude_selected = $this->current_settings[$option_value];

		if($site_pages)
		{
			foreach($site_pages as $page)
			{
				$s = '';
				if($exclude_selected and $exclude_selected != 'none')
				{
					if(in_array($page['id'], $exclude_selected))
					{
						$s = 'selected';
					}
				}
				$html .= "<option name=\"page_{$page['id']}\" value=\"{$page['id']}\" $s>{$page['post_title']}</option>\n";
			}
		}
		$html .= '</select>';
		$html .= '<ul>';
		$html .= '<li>Hold Ctrl to select more than one page.</li>';
		$html .= '<li>Hold Shift to select a region of pages.</li>';
		$html .= '<li>If you have selected more than one page<br /> then hold the Ctrl key to de-select individual pages.</li>';
		$html .= '<li>In OS X, use the Apple key instead of Ctrl<br /> to perform the described above operations.</li>';
		$html .= '</ul>';
		
		return $html;
	}

	/**
	 * Drop-down list to enable/disable 
	 * the use of the AJAX interface.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */		
	function option_ajax_dropdown_enable($option_value)
	{
		$html = "<p>";
		$html .= "<select name=\"$option_value\" >\n";
		if($this->current_settings[$option_value] == 'Y')
		{
			$html .= "<option value=\"Y\" selected>Use</option>\n";
			$html .= "<option value=\"N\">Do not use</option>\n";
		}
		else
		{
			$html .= "<option value=\"Y\">Use</option>\n";
			$html .= "<option value=\"N\" selected>Do not use</option>\n";
		}
		$html .= "</select>\n";
		$html .= " fancy drop-down AJAX interface.\n";
		$html .= "</p>";
		
		return $html;
	}

	/**
	 * Drop-down list to display on page load 
	 * the application with the sites hidden 
	 * (when using AJAX interface).
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */	
	function option_ajax_dropdown_fullsize($option_value)
	{
		$html = "<p>";
		$html .= "<select name=\"$option_value\" >\n";
		if($this->current_settings[$option_value] == 'Y')
		{
			$html .= "<option value=\"Y\" selected>Hide</option>\n";
			$html .= "<option value=\"N\">Show</option>\n";
		}
		else
		{
			$html .= "<option value=\"Y\">Hide</option>\n";
			$html .= "<option value=\"N\" selected>Show</option>\n";
		}
		$html .= "</select>\n";
		$html .= " the sites when the page loads (requires AJAX interface to be enabled).\n";
		$html .= "</p>";
		
		return $html;
	}
	
	/**
	 * Drop-down list for the number of link rows.
	 *
	 * Part of the General Options
	 *
	 * @return string
	 */
	function option_ajax_dropdown_speed($option_value)
	{
		$html = "<p>";
		$html .= "Set the drop-down speed to ";
		$html .= "<select name=\"$option_value\" >\n";
		
		switch($this->current_settings[$option_value])
		{
			case 0.5:
				$html .= "<option value=\"0.5\" selected>0.5</option>\n";
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"1.5\">1.5</option>\n";
				break;
			case 1:
				$html .= "<option value=\"0.5\">0.5</option>\n";
				$html .= "<option value=\"1\" selected>1</option>\n";
				$html .= "<option value=\"1.5\">1.5</option>\n";
				break;
			case 1.5:
				$html .= "<option value=\"0.5\">0.5</option>\n";
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"1.5\" selected>1.5</option>\n";	
				break;
			default:
				$html .= "<option value=\"0.5\">0.5</option>\n";
				$html .= "<option value=\"1\">1</option>\n";
				$html .= "<option value=\"1.5\" selected>1.5</option>\n";
				break;
		}

		$html .= "</select>\n";
		$html .= " second(s).\n";
		$html .= "</p>";
		
		return $html;
	}
}
?>
