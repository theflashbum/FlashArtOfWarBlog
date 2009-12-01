<?php
/*
Plugin Name: Code Snippet
Plugin URI: http://blog.enargi.com/codesnippet/
Description: Code highlighting plugin. Use plugin options (In menu Options>Code Highlighting) to configure code style.
Version: 2.1.5
Author: Roman Roan	
Author URI: http://blog.enargi.com/
*/
/*  Copyright 2005  Roman Roan  (email : roman.roan@gmail.com)

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
/**
 * Doesn't work if PHP version is not 4.0.6 or higher
 */
if (version_compare(phpversion(),"4.0.6","<")) {
	return;
}
/*
 * Instance of plugin
 */
 $CodeSnippet = new CodeSnippet();

/*
 * Register plugin actions
 */
 /*
  * Regular pages
  */
add_action('wp_head',array(&$CodeSnippet, 'addCSS'), 1);
/*
 * Admin pages
 */
//add_action('admin_head',array(&$CodeSnippet, 'addCSS'), 1);
add_action('admin_head',array(&$CodeSnippet, 'init'), 1);
add_action('admin_head',array(&$CodeSnippet, 'addCssStyle'), 1);
add_action('admin_menu', array(&$CodeSnippet, 'addPluginOptionsPage'), 1);

/*
 * Help text
 */
add_action('simple_edit_form', array(&$CodeSnippet, 'writeHelper'), 1);

add_filter('the_content', array(&$CodeSnippet, 'highlightCode'), -10);
add_filter('the_excerpt', array(&$CodeSnippet, 'highlightCode'), 1);
add_filter('comment_text', array(&$CodeSnippet, 'highlightCode'), 1);

unset($CodeSnippet);

/*
 * Fake class pear for pear highliter
 */
/* if (!class_exists("Pear")) {
	class Pear 
	{
		function raiseError($text){
		
		}
	}
} */
/**
 * Code Snippet plugin class
 */
class CodeSnippet
{
	var $pluginLocation="/wp-content/plugins/codesnippet";
	var $geshi_path	;
	var $DEFAULT_STYLE="border:1px solid #ccc; background:#eee; padding: 5px;margin:10px;";
	var $HELP_TEXT='To insert code into post, use [code] tag with lang attribure. <br>Example: [code lang="php"] echo $a; [/code]';
	/**
	 * if our page has any code snippets
	 */
	var $PageHasCode=false;
	/**
	 * text to check if code is present
	 */
	var $CODE_TAG="/code";
	
	var $lib_path;
	
	
var $samplePhpCode ='
/*
* Comment
*/
		function hello() {
				echo "Hello!";
				return null;
		}
		exit();
';


function CodeSnippet(){
	
/*	if($this->pageHasCode){

	}*/
}

/**
 * Initialization of environment
 */
function init(){
		global $codesnippet_lib_path;

		$codesnippet_lib_path = ABSPATH . $this->pluginLocation.DIRECTORY_SEPARATOR."lib";
		$codesnippet_pear_path = ABSPATH . $this->pluginLocation.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."PEAR";
		
		//if(is_dir($codesnippet_pear_path))
		ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . $codesnippet_lib_path . PATH_SEPARATOR.$codesnippet_pear_path);
		
		require_once('geshi.php');	
		$this->geshi_path=$codesnippet_lib_path.DIRECTORY_SEPARATOR.'geshi';
		$this->lib_path=$codesnippet_lib_path;
}

