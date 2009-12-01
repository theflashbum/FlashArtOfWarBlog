<?php
/*
Plugin Name: Truncate Title
Plugin URI: http://elliottback.com/wp/archives/2004/12/12/truncate-title/
Description: Truncates long titles
Author: Elliott Back
Author URI: http://elliottback.com/
Version: 1.0
*/ 

function truncate_long_titles($title) {
	$MAX_LENGTH = 60;
	$TERMINATOR = ' ...';

	if(strlen($title) > $MAX_LENGTH){
		$parts = explode(' ', $title);
		$title = "";
		$i = 0;

		while(strlen($title) < $MAX_LENGTH && $i < count($parts)){
			if(strlen($parts[$i]) + strlen($title) > $MAX_LENGTH){
				return $title . $TERMINATOR;
			} else {
				$title .= ' ' . $parts[$i];
				$i++;
			}
		}

		return $title . $TERMINATOR;
	} else{
		return $title;
	}
}

add_filter('the_title', 'truncate_long_titles');

?>