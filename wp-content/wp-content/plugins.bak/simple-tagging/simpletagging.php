<?php
/*
Plugin Name: Simple Tagging
Plugin URI: http://sw-guide.de/wordpress/plugins/simple-tagging/
Description: Simple Tagging is another tagging plugin for Wordpress: smarter, better, faster :-) 
Version: 1.6
Author: Michael Woehrer
Author URI: http://sw-guide.de

 	    ____________________________________________________
       |                                                    |
       |               Simple Tagging Plugin                |
       |                © Michael Woehrer                   |
       |____________________________________________________|

	© Copyright 2007  Michael Woehrer (michael dot woehrer at gmail dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

	----------------------------------------------------------------------------
	ACKNOWLEDGEMENTS
	Many thanks to Jerome Lavigne for his Keywords Plugin: 
	<http://vapourtrails.ca/wp-keywords>
	Simple Tagging Plugin is based on Jerome's Plugin 2.0 Beta 3, however 
	heavily extended. Anyway, I think without Jerome's great work I would have 
	never started this project. 

	Also, many thanks to Christopher T. Holland for WICK ("Web Input 
	Completion Kit") <http://wick.sourceforge.net/> which I use for the 
	type-ahead feature.

	----------------------------------------------------------------------------
	INSTALLATION, USAGE:
	See plugin's homepage...
	----------------------------------------------------------------------------
*/


$STagging =& new SimpleTagging();


################################################################################
########## TEMPLATE TAGS  
################################################################################

/*******************************************************************************
 * Function:	STP_PostTags / STP_GetPostTags
 * Purpose:		Outputs the list of tags related to the current post.
 * 				Use this function in the "Loop". 
 * Input:		...
 * Output:		STP_PostTags: Echo
 * 				STP_GetPostTags: String
 ******************************************************************************/ 
function STP_GetPostTags($linkformat=null, $include_cats=null, $tagseparator=null, $notagstext=null, $post_id=null) {
	global $STagging;
	return $STagging->outputPostTags($linkformat, $include_cats, $tagseparator, $notagstext, $post_id);
}
function STP_PostTags($linkformat=null, $include_cats=null, $tagseparator=null, $notagstext=null, $post_id=null) { 
	echo STP_GetPostTags($linkformat, $include_cats, $tagseparator, $notagstext, $post_id); 
}


/*******************************************************************************
 * Function:	STP_Tagcloud
 * Purpose:		Displays a tagcloud
 * Input:		...
 * Output:		STP_Tagcloud: Echo
 * 				STP_GetTagcloud: String
 ******************************************************************************/
function STP_GetTagcloud($linkformat=null, $tagseparator=null, $include_cats=null, $sort_order=null, $display_max=null, $display_min=null, $scale_max=null, $scale_min=null, $notagstext=null) {
	global $STagging;
	return $STagging->createTagcloud($linkformat, $tagseparator, $include_cats, $sort_order, $display_max, $display_min, $scale_max, $scale_min, $notagstext);
}
function STP_Tagcloud($linkformat=null, $tagseparator=null, $include_cats=null, $sort_order=null, $display_max=null, $display_min=null, $scale_max=null, $scale_min=null, $notagstext=null) {
	echo STP_GetTagcloud($linkformat, $tagseparator, $include_cats, $sort_order, $display_max, $display_min, $scale_max, $scale_min, $notagstext);
}


/*******************************************************************************
 * Function:	STP_RelatedPosts
 * Purpose:		Presents related posts according to the tags of the current post.
 * Input:		...
 * Output:		STP_RelatedPosts: Echo
 * 				STP_GetRelatedPosts: String
 ******************************************************************************/
function STP_GetRelatedPosts($format=null, $postsseparator=null, $sortorder=null, $limit_qty=null, $limit_days=null, $dateformat=null, $nothingfound=null, $includepages=null) {
    global $STagging;
    return $STagging->createRelatedPostsList($format, $postsseparator, $sortorder, $limit_qty, $limit_days, $dateformat, $nothingfound, $includepages);
}
function STP_RelatedPosts($format=null, $postsseparator=null, $sortorder=null, $limit_qty=null, $limit_days=null, $dateformat=null, $nothingfound=null, $includepages=null) { 
	echo STP_GetRelatedPosts($format, $postsseparator, $sortorder, $limit_qty, $limit_days, $dateformat, $nothingfound, $includepages);
}


/*******************************************************************************
 * Function:	STP_RelatedTags / STP_GetRelatedTags
 * Purpose:		Outputs related tags in tag search. Useful if you want to display
 * 				related tags like in del.icio.us.
 * Input:		...
 * Output:		STP_RelatedTags: Echo
 * 				STP_GetRelatedTags: String
 ******************************************************************************/ 
function STP_GetRelatedTags($format=null, $tagseparator=null, $sortorder=null, $nonfoundtext=null) {
	global $STagging;
	return $STagging->outputRelatedTags($format, $tagseparator, $sortorder, $nonfoundtext);
}
function STP_RelatedTags($format=null, $tagseparator=null, $sortorder=null, $nonfoundtext=null) {
	echo STP_GetRelatedTags($format, $tagseparator, $sortorder, $nonfoundtext);
}

// For removing tags links
function STP_GetRelatedTagsRemoveTags($format=null, $separator=null, $nonfoundtext=null) {
	global $STagging;
	return $STagging->outputRelatedTagsRemoveTags($format, $separator, $nonfoundtext);
}
function STP_RelatedTagsRemoveTags($format=null, $separator=null, $nonfoundtext=null) { 
	echo STP_GetRelatedTagsRemoveTags($format, $separator, $nonfoundtext);
}



/*******************************************************************************
 * Function:	STP_MetaKeywords
 * Purpose:		Outputs the list of meta keywords for the current view
 * 				Use this within your site's header, e.g.
 * 				<meta name="keywords" content="<X?php STP_MetaKeywords(); ?X>" />
 * Input:		...
 * Output:		STP_MetaKeywords: Echo
 * 				STP_GetMetaKeywords: String
 ******************************************************************************/
function STP_GetMetaKeywords($before='', $after='', $separator=',', $include_cats=null) {
	global $STagging;
    return $STagging->getMetaKeywords($before, $after, $separator, $include_cats);
}
function STP_MetaKeywords($before='', $after='', $separator=',', $include_cats=null) {
	echo STP_GetMetaKeywords($before, $after, $separator, $include_cats);
}

/*******************************************************************************
 * Function:	STP_IsTagView
 * Purpose:		Use this to determine if the current view will be returning 
 * 				tag search results.
 * Input:		...
 * Output:		Returns TRUE if a tag search was requested, FALSE otherwise.
 ******************************************************************************/
