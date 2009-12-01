<?php
/*
 * social_view_public.php
 *
 * The Front-end interface of the application
 */
 
include_once('social_object.php');
include_once('social_db.php');

class social_view_public extends social_object
{
	// The user's settings
	var $current_settings;
	
	// Social Sites
	var $social_places;
		
	function social_view_public(&$current_settings, &$sites)
	{
 		parent::social_object();
		
		$this->current_settings = $current_settings;

		$this->social_places = $sites;

		$this->attach_view();
		
		// Widget
//		$this->attach_widget();
	}
	
	function attach_widget()
	{
		if($this->platform['type'] == 'WP' and function_exists(add_action))
		{
			// Delays plugin execution until Dynamic Sidebar has loaded first.
			add_action('plugins_loaded', array(&$this, 'widget_sb_init'));
		}
		
		if($this->platform['type'] == 'WP' and function_exists(wp_register_sidebar_widget))
		{
			// This registers the widget..
			wp_register_sidebar_widget('Social Bookmarks', 'widget_sb', $this);
		}
		else
		{
//			print('Cannot register the sidebar widget');
		}
	}
	
	/**
	 * Attach the current view to the platform
	 *
	 * @return void
	 */
	function attach_view()
	{
		if($this->platform['type'] == 'WP' and function_exists(add_action))
		{
			// Adds the CSS to the header
			add_action('wp_head', array(&$this, 'include_header'));
		
			// Attach the application to the platform
			if($this->current_settings['sbb_display'] != 4)
			{
				$this->set_public_filters();
			}
		}
	}
	
	function set_public_filters()
	{
		if($this->platform['type'] == 'WP' and function_exists(add_filter))
		{
			// Display render_view() in the_content
			add_filter('the_content', array(&$this, 'render_view'), 999);
		
			// Display render_view() in the_excerpt
			add_filter('the_excerpt', array(&$this, 'render_view'), 999);
		}
	}

	/**
	 * Custom page header inclusion
	 *
	 * @return void
	 */	
	function include_header()
	{
		// Include the plugin stylesheet
		print("<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".$this->location_url."social_bookmarks.css\" />\n");
		
		// Include script.aculo.us library
		if($this->current_settings['sbb_ajax_dropdown_enabled'] == 'Y')
		{
			print("<script src=\"".$this->location_url."lib/script.aculo.us/prototype.js\" type=\"text/javascript\"></script>\n");
			print("<script src=\"".$this->location_url."lib/script.aculo.us/scriptaculous.js\" type=\"text/javascript\"></script>\n");
		}


/*		
		// Include niftyCube library
		if($this->current_settings['sbb_round_box_enabled'] == 'Y')
		{
            print("<link rel=\"stylesheet\" type=\"text/css\" href=\"".$this->location_url."lib/niftyCube/niftyCorners.css\" />\n");
            print("<script type=\"text/javascript\" src=\"".$this->location_url."lib/niftyCube/niftycube.js\"></script>\n");
            print("<script type=\"text/javascript\">\n window.onload=function()\n{Nifty(\"div#box\",\"transparent\");\n}\n</script>\n");
            print("<link rel=\"stylesheet\" type=\"text/css\" href=\"".$this->location_url."lib/niftyCube/niftyPrint.css\" media=\"print\" />");
		}
*/

	}

	/**
	 * Renders the view
	 *
	 * @return string
	 */
	function render_view($content)
	{
		global $id;					// WP variable
		$html = '';
		
		$db =& new social_db();
		
		if(is_array($this->current_settings['sbb_pages_excluded']))
		{
			if(!in_array($id, $this->current_settings['sbb_pages_excluded']))
			{		
				$html .= $this->render_plugin();
			}
		}
		else
		{
			$html .= $this->render_plugin();
		}
		
		if(substr($content, 0, 12) == '<p>Apostolos')
		{
			return($content);
		}
		else
		{
			switch($this->current_settings['sbb_display'])
			{
				case 1:
					// Only in blog listing (not single page)
					if($db->is_single())			// WP Function
					{
						return($content);
					}
					else
					{
						return($content.$html);
					}
					break;
				case 2: 
					// Single Page
					if($db->is_page($id))
					{
						return($content.$html);
					}
					elseif(!$db->is_single())	// WP Function
					{
						return($content);
					}
					else
					{
						return($content.$html);
					}
					break;
				case 3: 
					// Single & index.php 
					return($content.$html);
					break;
				case 4:
					// Nowhere (useful for custom display using render_social() )
					return($content);
					break;
				case 5:
					// Only in pages (not posts)
					if($db->is_page($id))
					{
						return($content.$html);
					}
					else
					{
						return($content);
					}
					break;
				default: 
					return($content.$html);
					break;
			}
		}
	}
	
