<?php
/*
 * social_object.php
 *
 * The base class of the application.
 */
include_once('social_common.php');

class social_object
{
	// The running platform (WordPress)
	var $platform;
	
	// The url of the application
	var $location_url;
	
	
	function social_object()
	{
		$this->platform = social_common::get_host_platform();
		
		$this->location_url = social_common::get_current_url();
	}
}
?>