function STP_IsTagView() {
	global $STagging;
    return $STagging->is_tag_view();
}

/*******************************************************************************
 * Function:	STP_CurrentTagSet
 * Purpose:		Outputs the keyword used in a keyword search.  Useful in your 
 * 				tags.php page:
 * 				<h2>All results for "<X?php STP_CurrentTagSet(); ?X>"</h2>
 * Input:		Separator if more than one tag is used
 * Output:		STP_CurrentTagSet: Echo
 * 				STP_GetCurrentTagSet: String
 ******************************************************************************/
function STP_GetCurrentTagSet($separator=', ') {
    global $STagging;
	return $STagging->tag_url2name($STagging->search_tag, $separator);
}
function STP_CurrentTagSet($separator=', ') {
	echo STP_GetCurrentTagSet($separator);
}


################################################################################
########## MAIN CLASS  
################################################################################
class SimpleTagging {

	/**
	 * @access public
	 */
	var $stp_version = '1.6';			// Plugin's version 
	var $stp_optname = 'stp_options';	// Name of the options in wp_options table
	var $option; 						// array containing the options
	var $defaultoption; 				// array containing the *default* options
	
	var $info;							// array containing addition information such as paths, URLs etc.

	// Structure of table, used for creation/installation
	var $tablestruct = " ( post_id bigint(20) unsigned NOT NULL, tag_name varchar(255) NOT NULL default '', PRIMARY KEY  (post_id, tag_name), KEY tag_name (tag_name) )";

	/**
	 * @access private
	 */
    var $_postids = '';         // stores comma-separated list of post IDs in current view
    var $_posttags = null;      // post tag data cache
    var $_alltags = null;       // all published tag data cache
    var $_allcats = null;       // all published categories cache
    var $_allcombined = null;   // sorted compilation of tags & cats
    var $_initdone = false;
    var $_flushrules = false;

	/**
	 * set during class setup
	 *	 
	 * @access public
	 */
    var $is_rewriteon = false;	// will be set to TRUE if rewrite rules are enabled
    var $base_url  = '';		// base URL for tags (depending on permalink style)
	var $is_wp_21 = false;		// will be set to TRUE if a WP version higher than 2.0.x is being used


	/**
	 * set during class usage
	 *	 
	 * @access public
	 */
    var $search_tag = '';		// tag search value, will be set if tag search is being used
    var $search_tag_count = 0;	// number of tags in search_tag. Users can use a tag search like bavaria+munich+beer, this would be 3
    