/*
 * Add css references to page head
 */
	function addCSS($id){
		if ($this->codePresentInPosts()) {
			$this->init();
			$this->addCssStyle();
		}
	}
	
	function addCssStyle() {
			echo '<link rel="stylesheet" href="'.get_option('siteurl').$this->pluginLocation.'/codesnippet.css" type="text/css" />', "\n";
			echo '<style type="text/css">', "\n";
			echo '.codesnip-container  {'.$this->getStyle().'}', "\n";
			echo '</style>', "\n";
	}
	
	function getStyle(){
		$style = stripslashes(get_option('codesnippet_css_style'));
		
		/*
		 * Workaround for preview
		 */
		 global $codesnippet_css_style;
		 global $_POST;
		if ('process' == $_POST['stage'])
		{
			if ($_POST['codesnippet_css_style'])
			$style=$_POST['codesnippet_css_style'];
		}
		
		if ($style==null || $style=="") {
			$style=CodeSnippet::getDefaultStyle();
		} 
		
		return $style;
		
	}

	/**
	 * Check if code tag is present in any of the posts on page
	 * Used in header, does not replace anything.
	 */
	function codePresentInPosts(){
		global $wp_query;
		if ($wp_query!=null && ($wp_query->post_count>0)) {
			foreach ($wp_query->posts as $post){
				if (stristr(strtolower($post->post_content), $this->getCodeTag())) {					
					$this->pageHasCode=true;
					return true;
				}
			}
		}
		return false;
	}
	
	
	function writeHelper($param){
		echo CodeSnippet::getHelpText();
	}
	
	function sampleCodeFactory() {		
		$this->init();
		$html=$this->highlight_geshi($this->samplePhpCode,"php-brief");
		return $this->addContainer($html);
	}
	
	/**
	 * 
	 */
	function addPluginOptionsPage(){
		if (function_exists('add_options_page')) {
			add_options_page('Code Snippet', 'Code Highlighting', 9, 'codesnippet/codesnippet-options.php');
		}
	}
	
	
    
    /**
     * Perform code highlighting using GESHi engine
     */
    function highlight_geshi($content,$lang){
		$lang=$this->filterLang($lang);
		if (!class_exists("geshi")) { 
			$this->init();
			require_once('geshi.php');
		}	
		$geshi = new GeSHi($content, $lang, $this->geshi_path);
		$geshi->enable_classes();
		$geshi->set_overall_class('codesnip');
			if (stripslashes(get_option('codesnippet_line_numbers'))==true) {
				$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS,1);
			} else {
				$geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS,1);
			}
			$geshi->set_header_type(GESHI_HEADER_DIV);
			

		if ( $geshi->error() ) {
			return $geshi->error();
		} 
		$result=$geshi->parse_code();
		return  $result;
    	
    }
    /**
     * Perform highlight usign PEAR highlighter
     */
    function highlight_pear($content,$lang){
    require_once 'Text/Highlighter.php';
    require_once 'Text/Highlighter/Renderer/Html.php';
    if (stripslashes(get_option('codesnippet_line_numbers'))==true) {
    $options = array(
            'numbers' => HL_NUMBERS_TABLE,
            'tabsize' => 4,
        );
    }
    $renderer =& new Text_Highlighter_Renderer_HTML($options);
    $highlighter =& Text_Highlighter::factory($lang);
    $highlighter->setRenderer($renderer); 
    return '<div class="hl-main">'.$highlighter->highlight($content).'</div>';
    }


	/**
	 * Search content for code tags and replace it
	 */
    function highlightCode($content)
    {
       $content = preg_replace('#\[code\](.*?)\[/code\]#sie', '$this->performHighlight(\'\\1\', false, $content);', $content);
       $content = preg_replace('#\[code lang="(.*?)"\](.*?)\[/code\]#sie', '$this->performHighlight(\'\\2\', \'\\1\', $content);', $content);
       $content = preg_replace('#\<code\>(.*?)\</code\>#sie', '$this->performHighlight(\'\\1\', false, $content);', $content);
       $content = preg_replace('#\<code lang="(.*?)"\>(.*?)\</code\>#sie', '$this->performHighlight(\'\\2\', \'\\1\', $content);', $content);

        return $content;
    }


	/**
	 * Perform code highlightning
	 */
	function performHighlight($text,$lang,$content) 
	{
		$text = str_replace(array("\\\"", "\\\'"), array("\"", "\'"), $text);
		$text = preg_replace('/(< \?php)/i', '<?php', $text); 
        $text = trim($text);
		
		if ($lang) {
		 $result = $this->highlight_geshi($text,$lang);
		 //$result = $this->highlight_pear($text,$lang);//TODO?
		}
		else {
			$result =$text;
		}

		return $this->addContainer($result);
	}

	function addContainer($html) {
		$result='<div class="codesnip-container" >'.$html.'</div>';
		return $result;
	}

	
	/**
	 * Process the lang identifier sttribute string
	 */
	function filterLang($lang){
		$lang=strtolower($lang);
		if (strstr($lang,"html")) {
			$lang="html4strict";
		}
		return $lang;
	}
	function getDefaultStyle(){
		return $this->DEFAULT_STYLE;
	}
	function getHelpText(){
		return $this->HELP_TEXT;
	}
	function getCodeTag(){
		return $this->CODE_TAG;
	}
	

}


?>
