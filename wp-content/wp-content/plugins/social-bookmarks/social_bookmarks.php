<?php
/*
Plugin Name: Social Bookmarks
Plugin URI: http://www.dountsis.com/projects/social-bookmarks/
Description: Adds a list of XHTML compliant graphic links at the end of your posts that allow your visitors to easily submit them to a number of social bookmarking sites. Use the plugin options under <a href="admin.php?page=social-bookmarks/social_view_admin.php">Dashboard > Social</a> to configure it.
Author: Apostolos Dountsis
Author URI: http://www.dountsis.com
Version: 4.1.3
*/

/*  Copyright 2008  APOSTOLOS DOUNTSIS

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

include_once('social_object.php');
include_once('social_db.php');
include_once('social_view_admin.php');
include_once('social_view_public.php');

if (!class_exists('social_bookmarks')) 
{
	class social_bookmarks extends social_object
	{
		var $db;
		
		var $social_places;
		
		var $current_settings;
		
		var $default_settings;
		
		function social_bookmarks()
		{
			parent::social_object();
	
			// Default Settings
			$this->default_settings = array('sbb_sites' => 'delicious_en|digg_en|facebook_en|google_en|misterwong_en|netscape_en|reddit_en|stumbleupon_en|technorati_en|yahoo_en|tipd_en|buzz_en|twitter_en',
								'sbb_label' => 'Bookmark It',
								'sbb_target' => 'new',
								'sbb_pages_excluded' => 'none',
								'sbb_lines' => 3,
								'sbb_display' => 3,
								'sbb_tooltip_text' => 'Add to',
								'sbb_round_box_enabled' => 'Y',
								'sbb_ajax_dropdown_enabled' => 'Y',
								'sbb_ajax_dropdown_hide' => 'Y',
								'sbb_ajax_dropdown_speed' => '0.5');
	
			// Instantiate database object
			$this->db =& new social_db();
			
			// Load the sites
			$this->init_social_places();
			
			// Retrieve user settings
			$this->current_settings = $this->get_current_settings();
			
			// Handle POST submission
			if($_POST['sbb_target'] or $_POST['sbb_sites'])
			{
				$this->process_post_submission($_POST);
			}
			
			// Attach the admin view
			$admin_menu =& new social_view_admin($this->current_settings, $this->social_places);
	
			// Attach the public view
			$public_view =& new social_view_public($this->current_settings, $this->social_places);
	
		}
		
		function init_social_places()
		{
			// Setup the sites 
			$xml_sites = $this->get_xml_sites();
	
			foreach($xml_sites as $i => $value)
			{
				$key = $value['key'];
				$name = $value['name'];
				$img = $value['img'];
				$url = $value['url'];
				$url = str_replace('&', '&amp;', $url);	
				
				$this->social_places[$key] = array('img' => $img, 
													'url' => $url , 
													'name' => $name);
			}
		}
		
		/**
		 * Loads the XML file containing the supported Sites
		 *
		 * @return array
		 */
		function get_xml_sites()
		{
			$xml_sites = array();
			// Load the Sites from the XML file
	//		$xml_sites = social_common::get_xml_contents(dirname(__FILE__)."/sites.xml", 'site');
	
			// Test code for Internationalization Support
			$language_files = social_common::get_xml_filenames();
	
			if(!empty($language_files))
			{
				foreach($language_files as $lang_file)
				{
					$lang_array = social_common::get_xml_contents(dirname(__FILE__)."/".$lang_file, 'site');
	
					if(!empty($xml_sites) and sizeof($language_files) != 1)
					{
						social_common::array_push_associative($xml_sites, $lang_array);
					}
					else
					{
						$xml_sites = $lang_array;
					}
				}
			}
			return $xml_sites;	
		}
	
		/**
		 * Set Default settings as current if no options in DB
		 * Otherwise populate current settings from DB
		 *
		 * @return void
		 */ 	
		function get_current_settings()
		{
			foreach($this->default_settings as $label => $value)
			{
				if(!$this->db->get_user_option($label))
				{
					$this->db->add_user_option($label, $value);
					$settings[$label] = $value;
				}
				else
				{
					$settings[$label] = $this->db->get_user_option($label);
				}
			} 
			
			return $settings;
		}
		
		function process_post_submission(&$data)
		{
			// Process the case of a submission (POST) in the admin menu
			if($data)
			{
				if($data['sbb_sites'])
				{
					unset($data['sbb_sites']);
					$this->update_option_sites($data);
				}
				elseif($data['sbb_general'])
				{
					unset($data['sbb_general']);
					$this->update_option_general($data);
				}
				
				// Reload the current page
				social_common::redirect();
			}
		}
	
		/**
		 * Updates the user's sites selection from the admin menu
		 *
		 * @return void
		 */ 	
		function update_option_sites($data)
		{
			$option_array = array();
	
			// Compile Sites as '|' separated values for DB
			foreach($this->social_places as $site => $settings)
			{
				if(array_key_exists($site, $data))
				{
					$option_array[] = "$site";
				}
			}
			$option = implode('|', $option_array);
	
			// Store in DB
			if($this->db->get_user_option('sbb_sites'))
			{
				$this->db->update_user_option('sbb_sites', $option);
			}
			else
			{
				$this->db->add_user_option('sbb_sites', $option);
			}
		}
	
		/**
		 * Updates the user's general application settings from the admin menu
		 *
		 * @return void
		 */ 		
		function update_option_general($data)
		{
			if(!array_key_exists('sbb_pages_excluded',$data))
			{
				$data['sbb_pages_excluded'] = 'none';
			}
	
			if($data)
			{
				foreach($data as $name => $value)
				{
					if($name == 'sbb_label' or $name == 'sbb_tooltip_text')
					{
						$value = $this->db->escape_string($value);
					}
	
					// Update *only if* the value of the option has changed
					if($this->db->get_user_option($name) != $value)
					{
						$this->db->update_user_option($name, $value);
					}
				}
			}
		}
	}
}
// Instantiate the application
$sb =& new social_bookmarks();
?>