	function render_plugin()
	{
		global $id;

		$user_sites = explode('|', $this->current_settings['sbb_sites']);
		
		if($this->current_settings['sbb_lines'] > 1)
		{
			$sites_per_line = ceil(sizeof($user_sites) / $this->current_settings['sbb_lines']);
		}
		else
		{
			$sites_per_line = 100;
		}
$this->current_settings['sbb_round_box_enabled'] = 'N';
		$html = "<!-- Social Bookmarks BEGIN -->\n";
		if($this->current_settings['sbb_round_box_enabled'] == 'Y')
		{
			$html .= "<div id=\"box\">\n";
		}
		$html .= "<div class=\"social_bookmark\">\n";

//		$html .= "<em>{$this->current_settings['sbb_label']}</em>";
		
		// Display the AJAX Interface
		if($this->current_settings['sbb_ajax_dropdown_enabled'] == 'Y')
		{
//				$html .= "<a href=\"#\" onclick=\"$$('div.d').each( function(e) { e.visualEffect('slide_up',{duration:0.5}) }); return false;\">Hide Sites</a> | ";
			$html .= "<a title=\"Click me to see the sites.\" href=\"#\" onclick=\"$$('div.d$id').each( function(e) { e.visualEffect('slide_down',{duration:2.5}) }); return false;\"><strong><em>{$this->current_settings['sbb_label']}</em></strong></a>\n";
			$html .= "<br />\n";
			$html .= "<div class=\"d$id\" style=\"overflow:hidden\">\n";
			$html .= "<br />\n";
		}
		else
		{
			$html .= "<a><strong><em>{$this->current_settings['sbb_label']}</em></strong></a>\n";
			$html .= "<br />\n";
			$html .= "<div class=\"d\">\n";
			$html .= "<br />\n";
		}
		
		// Display the individual Sites
		$i  = 1;
		foreach($this->social_places as $site => $settings)
		{
			if(in_array($site, $user_sites))
			{
				$html .= $this->get_social_bookmark($site, $settings, 0);
				
				if($i == $sites_per_line)
				{
					$html .= "<br />\n";
				}
/*				else
				{
					$html .= "&nbsp;";
				}
*/
				$i++;
			}
		}
		$html .= "<br />\n";
		
		if($this->current_settings['sbb_ajax_dropdown_enabled'] == 'Y')
		{
			// Display the Hide Sites AJAX button
			$html .= "<a style=\"font-size:90%;text-align: right; \" title=\"Click me to hide the sites.\" href=\"#\" onclick=\"$$('div.d$id').each( function(e) { e.visualEffect('slide_up',{duration:".$this->current_settings['sbb_ajax_dropdown_speed']."}) }); return false;\">Hide Sites</a>\n";
		}
		$html .= "</div>\n";
		
		if($this->current_settings['sbb_round_box_enabled'] == 'Y')
		{
			$html .= "</div>\n";
		}
		$html .= "</div>\n";

		$html .= "<!-- Social Bookmarks END -->\n";
		
		if($this->current_settings['sbb_ajax_dropdown_enabled'] == 'Y' and $this->current_settings['sbb_ajax_dropdown_hide'] == 'Y')
		{
			// Hide the Sites initially (on page load)
			$html .= "<script type=\"text/javascript\">$$('div.d$id').each( function(e) { e.visualEffect('slide_up',{duration:".$this->current_settings['sbb_ajax_dropdown_speed']."}) }); </script>";
		}
		
		return $html;
	}

	function get_social_bookmark($site_key, $settings, $output = 1)
	{
		// Post Permalink
		$permalink = get_permalink();			// WP function
		// Post Title
		$title = the_title('', '', false);		//WP function
		$title_enc = urlencode($title);

		// Post Alternative (Title) description
//		$target_desc = "Add '$title' to ".$settings['name']; (up to 3.2)
		$target_desc = $this->current_settings['sbb_tooltip_text'] ."&nbsp;". $settings['name'];	// sbb_tooltip_text
		
		// Populate the url with the article variables
		$target_href = str_replace('{title}', $title_enc, $settings['url']);
		$target_href = str_replace('{link}', $permalink, $target_href);	

		$img_src = $this->location_url."images/".$settings['img'];

		$target_img = "<img class=\"social_img\" src=\"$img_src\" title=\"$target_desc\" alt=\"$target_desc\" />";
//		$target_img = "<img style=\"padding:0px; margin:0px; border: none;\" src=\"$img_src\" title=\"$target_desc\" alt=\"$target_desc\" />";

		if($this->current_settings['sbb_target'] != 'new')
		{
			$target_url = "<a href=\"$target_href\" title=\"$target_desc\">$target_img</a>\n";
		}
		else
		{
			$target_url = "<a onclick=\"window.open(this.href, '_blank', 'scrollbars=yes,menubar=no,height=600,width=750,resizable=yes,toolbar=no,location=no,status=no'); return false;\" href=\"$target_href\" title=\"$target_desc\">$target_img</a>\n";
		}
		
		// Return result
		if($output)
		{
			print($target_url);
			return;
		}
		else
		{
			return($target_url);
		}
	}
	
	function display_social_custom()
	{
		print($this->render_plugin());	
	}

	function widget_sb_init()
	{
		// Check to see required Widget API functions are defined...
		if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
			return; // ...and if not, exit gracefully from the script.
		
		// Widget Kernel
		function widget_sb($args) 
		{
		    extract($args);
			print($before_widget);
		    print($before_title.'Social Bookmarks'. $after_title);
		    print($this->render_plugin());
		    print($after_widget);
		}
	}
}
?>