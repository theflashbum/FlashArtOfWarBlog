<?php
/*
 * social_db.php
 *
 * Models the interaction with the database.
 */
 
 include_once('social_object.php');
 
 class social_db extends social_object
 {
 	function social_db()
 	{
		parent::social_object();
	}
	
 	/**
	 * Retrieves the current $value of the specificied $option
	 *
	 * @return mixed
	 */
 	function get_user_option($option)
 	{
 		$value ='';
		
 		if($this->platform['type'] == 'WP')
 		{
 			$value = get_option($option);
 		}
 		
 		return $value;
 	}

 	/**
	 * Inserts the specified $option with the supplied $value
	 *
	 * @return void
	 */	 
 	function add_user_option($option, $value)
 	{
  		if($this->platform['type'] == 'WP')
 		{
 			add_option($option, $value);
 		}	
 	}
 	
 	/**
	 * Updates the specified $option with the supplied $value
	 *
	 * @return void
	 */	
 	function update_user_option($option, $value)
 	{
		if($this->platform['type'] == 'WP')
 		{
 			update_option($option, $value);
 		}
 	}
 	
 	/**
	 * Updates the user's sites selection from the admin menu
	 *
	 * @return string
	 */
	function escape_string($sql)
	{
 		if($this->platform['type'] == 'WP')
 		{
 			global $wpdb;
 			return $wpdb->escape($sql);
 		}		
	}
	
	/**
	 * Retrieves the pages in the host
	 *
	 * @return array
	 */
	function get_pages()
	{
		$page = array();
		
		if($this->platform['type'] == 'WP')
		{
			global $wpdb;
			
			// Retrieve the version of the installed WP
			// WP 2.1 defines the pages differently from 2.0
			if ($this->platform['version'] >= "2.1")
			{
				$pages = $wpdb->get_results("select id, post_title
								from $wpdb->posts
								where post_status = 'publish'
								and post_type = 'page'
								order by post_title asc", 'ARRAY_A');
			}
			else
			{
				$pages = $wpdb->get_results("select id, post_title
								from $wpdb->posts
								where post_status = 'static'
								order by post_title asc", 'ARRAY_A');
			}
		}
		
		return $pages;
	}

	/**
	 * Checks where the supplied $id belongs to a page or post
	 *
	 * @return boolean
	 */
	function is_page($id)
	{
		if($this->platform['type'] == 'WP')
		{
			global $wpdb;

			// Retrieve the version of the installed WP
			// WP 2.1 defines the pages differently from 2.0
			if ($this->platform['version'] >= "2.1")
			{
					
				$status = $wpdb->get_var("select post_type
								from $wpdb->posts
								where id = '$id'");

				if($status == 'page')
				{
					return true;
				}
				else
				{
					return false;
				}		
			}
			else
			{
					
				$status = $wpdb->get_var("select post_status
								from $wpdb->posts
								where id = '$id'");

				if($status == 'static')
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
	}
	
	function is_single()
	{
		$state = false;
		
		if($this->platform['type'] == 'WP' and function_exists(is_single))
		{
			$state = is_single();
		}
		
		return $state;
	}
 }
?>
