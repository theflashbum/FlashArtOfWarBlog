<?php
/*
 * social_common.php
 *
 * General Purpose Library
 * It contains static functions.
 */
 
 include_once('social_db.php');
 
 class social_common
 {

	/**
	 * Wrapper for print_r() that formats the array for HTML output
	 *
	 * @return void
	 */
	function pre_print_r($txt)
	{
		print("<pre>\n"); 
		print_r($txt); 
		print("</pre>\n");
	}
	
	/**
	 * Checks whether the supplied $number is even
	 *
	 * @return boolean
	 */
	function is_even($number)
	{
		if ($number % 2 == 0 )
		{
			// The number is even
			return true;
		}
    		else
		{
			// The number is odd
			return false;
		}
	}
	
 	/**
	 * Retrieves the children found in the specified $file
	 *
	 * @return array
	 */	 	
 	function get_xml_contents($filename, $parent_tag='site')
 	{
		$site = array();

		// read the XML file
		$data = implode("", file($filename));

		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $data, $values, $tags);
		xml_parser_free($parser);

	   // loop through the structures
	   foreach ($tags as $key=>$val) 
	   {
		   if ($key == $parent_tag)
		   {
			   $site_ranges = $val;

			   // each contiguous pair of array entries are the 
			   // lower and upper range for each site definition
			   for ($i=0; $i < count($site_ranges); $i+=2)
			   {
				   $offset = $site_ranges[$i] + 1;
				   $len = $site_ranges[$i + 1] - $offset;

				   $site[] = social_common::parse_xml_attribute(array_slice($values, $offset, $len));
			   }
		   }
		   else
		   {
			   continue;
		   }
	   }
	   return $site;
	}
	
	function parse_xml_attribute($site_values) 
	{
		$site = array();
		for ($i=0; $i < count($site_values); $i++)
		{
			$site[$site_values[$i]['tag']] = $site_values[$i]['value'];
		}

		return($site);
	}
	
	
	function redirect($location = '')
	{
		if(empty($location))
		{
			$location = $_SERVER['PHP_SELF'];
			
			if($_GET)
			{
				$args = '?';
				foreach($_GET as $var => $value)
				{
					$args .= "$var=$value&";
				}
			}
		}

		header("location: $location$args");
		exit();
	}

	/**
	 * Determines the application host platform
	 *
	 * @return array
	 */	
	function get_host_platform()
	{
		$host = array();
		
		// Check for WordPress
		if(function_exists(get_bloginfo))
		{
			$host = array('type' => 'WP',
						  'version' => substr(get_bloginfo('version'), 0, 3));
		}
		
		return $host;
	}
	
	/**
	 * Retrieve the application URL
	 *
	 * @return string
	 */ 	
 	function get_current_url()
 	{
		$current_dir = '';
		
 		// Get Host
		$host = social_common::get_host_platform();
		
 		if($host['type'] == 'WP')
 		{
//			$app_directory = end(explode('/', dirname(__FILE__)));
			$app_directory = end(explode(DIRECTORY_SEPARATOR, dirname(__FILE__)));
			$current_dir = social_db::get_user_option('siteurl').'/wp-content/plugins/'.$app_directory.'/';
 		}

		return $current_dir;
 	}
	
	// Retrieve all the languge files in the plugin directory
	// Test code for Internationalization Support
	function get_xml_filenames()
	{
		$handle = opendir(dirname(__FILE__));
//		$exclude = array("index.php", ".", "..");
		$language_files = array();

		// Parse directory
		while (false !== ($file = readdir($handle))) //while($fn = readdir($dn)) 
		{
			$f = explode('.', $file);

			if($f[1] == 'xml' and 					// Is it an XML file
				substr($f[0], 0, 5) == 'sites' and	// Identify the language files
				strlen($f[0]) != 5)					// Exclude sites.xml (used up to 3.2)
			{
//				print(substr(strrchr($f[0], '_'), 1));	// Gets the language code
				$language_files[] = $file;
			}
//			if ($file == $exclude[0] || $file == $exclude[1] || $file == $exclude[2]) continue;
// 			$language_file[] = $file;
		}
		
		closedir($handle);
		return $language_files;
	}
	
	// Append associative array elements
	// array_push_associative($theArray, $items);
	function array_push_associative(&$the_array, $item)
	{
/*
		print('home array: <br />');
		social_common::pre_print_r($the_array);
		print('To be added: <br />');
		social_common::pre_print_r($item);
*/
		if(is_array($item) and !empty($item))
		{
			$array_size = sizeof($the_array);
			
			foreach($item as $key => $value)
			{
				$the_array[$array_size] = $value;
				// Increment the array key
				$array_size++;
			}
		}
//		print('Merged <br />');
//		social_common::pre_print_r($the_array);		
//		return $the_array;
	}
}
?>