	/**
	 * SimpleTagging
	 *
	 * Constructor for the SimpleTagging class. 
	 */ 
	function SimpleTagging() {

		########################## OPTIONS #####################################
		// 1. Define default options
		$defaultopt = array(
			'version_for_install' 	=> '0',    		// plugin's version, !! DON'T CHANGE THIS VALUE !! -- will be updated automatically. Change $stp_version instead 
			'tags_table'			=> 'stp_tags',	// table where tags are stored

			// Admin options
			'admin_max_suggest'		=> '30',			// Maximum number of suggested tags, zero (0) means no limit and shows all tags
			'admin_tag_suggested'	=> '1',			// Uses 'detection' for tags: 
			'admin_tag_sort'		=> 'alpha',	// 'relevance' => most relevant/used tags first, 'alpha' => alphabetic order 

			// miscellaneous
			'query_varname'			=> 'tag',		// HTTP var name used for tag searches
			'trailing_slash'		=> '0',			// include trailing slash on tag urls
			'template'				=> 'pagetemplate.simpletagging.php', // template file to use for displaying tag queries
			'usehyphen'				=> '0',			// use hyphens "-" as space separator in the tag URL instead of underscore "_"

			// feed options
			'use_feed_cats'			=> '1',			// insert tags into feeds as categories
			// meta keyword options
			'meta_autoheader'		=> '1',			// automatically output meta keywords in header
			'meta_always_include'	=> '',			// meta keywords to always include
			'meta_includecats'		=> 'default',	// 'default' => include cats in meta keywords only for home page, 'all' => includes cats on every page, 'none' => never included
			// post tag options
			'post_linkformat'		=> '<a href="%fulltaglink%" title="Browse for %tagname%" rel="tag">%tagname%</a>', // post tag format (initialized to $link_localsearch)
			'post_tagseparator'		=> ', ',       // tag separator string
			'post_includecats'		=> '0',        // include categories in post's tag list
			'post_notagstext'		=> 'none',     // text to display if no tags found
			// tag cloud options
			'cloud_linkformat'		=> '<li class="t%scale%"><a title="%tagname% (%count%)" href="%fulltaglink%">%tagname%</a></li>',         // post tag format (initialized to $link_tagcloud)
			'cloud_tagseparator'	=> ' ',			// tag separator character(s)
			'cloud_includecats'		=> '0',			// include categories in tag cloud
			'cloud_sortorder'		=> 'natural',	// tag sorting: natural, countup/asc, countdown/desc, alpha
			'cloud_displaymax'		=> '0',			// maximum # of tags to display (all if set to zero)
			'cloud_displaymin'		=> '1',			// minimum tag count to include in tag cloud
			'cloud_scalemax'		=> '10',        // maximum value for count scaling (no scaling if zero)
			'cloud_scalemin'		=> '1',			// minimum value for count scaling
			'cloud_notagstext'		=> 'No tags were found that match the criteria given.',	// text to display if no tags found
			// related posts options
			'related_format'		=> '<li><a href="%permalink%" title="%title% (%date%)">%title%</a> (%commentcount%)</li>',
			'related_postsseparator'=> ' ',			// related posts separator
			'related_sortorder'		=> 'date-desc',	// sort order: alpha, date-asc, date-desc, random
			'related_limit_qty'		=> '5',
			'related_limit_days'	=> '365',
			'related_dateformat'	=> get_option('date_format'),
			'related_nothingfound'	=> '<li>None</li>',
			'related_includepages'	=> '0',			// include pages in related posts list
			// Related tags options
			'relatedtags_format'		=> '<li><span>%count%</span> <a href="%taglink_plus%">+</a> <a href="%taglink%">%tagname%</a></li>',
			'relatedtags_tagseparator'	=> ' ',	// related tags separator
			'relatedtags_sortorder'		=> 'alpha-asc',	// sort order: count-desc, count-asc, alpha-desc, alpha-asc
			'relatedtags_nonfoundtext'	=> 'No related tags found.',
			'relatedtags_remove_format'	=> '<li>&raquo; <a href="%url%">remove %tagname%</a></li>',
			'relatedtags_remove_separator'=> ' ',	// remove tags separator
			'relatedtags_remove_nonfoundtext' => '<li>&nbsp;</li>',	// text if no result 
			// Embedded Tags
			'tag_embed_use'		=> 	'0',		// Use embedded tags
			'tag_embed_start'	 => '[tags]',	// For tags to be places into the content: start tag 
			'tag_embed_end'	 	 => '[/tags]',	// For tags to be places into the content: end tag
		);
		// 2. Set class property for default options
		$this->defaultoption = $defaultopt;

		
		// 3. Get options from WP options table
		$optionsFromTable = get_option($this->stp_optname);

		// 4. Update default options by getting not empty values from options table
        foreach($defaultopt as $def_optname => $def_optval) {
			if ($optionsFromTable[$def_optname] != '' ) {
				$defaultopt[$def_optname] = $optionsFromTable[$def_optname];
			}
		}

		// 5. Set the class property
		$this->option = $defaultopt;

		######################### INFO #########################################

		// Determine installation path & url
	    $install_dirurl_tmp = basename(dirname(__FILE__)); // basename strips all parent directories of the directory of the given filename
		$info['install_url'] = get_option('siteurl') . '/wp-content/plugins';
		$info['install_dir'] = ABSPATH . 'wp-content/plugins';
		if ( $install_dirurl_tmp != 'plugins' ) { 
			$info['install_url'] .= '/' . $install_dirurl_tmp;
			$info['install_dir'] .= '/' . $install_dirurl_tmp;
		}

        // Set custom table name
        global $table_prefix;
        $info['stptable'] = $table_prefix . $this->option['tags_table'];

		$this->info = array(	// repeated to get an overview of all available info values
			'install_url'		=> $info['install_url'],
			'install_dir'		=> $info['install_dir'],
			'stptable'			=> $info['stptable'],
		);


		######################### WORDPRESS VERSION ############################
		// Get Wordpress Version since we use different queries etc. in case of WP 2.1
		global $wp_version;
		if (preg_match("/^2\.0/", $wp_version)) { 
			$this->is_wp_21 = false;
		} else {
			$this->is_wp_21 = true;
		}

		################# SETUP FILTER/ACTION TRIGGERS #########################
        add_action('init', array(&$this, 'wpaction_InitRewrite'));  // can't use WP rewrite flags until "init" hook
        add_filter('the_posts', array(&$this, 'wpfilter_GetPostIds'), 90);   // get post IDs once WP query is done
        add_filter('query_vars', array(&$this, 'wpfilter_AddQueryVar'));     // used for tag searches
        add_action('parse_query', array(&$this, 'wpaction_ParseQuery'));     // used for tag searches
		add_filter('the_content', array(&$this, 'wpfilter_the_content'), 10);	// used for filtering the content -- e.g. removing embedded tags [tags]...[/tags]	
		add_filter('the_excerpt', array(&$this, 'wpfilter_the_content'), 10);	// used for filtering the content -- e.g. removing embedded tags [tags]...[/tags]
        if ($this->option['use_feed_cats'])       // insert tags into feeds as categories
            add_filter('the_category_rss', array(&$this, 'wpfilter_CreateFeedCategories'), 5, 2);
        if ($this->option['meta_autoheader'])     // automagic meta keywords in header
            add_action('wp_head', array(&$this, 'wpaction_OutputHeader'));


		################# Load admin file and initialize object ################
        if (is_admin() || ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) ) {

			include ($this->info['install_dir'] . '/simpletagging.admin.php');
			
			$STaggingAdmin = new SimpleTaggingAdmin($this);

		}


    } // function SimpleTagging


	####################################################################################################################
	########## PART 0: Convert query tags  
	####################################################################################################################
	
	##################
	## Naming:
	## $tag_name	- Single tag name, e.g. 'Hello World'
	## $tag_url		- tag name used in URL - can containt multiple tags separated by +, 
	##  			  e.g. 'Hello_World' or 'Hello_World+Wordpress+CMS'
	##################

	// Converts user tag input
	// $taglist: string - a comma separated list of tags the user has entered and wants to save to the database
	function tag_convertUserInput($tag_name_list) {
		$tag_name_list = strip_tags($tag_name_list);					// no HTML tags
		$tag_name_list = preg_replace('/\s\s+/', ' ', $tag_name_list); // Replace multiple spaces with one space
		$tag_name_list = trim($tag_name_list);
		$tag_name_list = str_replace('_', '-', $tag_name_list);
		if ($this->option['usehyphen']) 
				$tag_name_list = str_replace('-', ' ', $tag_name_list);
		$tag_name_list = str_replace('+', '', $tag_name_list);	// we need + for adding multiple tags
		$tag_name_list = str_replace('\\', '-', $tag_name_list);
		$tag_name_list = str_replace('/', '-', $tag_name_list);
		$tag_name_list = preg_replace('|[\'"<>$%?&^#;*=]|i', '', $tag_name_list); // remove several special chars

		return $tag_name_list;

	}
	// Converts Name to URL; E.g. 'Hans Dampf' to 'Hans_Dampf'
	function tag_name2url($tag_name) {
		if ($this->option['usehyphen']) {
			$tag_name = str_replace(' ', '-', $tag_name);
		} else {
			$tag_name = str_replace(' ', '_', $tag_name);	// urlencode converts space ' ' into +. We wanna use _ instead		
		}

		$tag_name = urlencode($tag_name);
		$tag_name = str_replace('%2F', '/', $tag_name);	// seems that Apache's mod_rewrite are unable to handle urlencoded URLs properly
		$tag_name = str_replace('%2B', '+', $tag_name);	// seems that Apache's mod_rewrite are unable to handle urlencoded URLs properly
		$tag_name = str_replace('%20', '_', $tag_name);	// seems that Apache's mod_rewrite are unable to handle urlencoded URLs properly
				
		return $tag_name;
	}




	// Converts URL to Name; E.g. 'Hans_Dampf' -> 'Hans Dampf' or '100_dollar+200+300' -> '100 dollar, 200, 300'
	function tag_url2name($tag_url, $sep=', ') {
		$tag_name = str_replace(' ', '+', $tag_url); // Replace ' ' with + if + became ' ' due to urlencode. Should always work since we don't allow spaces in URLS as we convert them to _ 	

		if ($this->option['usehyphen']) {
			$tag_name = str_replace('-', ' ', $tag_name);
		} else {
			$tag_name = str_replace('_', ' ', $tag_name);	// consider real blanks
		}

		$tag_name_array = explode('+', $tag_name);	// Create array

		$res = '';
		foreach ($tag_name_array as $val) {
			$res .= ($res == '') ? $val : $sep . $val;
		}

		return $res;

	}


	// Converts an URL to an array containing all URLs.
	// E.g.: '100+200+300' -> array('100','200','300')
	function tag_url2url_array($tag) {
		// Replace ' ' with + if + became ' ' due to urlencode
		// Should always work since we don't allow spaces in URLS as we convert them to _
		$tag = str_replace(' ', '+', $tag); 

		// Now we can create the array
		$tag = explode('+', $tag);

		return $tag;			
	}
	// Converts an URL to a comma separated string -- containing >'< before and after each tag to use it for MySQL query "IN" 
	// Input: e.g. $this->search_tag, may be comma separated
	function tag_urls2names_IN ($tags) {
		$currtagArray = explode(' ', $tags); // Due to urlencode, + became ' ', so we explode with ' ' 
		$currtagArray = array_unique($currtagArray);
		$tagList = implode("','", $currtagArray);
		$tagList = $this->tag_url2name($tagList);
		$tagList = "'" .  $tagList . "'"; 
		return $tagList; 
	}

    function getTagPermalink($tag_url) {
		$trailing_slash = ($this->option['trailing_slash'] && $this->is_rewriteon) ? '/' : '';
		return ($this->base_url . $this->tag_name2url($tag_url) . $trailing_slash);
    }

	// Removes one tag from the URL. E.g. 100+200+300 and 200 shall be removed -> 100+300
	function removeTagFromURL($tag_url, $remove) {
		$tagarr = $this->tag_url2url_array($tag_url);
		$result = '';
		foreach ($tagarr as $var) {
			if ($var != $remove)
				$result .= ($result == '') ? $var : '+' . $var;
		}
		return $result;
	}

	####################################################################################################################
	########## PART 1: Functions for action and filter hooks  
	####################################################################################################################


	/**
	 * wpfilter_the_content
	 */ 
	function wpfilter_the_content($content) {

		// Remove "[tags]tag1, tag2[/tags]" from the content.
		if ($this->option['tag_embed_use']) {
			$regex = '/(' . $this->regexEscape($this->option['tag_embed_start']) . '(.*?)' . $this->regexEscape($this->option['tag_embed_end']) . ')/i';
			$content = preg_replace($regex, '', $content);
		}
		return $content;			

	}


	/**
	 * wpaction_InitRewrite
	 *
	 * Called by wordpress action hook 'init. We do not use this code in the
	 * constructor since we can't use WP rewrite flags until "init" hook.	  
	 */ 
    function wpaction_InitRewrite() {

		global $wp_rewrite;
        /* detect permalink type & construct base URL for local links */
        $this->base_url = get_settings('home') . '/';
        if (isset($wp_rewrite) && $wp_rewrite->using_permalinks()) {
            $this->is_rewriteon = true;                    // using rewrite rules
            $this->base_url .= $wp_rewrite->root;		// set to "index.php/" if using that style
            $this->base_url .= $this->option['query_varname'] . '/';
        } else {
            $this->base_url .= '?' . $this->option['query_varname'] . '=';
        }
        
        /* generate rewrite rules for tag queries */
        if ($this->is_rewriteon)
            add_filter('search_rewrite_rules', array(&$this, 'createRewriteRules'));
        
        /* flush rules if requested */
        $this->_initdone = true;
        if ($this->_flushrules) 
            $wp_rewrite->flush_rules();
    }

	/**
	 * wpfilter_GetPostIds
	 *
	 * Called by wordpress filter hook 'the_posts'. 	 
	 * Gets post IDs once WP query is done.
	 */ 
    function wpfilter_GetPostIds($posts) {
        /* extract list of post IDs from the posts array */
        if (!is_null($posts) && is_array($posts)) {
            foreach($posts as $p) {
                $this->_postids .= (!empty($this->_postids) ? ',' : '') . $p->ID;   //create comma-separated list
            }
        }
        return $posts;  //send'em back to WP
    }

	/**
	 * wpfilter_AddQueryVar
	 *
	 * Called by wordpress filter hook 'query_vars'. 	 
	 * Used for tag searches.
	 */ 
    function wpfilter_AddQueryVar($wpvar_array) {
        $wpvar_array[] = $this->option['query_varname'];
        return($wpvar_array);
    }

	/**
	 * wpaction_ParseQuery
	 *
	 * Called by wordpress action hook 'parse_query'. 	 
	 * Set the search tag if it's available. WP2.0's new rewrite rules 
	 * mean we need to grab it from the query vars.
	 */
    function wpaction_ParseQuery() {
        $this->search_tag = stripslashes(get_query_var($this->option['query_varname']));
        
		if (get_magic_quotes_gpc())
            $this->search_tag = stripslashes($this->search_tag);  // why so many freakin' slashes?

        // if this is a tag query, then reset other is_x flags and add query filters
        if ($this->search_tag != '') {
            global $wp_query;
            
			$wp_query->is_single = false;
            $wp_query->is_page = false;
            $wp_query->is_archive = false;
            $wp_query->is_search = false;
            $wp_query->is_home = false;
            $wp_query->is_paged = false; 
            add_filter('posts_where', array(&$this, 'wpfilter_posts_where'));
            add_filter('posts_join',  array(&$this, 'wpfilter_posts_join'));
            add_filter('posts_groupby',  array(&$this, 'wpfilter_posts_groupby'));
            add_action('template_redirect', array(&$this, 'wpaction_template_redirect'));

        }
    }

	/**
	 * wpfilter_CreateFeedCategories
	 * 
	 * Called by wordpress filter hook 'the_category_rss'.
	 * Insert tags into feeds as categories.
	 */
    function wpfilter_CreateFeedCategories($list, $type) {

        global $id;
        $post_tags = $this->getPostTags($id);
        
        foreach($post_tags as $tag) {
            if ($type == "rdf")
                $list .= "<dc:subject>$tag</dc:subject>";
            else
                $list .= "<category>$tag</category>";
        }
        return $list;   //send'em back to WP
    }


	/**
	 * wpaction_OutputHeader
	 * 
	 * Called by wordpress action hook 'meta_autoheader'.
	 * Automagically output meta tags in header.
	 */
    function wpaction_OutputHeader() {
		echo "\t" . '<meta name="keywords" content="' . $this->getMetaKeywords() . '" />';
    }


	/**
	 * wpfilter_posts_where
	 *
	 * update where clause to search on keywords table
	 */
    function wpfilter_posts_where($where) {
		$tagList = $this->tag_urls2names_IN($this->search_tag);	// comma separated list
		$this->search_tag_count =  count(explode(',', $tagList));
		$result = $where . " AND stptags.tag_name IN ($tagList)";
		
        // include pages in search
		if ($this->is_wp_21) {
			$result = str_replace('post_type = \'post\'', 'post_type IN(\'page\', \'post\')', $result);
		}
		$result = str_replace(' AND (post_status = \'publish\'', ' AND (post_status IN(\'static\', \'publish\')', $result); 
		return $result;

    }

	/**
	 * wpfilter_posts_join
	 *
	 * update where clause to search on keywords table
	 */
    function wpfilter_posts_join($join) {
        global $wpdb;
        $join .= " LEFT JOIN {$this->info['stptable']} AS stptags ON ({$wpdb->posts}.ID = stptags.post_id) ";
        return ($join);
    }
//echo '<div style="background: white; padding: 50px;">' . $join . '</div>';

	/**
	 * wpfilter_posts_groupby
	 */
    function wpfilter_posts_groupby($groupby) {
 		global $wpdb;
		$tagcount = $this->search_tag_count;
		$groupby = " {$wpdb->posts}.ID HAVING COUNT(ID) = $tagcount";
    	return $groupby; 
    }


	/**
	 * wpaction_template_redirect
	 *
	 * switch template when doing a keyword search
	 */
    function wpaction_template_redirect() {
        if ($this->is_tag_view()) {
            $template = '';
            
            if ( file_exists(TEMPLATEPATH . "/" . $this->option['template']) )
                $template = TEMPLATEPATH . "/" . $this->option['template'];
            else if ( file_exists(TEMPLATEPATH . "/tags.php") )
                $template = TEMPLATEPATH . "/tags.php";
            else
                $template = get_category_template();
            
            if ($template) {
                load_template($template);
                exit;
            }
        }
        return;
    }




	####################################################################################################################
	########## PART 2: Functions for template tags  
	####################################################################################################################

    function outputPostTags($linkformat=null, $include_cats=null, $tagseparator=null, $notagstext=null) {

		global $id;
		if ($id < 1 || ! is_int($id)) {
			global $post;
			$id = $post->ID;
		}

        // check parameters vs. class options
        $linkformat		= (is_null($linkformat))	? $this->option['post_linkformat']  : $linkformat;
        $include_cats	= (is_null($include_cats))	? $this->option['post_includecats'] : $include_cats;
        $tagseparator	= (is_null($tagseparator))	? $this->option['post_tagseparator'] : $tagseparator;
        $notagstext		= (is_null($notagstext))	? $this->option['post_notagstext'] : $notagstext;

        // create array of tags & full links
        $taglinks = array();
        if ($include_cats) {
            $categories = get_the_category();
            foreach($categories as $cat)
                $taglinks[ $cat->cat_name ] = get_category_link((int)$cat->cat_ID);
        }
        
        $post_tags = $this->getPostTags($id);
        foreach($post_tags as $tag) {
            $taglinks [ $tag ] = $this->getTagPermalink($tag);
        }
        
        // substitute values into link format
        $output = '';
        foreach($taglinks as $tag => $url) {
            $output .= (($output != '') ? $tagseparator : '') . 
                        $this->formatLink($tag, $url, $linkformat);
        }

        if (empty($output))
            return $notagstext;
        else
            return $output;

    }

    function createTagcloud($format=null, $tagseparator=null, $include_cats=null, $sort_order=null, $display_max=null, 
							$display_min=null, $scale_max=null, $scale_min=null, $notagstext=null ) {

        // check parameters vs. class options
        $format			= (is_null($format))		? $this->option['cloud_linkformat']		: $format;
        $tagseparator	= (is_null($tagseparator))	? $this->option['cloud_tagseparator']	: $tagseparator;
        $include_cats	= (is_null($include_cats))	? $this->option['cloud_includecats']	: $include_cats;
        $sort_order		= (is_null($sort_order))	? $this->option['cloud_sortorder']		: $sort_order;
        $display_max	= (is_null($display_max))	? $this->option['cloud_displaymax']		: $display_max;
        $display_min	= (is_null($display_min))	? $this->option['cloud_displaymin']		: $display_min;
        $scale_max		= (is_null($scale_max))		? $this->option['cloud_scalemax']		: $scale_max;
        $scale_min		= (is_null($scale_min))		? $this->option['cloud_scalemin']		: $scale_min;
        $notagstext		= (is_null($notagstext))	? $this->option['cloud_notagstext']		: $notagstext;


        // create array of tags & full links
        if ($include_cats) {
            $alltags = $this->getAllCombined();
        } else {
            $alltags = $this->getAllTags();
        }
        
        // limit results
        $limit = (int) $display_max;
        
		if (($limit > 0) && (count($alltags) > $limit)) {       
            $alltags = array_slice($alltags, 0, $limit);  // already in descending order
		}
        
        // re-sort results
        switch(strtolower($sort_order)) {
            case 'alpha':
                ksort($alltags);
                break;
            case 'countup':
            case 'asc':
                $alltags = array_reverse($alltags, true);       // reverse array order to be ascending
                break;
            case 'countdown':
            case 'desc':
                // already in descending order
                break;
            case 'random':
                srand((float)microtime() * 1000000);
                shuffle($alltags);                              // WARNING: keys not kept!
                break;
            default:    // case for 'natural'
                uksort($alltags, 'strnatcasecmp');
                break;
        }

        // scaling
        $do_scale = ($scale_max != 0);
        if ($do_scale) {
            $minval = $maxval = $alltags[ key($alltags) ]['count'];
            foreach($alltags as $tag) {
                $minval = min($tag['count'], $minval);
                $maxval = max($tag['count'], $maxval, $display_min);
            }
            $minval = max($minval, $display_min);
            $minout = max($scale_min, 0);
            $maxout = max($scale_max, $minout);
            $scale = ($maxval > $minval) ? (($maxout - $minout) / ($maxval - $minval)) : 0;
        }
        
        // scale counts & format links
        $output = '';
        foreach($alltags as $tag) {
            if ($tag['count'] >= $display_min) {
				$scaleResult = (int) (($tag['count'] - $minval) * $scale + $minout);
                $output .= (($output != '') ? $tagseparator : '') . 
                            $this->formatLink($tag['name'], $tag['link'], $format, $scaleResult, $tag['count'] 
                            );
				$output .= (substr($format,0,3) == '<li') ? "\n" : '';	// new line only if list is used
            }
        }

		if ( $output <> '' ) {
			$res = "\n" . '<!-- Generated by \'Simple Tagging Plugin ' . $this->stp_version . '\' - http://sw-guide.de/ -->' . "\n" . $output . "\n"; // Please do not remove this line.
			return $res;
		} else {
			return $notagstext;
		}

    } // function createTagcloud


	function createRelatedPostsList($format=null, $postsseparator=null, $sortorder=null, $limit_qty=null, $limit_days=null, 
									$dateformat=null, $nothingfound=null, $includepages=null) {
	
		global $id, $wpdb;
		if ($id < 1 || ! is_int($id)) {
			global $post;
			$id = $post->ID;
		}

		// check parameters vs. class options
        $format			= (is_null($format))		? $this->option['related_format'] : $format;
        $postsseparator	= (is_null($postsseparator))? $this->option['related_postsseparator'] : $postsseparator;
        $sortorder 		= (is_null($sortorder)) 	? $this->option['related_sortorder']: $sortorder;
        $limit_qty  	= (is_null($limit_qty)) 	? $this->option['related_limit_qty'] : $limit_qty;
        $limit_days 	= (is_null($limit_days))	? $this->option['related_limit_days']  : $limit_days;
        $dateformat		= (is_null($dateformat))	? $this->option['related_dateformat'] : $dateformat;
        $nothingfound	= (is_null($nothingfound))	? $this->option['related_nothingfound'] : $nothingfound;
        $includepages	= (is_null($includepages))	? $this->option['related_includepages'] : $includepages;


		// Get tags of current post
		$tags_arr = $this->getPostTags($id);
		if (empty ($tags_arr)) {
			return $nothingfound;
		}
		$tags_comma_separated = '';
		foreach($tags_arr as $loopval) { 
			$tags_comma_separated .= mysql_real_escape_string($loopval) . "','";
		}
		$tags_comma_separated = substr($tags_comma_separated, 0, -3);	// remove trailing ','
		
		// PREPARE ORDER
		switch (strtolower($sortorder)) {
			case 'alpha':
				$order_by = 'posts.post_title ASC';
				break;
			case 'date-asc':
				$order_by = 'posts.post_date ASC';
				break;
			case 'random':
				$order_by = 'RAND()';
				break;
			default:	// 'date-desc'
				$order_by = 'posts.post_date DESC';
		}


		// Set limit of posting date. 86400 seconds = 1 day
		$timelimit = '';
		if ($limit_days != 0) $timelimit = 'AND posts.post_date > ' . date('YmdHis', time() - $limit_days*86400);
	
		// Include pages ?
		if ($includepages) {
			$posts_pages = ($this->is_wp_21) ? "AND posts.post_type IN('page','post') AND posts.post_status = 'publish'"
											 : "AND posts.post_status IN('static','publish')";
		} else {
			$posts_pages = ($this->is_wp_21) ? "AND posts.post_type = 'post' AND posts.post_status = 'publish'"
											 : "AND posts.post_status = 'publish'";
		}

		// SQL query
		$qry = "SELECT DISTINCT posts.ID, posts.post_title, posts.post_date, posts.comment_count
				FROM {$wpdb->posts} posts, {$this->info['stptable']} tags
				WHERE posts.ID <> $id
				$posts_pages
				AND posts.post_date < '" . current_time('mysql') . "'
				AND tags.tag_name IN ('$tags_comma_separated')
				AND tags.post_id = posts.ID
				$timelimit
				ORDER BY " . $order_by . "
				LIMIT $limit_qty
				";
		$queryresult = $wpdb->get_results($qry);

	
		// RETURN LIST
		$permalist = '';
		if (count($queryresult) > 0) {
			foreach($queryresult as $tag_loop) {
				// Date of post
				$loop_postdate = mysql2date($dateformat, $tag_loop->post_date);
				// Replace placeholders
				$element_loop = $format;
				$element_loop = str_replace('%date%', $loop_postdate, $element_loop);
				$element_loop = str_replace('%permalink%', get_permalink($tag_loop->ID), $element_loop);
				$element_loop = str_replace('%title%', $tag_loop->post_title, $element_loop);
				$element_loop = str_replace('%commentcount%', $tag_loop->comment_count, $element_loop);

				// Add to list
                $permalist .= (($permalist != '') ? $postsseparator : '') . $element_loop;	// Incl. adding separator
				$permalist .= (substr($format,0,3) == '<li') ? "\n" : '';					// new line only if list is used

			}
			$res = "\n" . '<!-- Generated by \'Simple Tagging Plugin ' . $this->stp_version . '\' - http://sw-guide.de/ -->' . "\n" . $permalist . "\n"; // Please do not remove this line.
			return $res;
		} else {
			return $nothingfound;
		}
		
	} // createRelatedPostsList


	function outputRelatedTags($format=null, $tagseparator=null, $sortorder=null, $nonfoundtext=null) {

		global $wpdb;

		// check parameters vs. class options
        $format			= (is_null($format))		? $this->option['relatedtags_format'] 		: $format;
		$tagseparator	= (is_null($tagseparator))	? $this->option['relatedtags_tagseparator'] : $tagseparator;
        $sortorder		= (is_null($sortorder))		? $this->option['relatedtags_sortorder'] 	: $sortorder;
        $nonfoundtext	= (is_null($nonfoundtext))	? $this->option['relatedtags_nonfoundtext']	: $nonfoundtext;
		
		if ( !($this->is_tag_view()) ) {
			return $nonfoundtext;
		}

		
		// prepare values	
		$tagList = $this->tag_urls2names_IN($this->search_tag);			// comma separated list for IN()
		$tagListArray = $this->tag_url2url_array($this->search_tag);	// tag array
		$number_tags_base0 = $this->search_tag_count - 1;				// number of tags -1
		
		##########################
		# 1. We need all post IDs of all tags -- but in case of more tags only these which have all these tags 
		##########################
		$qry = "SELECT post_id FROM {$this->info['stptable']}
				WHERE tag_name IN ($tagList)
				GROUP BY post_id
				HAVING COUNT(post_id) > $number_tags_base0
				";
		$qryres = $wpdb->get_results($qry);

		if (count($qryres) < 1) {
			return $nonfoundtext;
		}

		// Convert to use in IN() 
		$postidsIN = '';
		foreach( $qryres as $loopval ) {
			$postidsIN .= ($postidsIN == '') ? $loopval->post_id : ',' . $loopval->post_id;
		}
	
		##########################
		# 2. We need all tags of these post IDs but not the ones that are already used
		##########################
		// prepare order
		switch (strtolower($sortorder)) {
			case 'count-desc':	$order_by = 'qty DESC';			break;
			case 'count-asc':	$order_by = 'qty ASC';			break;
			case 'alpha-desc':	$order_by = 'tag_name DESC';	break;
			default:			$order_by = 'tag_name ASC';		// 'alpha-asc'
		}

		$qry = "SELECT tag_name, COUNT(*) AS qty FROM {$this->info['stptable']}
				WHERE post_id IN ($postidsIN)
				AND tag_name NOT IN ($tagList)
				GROUP BY tag_name
				ORDER BY $order_by";			
		$qryres = $wpdb->get_results($qry);
	
		if (count($qryres) < 1) {
			return $nonfoundtext;
		}
	
		##########################
		# 3. Output
		##########################
		$result = '';
		foreach( $qryres as $loopval ) {
			// Tag Link
			$vr['taglink'] = $this->getTagPermalink($loopval->tag_name);

			// Tag Link + Additional Tag
			$current_url = $this->search_tag;	// Current URL
			$current_url = str_replace(' ', '+', $current_url);	// + was replaced by ' ', so restore that
			$current_url_add = $current_url . '+' . $loopval->tag_name; // Current URL + additional tag
			$vr['taglink_plus'] = $this->getTagPermalink($current_url_add); // Permalink with additional tag

			// Number of tags		
			$vr['count'] = $loopval->qty;

			// Tag Name
			$vr['tagname'] = $loopval->tag_name;

			// Format			
			$fmt_res = $format;
			$fmt_res = str_replace('%tagname%', $vr['tagname'], $fmt_res);
			$fmt_res = str_replace('%taglink%', $vr['taglink'], $fmt_res);
			$fmt_res = str_replace('%taglink_plus%', $vr['taglink_plus'], $fmt_res);
			$fmt_res = str_replace('%count%', $vr['count'], $fmt_res);
	
			// Result
			$result .= (($result != '') ? $tagseparator : '') . $fmt_res;	// Incl. adding separator
			$result .= (substr($format,0,3) == '<li') ? "\n" : '';			// new line only if list is used			

		}

		$result = "\n" . '<!-- Generated by \'Simple Tagging Plugin ' . $this->stp_version . '\' - http://sw-guide.de/ -->' . "\n" . $result . "\n"; // Please do not remove this line.
		return $result;
	
	}

	function outputRelatedTagsRemoveTags($format=null, $separator=null, $nonfoundtext=null) {

		// check parameters vs. class options
        $format		= (is_null($format))	? $this->option['relatedtags_remove_format'] : $format;
        $separator	= (is_null($separator))	? $this->option['relatedtags_remove_separator'] : $separator;
        $nonefound	= (is_null($nonefound))	? $this->option['relatedtags_remove_nonfoundtext'] : $nonefound;

		if ( !($this->is_tag_view()) ) {
			return $nonefound;
		}

		$tagListArray = $this->tag_url2url_array($this->search_tag);	// tag array

		if ($this->search_tag_count > 1) {

			$result = '';
			foreach ($tagListArray as $lval) {
				// Get Permalink
				$url = $this->removeTagFromURL($this->search_tag, $lval);	// remove current tag
				$url = $this->getTagPermalink($url); // permalink

				// Get Tag Name
				$name = $this->tag_url2name($lval);

				// Format
				$fmt_res = $format;
				$fmt_res = str_replace('%url%', $url, $fmt_res);
				$fmt_res = str_replace('%tagname%', $name, $fmt_res);

				// Result
				$result .= (($result != '') ? $separator : '') . $fmt_res;	// Incl. adding separator
				$result .= (substr($format,0,3) == '<li') ? "\n" : '';			// new line only if list is used

			}
			
			return $result;
		
		} else {
			return $nonefound;
		}
	
	}

	/**
	 * getMetaKeywords
	 *
	 * get meta keywords
	 */
    function getMetaKeywords($before='', $after='', $separator=',', $include_cats=null) {
        // add pre-defined keywords
        $pagekeys = explode(',', $this->option['meta_always_include']);
        
        // get tags for all posts in current view
        foreach($this->getPostTags() as $post_tags)
            $pagekeys = array_merge($pagekeys, $post_tags);
        
        // add categories if necessary
        if (is_null($include_cats))
            $include_cats = $this->option['meta_includecats'];
        if ($include_cats) {
            global $category_cache;
            if ( ($include_cats == 'all') || (($include_cats == 'default') && is_home()) ) {
				// include all site categories
                foreach($this->getAllCats() as $category)
                    $pagekeys[] = $category['name'];
            } elseif (isset($category_cache) && ($include_cats != 'none')) {
                // include only categories from posts in current view. Array is pretty encapsulated so we use several foreach loops. 
                foreach($category_cache as $arrayA) {
                    foreach($arrayA as $arrayB) {
						if ($this->is_wp_21) {	// In WP 2.1, the category is inside another array :-(
		                    foreach($arrayB as $category) {
								$pagekeys[] = $category->cat_name;
							}
						} else {
							$pagekeys[] = $arrayB->cat_name;
						}
					}
                }
            }
        }
        $pagekeys = array_unique($pagekeys);    // remove duplicates
        
        // setup meta keywords for page header
        $keywordlist = '';
        foreach($pagekeys as $keyword) {
            $keywordlist .= (($keywordlist != '') ? $separator : '') .
                                $before . $keyword . $after;
        }
        return htmlspecialchars($keywordlist);
    }



	####################################################################################################################
	########## PART 3: Misc
	####################################################################################################################

    function createRewriteRules($rewrite) {
        global $wp_rewrite;
        /* add rewrite tokens */
        $qvar =& $this->option['query_varname'];
        
		$token = '%' . $qvar . '%';
        $wp_rewrite->add_rewrite_tag($token, '(.+)', $qvar . '=');
        
        $tags_structure = $wp_rewrite->root . $qvar . "/$token";
        $tags_rewrite = $wp_rewrite->generate_rewrite_rules($tags_structure);

        return ( $rewrite + $tags_rewrite );
    }


	/**
	 * setOption
	 *
	 * update an option value  -- note that this will NOT save the options.
	 *
	 * @param string - name of the option
	 * @param string - option value
	 */ 
    function setOption($optname, $optval) {
        $this->option[$optname] = $optval;
    }


	/**
	 * saveOption
	 *
	 * Save all current options
	 */
    function saveOptions() {
        // Flush WP rewrite rules if query var has changed
        global $wp_rewrite;
        $qvar = 'query_varname';

		$optTable = get_option($this->stp_optname);	
        if ( $this->option[$qvar] != $optTable[$qvar] ) {
            if ($this->_initdone) {
                $wp_rewrite->flush_rules();
            } else {
                $this->_flushrules = true;
            }
        }

        // write current option values to the database
		update_option($this->stp_optname, $this->option);

    } // saveOptions

	/**
	 * resetToDefaultOptions
	 *
	 * Reset to default options
	 */
    function resetToDefaultOptions() {
		// Flush WP rewrite rules if query var has changed
        global $wp_rewrite;
        $qvar = 'query_varname';

		$default = $this->defaultoption;
        if ( $this->option[$qvar] != $default[$qvar] ) {
            if ($this->_initdone) {
                $wp_rewrite->flush_rules();
            } else {
                $this->_flushrules = true;
            }
        }

        // write option values to database
		update_option($this->stp_optname, $default);
		// set class options
		$this->option = $default;

    } // saveOptions


	/**
	 * getPostTags
	 *
	 * caches all tags matching current post selection
	 */
      function getPostTags($postid=null, $force=false) {

        if (is_null($this->_posttags) || $force) {
            if (empty($this->_postids)) {
                $this->_posttags = array(); // no posts, thus no tags
                return $this->_posttags;
            }
            
            global $wpdb;
			$q = "SELECT DISTINCT post_id, tag_name AS name
                    FROM {$this->info['stptable']} tags
                    WHERE post_id IN ({$this->_postids})
                    ORDER BY post_id, name";
            $tag_results = $wpdb->get_results($q);
            
            $post_tags = array();
            if (!is_null($tag_results) && is_array($tag_results)) {
                foreach ($tag_results as $tag) {
                    $post_id = $tag->post_id;
                    if (!isset($post_tags[$post_id]))
                            $post_tags[$post_id] = array();
                    $post_tags[$post_id][] = $tag->name;
                }
            }
            $this->_posttags =& $post_tags;
        }
        if (is_null($postid))
            return $this->_posttags;
        elseif (isset($this->_posttags[$postid]))
            return $this->_posttags[$postid];
        else
            return array();
    }

	/**
	 * getAllTags
	 *
	 * gets all published site tags
	 */
    function getAllTags($force=false) {
        if (is_null($this->_alltags) || $force) {
            global $wpdb;
            $table = $this->info['stptable'];
			$q = "SELECT tag.tag_name AS name, COUNT(tag.post_id) AS numposts
                FROM {$table} tag
                INNER JOIN {$wpdb->posts} p ON tag.post_id=p.id
                WHERE (p.post_status='publish' OR p.post_status='static')
                  AND p.post_date_gmt<='" . gmdate("Y-m-d H:i:s", time()) . "'
                GROUP BY tag.tag_name
                ORDER BY numposts DESC ";
            $tag_results = $wpdb->get_results($q);
            
            $alltags = array();
            if (!is_null($tag_results) && is_array($tag_results)) {
                foreach ($tag_results as $tag) {
                    $alltags[$tag->name] = array('name' => $tag->name,
                                                 'count'=> $tag->numposts,
                                                 'link' => $this->getTagPermalink($tag->name)
                                                );
                }
            }
            $this->_alltags =& $alltags;
        }
        return $this->_alltags;
    }

	/**
	 * getAllCats
	 *
	 * gets all published site categories (same format as getAllTags)
	 */
    function getAllCats($force=false) {
 
        if (is_null($this->_allcats) || $force) {
            global $wpdb;
            $q = "SELECT p2c.category_id AS cat_id, COUNT(p2c.rel_id) AS numposts,
                    UNIX_TIMESTAMP(max(p.post_date_gmt)) + '" . get_option('gmt_offset') . "' AS last_post_date,
                    UNIX_TIMESTAMP(max(p.post_date_gmt)) AS last_post_date_gmt
                FROM {$wpdb->post2cat} p2c
                INNER JOIN {$wpdb->posts} p ON p2c.post_id=p.id
                WHERE (p.post_status='publish' OR p.post_status='static')
                  AND p.post_date_gmt<='" . gmdate("Y-m-d H:i:s", time()) . "'
                GROUP BY p2c.category_id
                ORDER BY numposts DESC ";
            $results = $wpdb->get_results($q);
            
            $allcats = array();
            if (!is_null($results) && is_array($results)) {
                foreach ($results as $cat) {
                    $catname = get_catname($cat->cat_id);
                    $allcats[$catname] = array('name' => get_catname($cat->cat_id),
                                               'count'=> $cat->numposts,
                                               'link' => get_category_link((int)$cat->cat_id)
                                              );
                }
            }
            $this->_allcats =& $allcats;
        }
        return $this->_allcats;
    }

	/**
	 * getAllCombined
	 *
	 * combines all published tags & categories
	 */
    function getAllCombined($force=false) {

        if (is_null($this->_allcombined) || $force) {
            $combined = array_merge($this->getAllTags(), $this->getAllCats());
            uasort($combined, array(&$this, 'sortCombined'));
            $this->_allcombined =& $combined;
        }
        return $this->_allcombined;
    }

    function sortCombined($a, $b) {
        /* sort by descending count, ties broken by nat case ascending name */
        if ($a['count'] == $b['count']) 
            return strnatcasecmp($a['name'], $b['name']);
        else
            return ( ($a['count'] > $b['count']) ? -1 : 1 );
    }
    
	/**
	 * formatLink
	 *
	 * substitute values into link format
	 */
    function formatLink($name, $full_link, $format, $scale=0, $count=0) {
        /* substitute values into link format */
        $newlink = $format;
        $newlink = str_replace('%tagname%', $name, $newlink);
        $newlink = str_replace('%taglink%', $this->tag_name2url($name), $newlink);
        $newlink = str_replace('%flickr%', $this->flickrLink($name), $newlink);
        $newlink = str_replace('%delicious%', $this->deliciousLink($name), $newlink);
        $newlink = str_replace('%fulltaglink%', $full_link, $newlink);
		$newlink = str_replace('%scale%', $scale, $newlink);
		$newlink = str_replace('%count%', $count, $newlink);
		return $newlink;
    }
    function flickrLink($tag) {
        return urlencode(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($tag)));
    }
	function deliciousLink($tag) {
        $del = str_replace(' ', '', $tag);	// Strip whitespace
        if (strstr($del, '+'))
            $del = '"' . $del . '"';
        return str_replace('%2F', '/', rawurlencode($del));
    }

	/**
	 * is_tag_view
	 *
	 * Use this to check if the current view will be returning tag search results.
	 * Returns TRUE if a tag search was requested, FALSE otherwise.
	 */
	function is_tag_view() {
        if (!is_null($this->search_tag) && ($this->search_tag != ''))
            return true;
        else
            return false;
    }

	/**
	 * regexEscape
	 *
	 * Escape string so that it can used in Regex. E.g. used for [tags]...[/tags]
	 */
	function regexEscape($str) {
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('/', '\\/', $str);
		$str = str_replace('[', '\\[', $str);
		$str = str_replace(']', '\\]', $str);
	
		return $str;
	}


} // CLASS


?>