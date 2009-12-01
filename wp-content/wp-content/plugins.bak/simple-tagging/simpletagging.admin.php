<?php

class SimpleTaggingAdmin {

	var $sTagObj;	// Contains SimpleTagging Object

	var $import;	// Import Variables


	// Constructor
    function SimpleTaggingAdmin($sTagObj) {

		global $table_prefix;

		// Set object
        $this->sTagObj = $sTagObj;

		// Set Import Variables
		$this->import = array(
			// UTW
			'UTW_tTags'			=> $table_prefix . 'tags',
			'UTW_tTagsID'		=> 'tag_ID',
			'UTW_tTagsName'		=> 'tag',
			'UTW_tP2T'			=> $table_prefix . 'post2tag',
			'UTW_tP2TtagID'		=> 'tag_id',
			'UTW_tP2TpostID'	=> 'post_id'
		);
		
		// add WP filters
		if (is_admin()) {
			add_filter('simple_edit_form',	array(&$this, 'showTagEntry'));
			add_filter('edit_form_advanced',array(&$this, 'showTagEntry'));
			add_filter('edit_page_form',	array(&$this, 'showTagEntry'));
		}

		// add WP actions - not limited to is_admin() to be applied also in case of xmlrpc posts by external blogging application
		add_action('publish_post',		array(&$this, 'savePostTags'));
		add_action('edit_post',			array(&$this, 'savePostTags'));
		add_action('save_post',			array(&$this, 'savePostTags'));
		add_action('wp_insert_post',	array(&$this, 'savePostTags'));
		add_action('delete_post', 		array(&$this, 'wpaction_delete_post'));

		// add menu
		if (is_admin()) {
			add_action('admin_menu', array(&$this, 'wpaction_admin_menu'));
		}

	    // check if we need to upgrade
		if (is_admin()) {
			if ( $this->sTagObj->option['version_for_install'] < $this->sTagObj->stp_version  ) {
				// Execute installation
				$this->install();
				// Update version number in the options
				$this->sTagObj->setOption('version_for_install', $this->sTagObj->stp_version);
				$this->sTagObj->saveOptions();
			}
		}

	}

    function wpaction_delete_post($postid) {
		$this->deleteTagsByPostID($postid);
	}


    function wpaction_admin_menu() {
		add_menu_page('Simple Tagging', 'Tags', 8, __FILE__, array(&$this, 'stp_menu_options'));
		add_submenu_page(__FILE__, 'Simple Tagging: Options', 'Tag Options', 8, __FILE__, array(&$this, 'stp_menu_options'));
		add_submenu_page(__FILE__, 'Simple Tagging: Manage Tags', 'Manage Tags', 8, 'simple-tagging_manage', array(&$this, 'stp_menu_manage'));
		add_submenu_page(__FILE__, 'Simple Tagging: Not Tagged Articles', 'Not Tagged Articles', 8, 'simple-tagging_not-tagged', array(&$this, 'stp_menu_not_tagged'));
		add_submenu_page(__FILE__, 'Simple Tagging: Import Tags', 'Import Tags', 8, 'simple-tagging_import', array(&$this, 'stp_menu_import'));
	}


    function getAllTags($sort='desc') {
        /* get all tags from the db */
        global $wpdb;
        $all_tags = array();
        
        switch($sort) {
            case 'asc':
                $orderby = 'tag_count';
                break;
            case 'desc':
                $orderby = 'tag_count DESC';
                break;
            default:
                $orderby = 'tag_name';
                break;
        }
        $tablename = $this->sTagObj->info['stptable'];
        $tags = $wpdb->get_results("SELECT tag_name, COUNT(post_id) AS tag_count
                                    FROM {$tablename}
                                    GROUP BY tag_name ORDER BY $orderby ");
        if (is_array($tags)) {
            foreach($tags as $t)
                $all_tags[$t->tag_name] = $t->tag_count;
            
            switch($sort) {
                case 'natural':
                    uksort($all_tags, 'strnatcasecmp');
                    break;
                default:
                    //do nothing
                    break;
            }
        }
        
        return $all_tags;
    }

    function getPostTags($id) {
        /* get all tags for the specified post from the db */
        global $wpdb;
        $post_tags = array();
        
		$tablename = $this->sTagObj->info['stptable'];
		$tags = $wpdb->get_results("SELECT tag_name FROM {$tablename} WHERE post_id='$id'");
       
        if (is_array($tags)) {
            foreach($tags as $t)
                $post_tags[] = $t->tag_name;
        }
        return $post_tags;
    }
    
    // Display tag entry & suggested tag fields
    function showTagEntry() {
        
        global $post, $wpdb;

	   	################################ 
        # prepare options
        ################################
		// max number of tags to be displayed
		$opt['max_suggest'] = intval( $this->sTagObj->option['admin_max_suggest'] );
		if($opt['max_suggest'] == 0) $opt['max_suggest'] = 99999;	// if user wants no limit
		// display suggested tags or all tags?
		$opt['dsp_suggested'] = $this->sTagObj->option['admin_tag_suggested'];
		// sorting
		$opt['tag_sort'] = $this->sTagObj->option['admin_tag_sort'];
		
	   	################################ 
        # get tags of this post and all tags
        ################################
		$tags = $this->getPostTags($post->ID);
        $post_tags = implode(', ', $tags);
        $all_tags = $this->getAllTags('desc');

	   	################################ 
        # create array of tags to be displayed
        ################################
		$result_tags = array();
        foreach($all_tags as $tag => $count) {
            if (!in_array($tag, $tags)) {
				if ( $opt['dsp_suggested'] ) {
					// only add tag if it is somewhere used in post content
					// added check for $tag not empty since some users got error message "Warning: stristr() [function.stristr]: Empty delimiter"				
					if ( is_string($tag) && $tag != '' && stristr($post->post_content, $tag) ) $result_tags[] = $tag;

				} else {
					$result_tags[] = $tag;
				}
				if ( count($result_tags) >= $opt['max_suggest'] )
					break;		
			}
		}
        
		// Add more tags if 'suggested tags' should be displayed but not enough tags were found
        if ( (count($result_tags) < $opt['max_suggest']) && $opt['dsp_suggested'] ) {
            foreach($all_tags as $tag => $count) {
                if (!in_array($tag, $tags) && !in_array($tag, $result_tags)) {
                    $suggested[] = $tag;
                    if (count($suggested) >= $opt['max_suggest'])
						break;
                }
            }
        }
                
        if (count($result_tags) > 0) {

			if (strtolower($opt['tag_sort']) == 'alpha')
				natcasesort($result_tags);			

            $result_tags_str = '<span onclick="javascript:addTag(this.innerHTML);">' . implode('</span> <span onclick="javascript:addTag(this.innerHTML);">', $result_tags) . '</span>';

        } else {
            $result_tags_str = 'No (suggested) tags founds.';
		}

        // TODO: add word boundaries to the regexp, or use a global array to store
        //       already added tags, too tired now and too many years since I last
        //       used JS --ludo
        echo '
		<style type="text/css">
			/* Style for tag section */
	        #stp_tag_entry { width: 98%; }
	        #stp_taglist { margin: 3px 0 0 1%; }
	        #stp_taglist h4 { margin: 0; padding: 0 0 2px 0; font-weight: normal; font-size: 10pt}
	        #stp_taglist p { padding: 0; margin: 0}
			#stp_taglist span { font-size: 90%; display: block; float: left; background-color: #f0f0ee; padding: 0px 1px 0px 1px;
	            margin: 1px; border: solid 1px; border-color: #ccc #999 #999 #ccc; color: #333; cursor: pointer; }
	        #stp_taglist span:hover { color: black; background-color: #b6bdd2; border-color: #0a246a; }
			#stp_taglist div#clearer { clear:both; line-height: 1px; font-size: 1px; height: 5px; }
	
			/* Style for Type Ahead (Wick) */ 
			table.floater { position:absolute; z-index:1000; display:none; padding:0; margin:0; }
			table.floater td { font-family: Gill, Helvetica, sans-serif; background-color:white; border:1px inset #979797; color:black; } 
			.matchedSmartInputItem { font-size:0.8em; padding: 5px 10px 1px 5px; margin:0; cursor:pointer; }
			.selectedSmartInputItem { color:white; background-color:#3875D7; }
			#smartInputResults { padding:0; margin:0; }
			.siwCredit { margin:0; padding:0; margin-top:10px; font-size:0.7em; color:black; }  
        </style>

		<!-- Hack for IE incl. IE7, since the dropdown is not placed properly -->		
			<!--[if IE]>
				<style type="text/css"> table.floater { position:static; }	</style>
			<![endif]-->

        <script type="text/javascript">
        if(document.all && !document.getElementById) {
            document.getElementById = function(id) { return document.all[id]; }
        }
        function addTag(tag) {
            var stp_tag_entry = document.getElementById("stp_tag_entry");
            if (stp_tag_entry.value.length > 0 && !stp_tag_entry.value.match(/,\s*$/))
                stp_tag_entry.value += ", ";
            var re = new RegExp(tag + ",");
            if (!stp_tag_entry.value.match(re))
                stp_tag_entry.value += tag + ", ";
        }
        </script>
		';

        
		/* Prepare Type-Ahead */
		$tablename = $this->sTagObj->info['stptable'];
		$ta_alltags = $wpdb->get_col("SELECT DISTINCT tag_name FROM {$tablename}");
        $ta_result = '';
		if (is_array($ta_alltags)) {
			foreach($ta_alltags as $t) {
				$ta_alltags2[] = '\'' . $t . '\'';
			}
			$ta_result = implode(',', $ta_alltags2);
			$ta_result = '//<![CDATA[
				collection = [' . $ta_result . '];
//]]>';
        }

		// display tag entry fields
		// the hidden field is there due to http://markjaquith.wordpress.com/2007/01/28/authorization-and-intentionorigination-verification-when-using-the-edit_post-hook/
        echo '
		<div id="lptagstuff" class="dbx-group">
            <fieldset class="dbx-box" id="posttags">
                <h3 class="dbx-handle">' . __('Tags (comma separated list)') . '</h3>
                <div class="dbx-content">
					<input class="wickEnabled" name="tag_list" id="stp_tag_entry" value="' . $post_tags . '" />
					<input type="hidden" name="simpletaggingverifykey" id="simpletaggingverifykey" value="' . wp_create_nonce('simpletagging') . '" />' . '
					<script type="text/javascript" language="JavaScript">' . $ta_result . '</script>
					<script type="text/javascript" language="JavaScript" src="' . $this->sTagObj->info['install_url'] . '/simpletagging.type-ahead.js"></script>

					<div id="stp_taglist">
						<h4>' . (($opt['dsp_suggested']) ? 'Suggested Tags' : 'Tags') . ':</h4>
						<p>' . $result_tags_str . '</p>
						<div id="clearer">&nbsp;</div>
					</div>
				</div>
            </fieldset>
        </div>
		';
    }

    function saveTag($id, $tag) {
		global $wpdb;
		$wpdb->query("INSERT IGNORE INTO {$this->sTagObj->info['stptable']} VALUES ('$id', '$tag')");
    }


   /**
	* Retrieves embedded tags ([tags]tag1, tag2[/tags]) from the post
	*/
	function getEmbeddedTags($postID) {
		
		$post = &get_post($postID);
		$postContent = $post->post_content;

		$regex = '/(' . $this->sTagObj->regexEscape($this->sTagObj->option['tag_embed_start']) . '(.*?)' . $this->sTagObj->regexEscape($this->sTagObj->option['tag_embed_end']) . ')/i';

		// Return Tags
		preg_match_all($regex, $postContent, $matches);
		$tags = array();
		foreach ($matches[2] as $match) {
			foreach(explode(',', $match) as $tag) {
				$tags[] = $tag;
			}
		}

		return $tags;

	}


	/* save new list of post tags to database */
    function savePostTags($id) {
		
        global $wpdb;

		#### Added due to <http://markjaquith.wordpress.com/2007/01/28/authorization-and-intentionorigination-verification-when-using-the-edit_post-hook/>
		#### However, also added "XMLRPC_REQUEST" to be able to use embedded tags [tags]...[/tags] 
			// authorization
			if ( !current_user_can('edit_post', $id) )
				return $id;
			// origination and intention
			if ( ! ( wp_verify_nonce($_POST['simpletaggingverifykey'], 'simpletagging') || ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )  ) )
				return $id;
		##################


        // clear old values first
		$this->deleteTagsByPostID($id);

		// Retrieve post content (tags between [tags] and [/tags]) as list
		$tags_embedded = ( $this->sTagObj->option['tag_embed_use'] ) ? implode(',', $this->getEmbeddedTags($id)) : '';

		// Retrieve tags entered in field
		$tags_entered = $_REQUEST['tag_list'];
				
		// Merge 'em
		$tag_list = $tags_embedded . ',' . $tags_entered;
		
		// Replace some stuff since we will not allow _ and + etc.
		$tag_list = $this->sTagObj->tag_convertUserInput($tag_list);
		
		// Consider slashes
		$tag_list = ( get_magic_quotes_gpc() ) ? $tag_list : addslashes($tag_list);

		// convert to array & remove duplicate values
		$tag_list = array_unique(explode(',', $tag_list));

		foreach($tag_list as $tag) {
			$tag = trim($tag);
			if (!empty($tag)) {
				$this->saveTag($id, $tag);
			}
		}
	}
    
	// Deletes all tags of a post according passed post id.
    function deleteTagsByPostID($postid) {
		
		global $wpdb;

		if ( is_numeric($postid) || $postid > 0 ) { 
			$wpdb->query("DELETE FROM {$this->sTagObj->info['stptable']} WHERE post_id='$postid'");
			return true;
		} else {
			return false;
		}

	}


	// Deletes list of tags from the database
    function deleteTagsByTagList($todelete) {

        global $wpdb;
        
        /* split list of tags */
        $old_tags = array_unique(explode(',', $todelete));
        $old_list = '';
        foreach($old_tags as $key=>$tag) {
            if (!empty($old_list))
                $old_list .= ',';
            $old_list .= "'" . addslashes(trim($tag)) . "'";
        }
        
        /* delete old tags */
		$tablename = $this->sTagObj->info['stptable'];
		if ($wpdb->query("DELETE FROM {$tablename} WHERE tag_name IN ($old_list)") > 0)
            return "Deleted the following tag(s): $todelete";
        else
            return "Could not find tag(s) in database: $todelete";
    }


	// Resaves list of old tags to new value(s) or adds new tag to all posts ($rename=false)
    function updateTags($old, $new, $rename=true) {

        global $wpdb;
		$tablename = $this->sTagObj->info['stptable'];

        
        // split lists of old & new tags
        $old_tags = array_unique(explode(',', $old));
        $old_list = '';
        foreach($old_tags as $tag) {
            if (!empty($old_list))
                $old_list .= ',';
            $old_list .= "'" . addslashes(trim($tag)) . "'";
        }
        if (trim(str_replace(',', '', stripslashes($new))) == '')
            return ('No new tag specified!'); 
        $new_tags = array_unique(explode(',', $new));
        
        // Get list of posts matching old tags
		if ($old == '' && $rename === false) {
			// User wants the tag(s) to be added to all published posts
			$posts_arg = ($this->sTagObj->is_wp_21) ? "AND posts.post_type = 'post' AND posts.post_status = 'publish'"
													 : "AND posts.post_status = 'publish'";
			$qry = "SELECT DISTINCT posts.ID as the_id FROM {$wpdb->posts} posts
					WHERE posts.post_date < '" . current_time('mysql') . "'
					$posts_arg ";
		} else {
			// Only add tags to the posts which are tagged with the old tag(s)
			$qry = "SELECT DISTINCT post_id as the_id FROM {$tablename}
					WHERE tag_name IN ($old_list)
					GROUP BY post_id";
		}
		
		$posts = $wpdb->get_results($qry);

        if (is_array($posts) && (count($posts) > 0)) {
            if ($rename) {
                // delete old tags
				$wpdb->query("DELETE FROM {$tablename} WHERE tag_name IN ($old_list)");
            }
            
            // save new tags
            foreach ($posts as $p) {
                foreach($new_tags as $tag) {
                    $tag = addslashes(trim($tag));
                    // check if tag already exists for post before saving
					if ($wpdb->query("SELECT post_id, tag_name FROM {$tablename}
                                      WHERE tag_name='$tag' AND post_id='{$p->the_id}'") <= 0)
                        $this->saveTag($p->the_id, $tag);
                }
            }
            if ($rename) {
                return "Renamed tag(s) &laquo;$old&raquo; to &laquo;$new&raquo;";
            } elseif ($old == '' && $rename === false) {
                return "Added tag(s) &laquo;$new&raquo; to all posts.";
            } else {
                return "Added tag(s) &laquo;$new&raquo; to posts tagged with &laquo;$old&raquo;";
			}
        } else {
            return "No posts found matching tag(s): $old";
        }
    }
    
    function install() {
        global $wpdb;

        /* create tags table if it doesn't exist */
		$tablename = $this->sTagObj->info['stptable'];
        $found = false;
        foreach ($wpdb->get_results("SHOW TABLES;", ARRAY_N) as $row) {
            if ($row[0] == $tablename) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $res = $wpdb->get_results("CREATE TABLE $tablename " . $this->sTagObj->tablestruct);

        }
    }

	function STP_ConvertCatsToTags() {
		
		global $wpdb;
		
		// Get an array of all categories, e,g. ([cat_name] => Wordpress, [post_id] => 81) ([cat_name] => Wordpress, [post_id] => 82), ([cat_name] => Gadget, [post_id] => 82), ([cat_name] => Apps, [post_id] => 82)
		$qry = "	SELECT cats.cat_name, p2c.post_id FROM $wpdb->posts posts
					INNER JOIN $wpdb->categories cats ON (p2c.category_id = cats.cat_id) 
					LEFT JOIN $wpdb->post2cat p2c ON (posts.ID = p2c.post_id)
					WHERE posts.post_status IN ('publish')
					AND posts.post_type <> 'page'
					"; 
		$sqlres = $wpdb->get_results($qry);

		if (count($sqlres) > 0) {
			$count = 0;
			$queryInsert = '';
			foreach($sqlres as $loopval) {
				
				// Prepare values
				$postid = $loopval->post_id;
				$catname = $loopval->cat_name;
				$catname = str_replace(',', '.', $catname); // remove comma, for some reasons does Wordpress allow commas in categories
				$catname = $this->sTagObj->tag_convertUserInput($catname); // Convert several other chars we don't allow

				// Create query
				if ( $postid != '' && $catname != '' ) {
					$count++;
					$addquote = ($count == 1) ? '' : ',';
					$queryInsert .= "$addquote($postid,'$catname')";
				}
			}

			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "	INSERT IGNORE INTO $table ( post_id, tag_name )
							VALUES $queryInsert";
				$wpdb->query($query);
			}

			return 'Converting categories to tags was successful, ' . $count .  ' categories converted :)';

		} else {
		
			return 'No categories found so nothing was converted.';
		
		}
				




		
		
	} // function STP_ConvertCatsToTags() {
	
	


	function STP_ImportFromJK19() {
	
		global $wpdb;
	
        $qry = "SELECT post_id, meta_id, meta_key, meta_value
                FROM  {$wpdb->postmeta} meta
                WHERE meta.meta_key = 'keywords'";
        $metakeys = $wpdb->get_results($qry);
        if (count($metakeys) > 0) {
			$count = 0;
			$queryInsert = '';
            foreach($metakeys as $post_meta) {
                if ($post_meta->meta_value != '') {
                    $post_keys = explode(',', $post_meta->meta_value);
                    foreach($post_keys as $keyword) {
                        $keyword = addslashes(trim($keyword));
                        if ($keyword != '') {
							
							// Prepare values
							$keyword = trim($keyword);
							$keyword = $this->sTagObj->tag_convertUserInput($keyword); // Convert several other chars we don't allow
							$post_id = $post_meta->post_id;

							// Create query
							if ( $keyword != '' && $post_id != '' ) {
								$count++;
								$addquote = ($count == 1) ? '' : ',';
								$queryInsert .= "$addquote($post_id,'$keyword')";
							}

						}
                    } // foreacg
                } // if
			} // foreach
			
			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "	INSERT IGNORE INTO $table ( post_id, tag_name )
							VALUES $queryInsert";
				$wpdb->query($query);
			}			
		
			return 'Importing Tags of Jerome\'s Keywords 1.9 was successful, ' . $count .  ' tags imported :)';
		
		} else {
		
			return 'No tags found so nothing was imported.';
		
		}

		
	
	} // function STP_ImportFromJK19

	function STP_ImportFromJK20Beta() {
	
		global $wpdb, $table_prefix;

		$jk2table = $table_prefix . 'jkeywords';
        $qry = "SELECT post_id, tag_name
                FROM  {$jk2table} ";
        $sql_res = $wpdb->get_results($qry);

        if (count($sql_res) > 0) {

			$count = 0;
			$queryInsert = '';
            foreach($sql_res as $entry) {

				// Prepare values
				$post_id = $entry->post_id;
				$tag = $entry->tag_name;
				$tag = $this->sTagObj->tag_convertUserInput($tag); // Convert several other chars we don't allow
				
				// Create query
				if ( $post_id != '' && $tag != '' ) {
					$count++;
					$addquote = ($count == 1) ? '' : ',';
					$queryInsert .= "$addquote($post_id,'$tag')";
				}
			}

			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "	INSERT IGNORE INTO $table ( post_id, tag_name )
							VALUES $queryInsert";
				$wpdb->query($query);
			}

			return 'Importing Tags from Jerome\'s Keywords 2.0 Beta was successful, ' . $count .  ' tags imported :)';

		} else {
		
			return 'No tags found so nothing was imported.';
		
		}

	
	} // function STP_ImportFromJK20Beta



	function STP_ImportFromUTW() {
		
		global $wpdb;
	
		$UTW_tTags			= $this->import['UTW_tTags'];
		$UTW_tTagsID		= $this->import['UTW_tTagsID'];
		$UTW_tTagsName		= $this->import['UTW_tTagsName'];
		$UTW_tP2T			= $this->import['UTW_tP2T'];
		$UTW_tP2TtagID		= $this->import['UTW_tP2TtagID'];
		$UTW_tP2TpostID		= $this->import['UTW_tP2TpostID'];
		
		$query = "	SELECT DISTINCT p2t.$UTW_tP2TpostID, t.$UTW_tTagsName
					FROM $UTW_tTags t
					INNER JOIN $UTW_tP2T p2t ON p2t.$UTW_tP2TtagID = t.$UTW_tTagsID
					ORDER BY p2t.$UTW_tP2TpostID ASC 
					";
		$sql_res = $wpdb->get_results($query);


        if (count($sql_res) > 0) {	
			$count = 0;
			$queryInsert = '';
			foreach ($sql_res as $loopval) {

				// Prepare values
				$lpID = $loopval->$UTW_tP2TpostID;
				$lpName = $loopval->$UTW_tTagsName;
				$lpName = str_replace('-', ' ', $lpName); // UTW uses minus ("-") instead of space
				$lpName = str_replace('_', '-', $lpName); // We do not allow underscore "_"
				$lpName = trim($lpName);
				$lpName = $this->sTagObj->tag_convertUserInput($lpName); // Convert several other chars we don't allow
	
				// Create query
				if ( $lpID != '' && $lpName != '' ) {
					$count++;
					$addquote = ($count == 1) ? '' : ',';
					$queryInsert .= "$addquote($lpID,'$lpName')";
				}
	
			}

			if ( $count > 0 ) {
				// Write values into STP table
				$table = $this->sTagObj->info['stptable'];
				$query = "	INSERT IGNORE INTO $table ( post_id, tag_name )
							VALUES $queryInsert";
				$wpdb->query($query);
			}
		
			return 'Importing Tags from Ultimate Tag Worrior was successful, ' . $count .  ' tags imported :)';

		} else {
			
			return 'No tags found so nothing was imported.';
		
		}
	
	
	} // function STP_ImportFromUTW
    
        
   	######################################################################################################################
    # ADMIN MENU: OPTIONS 
   	######################################################################################################################
	function stp_menu_options() {

		// presentation data for each configurable option
		$option_data = array(
			'General Options' => array(
			    array('query_varname', 'Tag search base:', 'text', 40, 'Please don\'t enter a leading or trailing slash. <br />Output: ' . htmlspecialchars($this->sTagObj->base_url)),
				array('trailing_slash', 'Include trailing slash on tag urls:', 'checkbox', '1',
			            'This will add a trailing slash on tag urls, so "<code>/tag/sometag</code>" becomes "<code>/tag/sometag/</code>".
						It will work only if you are using <a href="http://codex.wordpress.org/Using_Permalinks">pretty permalinks</a>.'),
				array('usehyphen', 'Use hyphens as space separator:', 'checkbox', '1', 'This will convert spaces to hyphens "-" instead of underscores "_".
					A tag name like \'Hello World\' is per default converted to the URL \'Hello_World\', this makes it possible that you 
					can also use hyphens "-" in tag names like \'plug-in\', \'re-import\' etc. However, if you enable this option, then spaces 
					will be replaced with hyphens "-", but this will also disable the use of hyphens in tag names and will convert tags like \'re-import\'
					into \'re import\'.'),
			    array('template',      'Search results template file:', 'text', 40, 
			            'Create a template file with this name in your theme\'s directory to display custom
			            results. Otherwise, search tag results will use \'tags.php\' if it exists, or your category template.'),

			),
			'Feed Options' => array(
				array('use_feed_cats', 'Include tags as categories in feeds:', 'checkbox', '1',
			            'This will index your tags with <a href="http://technorati.com/tag">Technorati</a>.')
			),
			'Meta Keyword Options' => array(
				array('meta_autoheader', 'Automatically include in header:', 'checkbox', '1',
			            'Includes the meta keywords tag automatically in your header (most, but not all, themes support this). These keywords are sometimes used by search engines.'),
			    array('meta_always_include', 'Always add these keywords:', 'text', 80),
			    array('meta_includecats', 'Include categories as keywords:', 'dropdown', 'default/all/none', 
			            "'Default' = includes all categories on the homepage, and only categories for posts in the
			            current view for all other pages.<br/>'All' = includes all categories in every view.<br/>
			            'None' = never includes categories in the meta keywords.")	
			),
			'Display Tags of the Current Post' => array(
			    array('post_linkformat', 'Post tag link format:', 'text', 80, 
						'<ul>
							<li><code>%tagname%</code> &ndash; Replaced by the name of the tag, e.g. <em>Coffee and Tea</em>.</li>
							<li><code>%fulltaglink%</code> &ndash; Replaced by the full link, e.g. <em>http://site.com/tag/Coffee+and+Tea</em>.</li>
							<li><code>%taglink%</code> &ndash; Replaced by the link, e.g. <em>Coffee_and_Tea</em>. Can be used to create links e.g. to Technorati.</li>
							<li><code>%flickr%</code> &ndash; Replaced by the link for Flickr compatibility, e.g. <em>coffeeandtea</em>.</li>
							<li><code>%delicious%</code> &ndash; Replaced by the link for de.icio.us compatibility, e.g. <em>CoffeeandTea</em>.</li>
						</ul>'),
						
			    array('post_tagseparator', 'Post tag separator string:', 'text', 40),
			    array('post_includecats', 'Include categories in tag list:', 'checkbox', '1'),
			    array('post_notagstext', 'Text to display if no tags found:', 'text', 80)
			),
			'Tag Cloud' => array(
			    array('cloud_linkformat', 'Cloud tag link format:', 'text', 80, 
						'<ul>
							<li><code>%tagname%</code> &ndash; Replaced by the name of the tag.</li>
							<li><code>%fulltaglink%</code> &ndash; Replaced by the full link, e.g. <em>http://site.com/tag/Coffee+and+Tea</em>.</li>
							<li><code>%taglink%</code> &ndash; Replaced by the link, e.g. <em>Coffee_and_Tea</em>. Can be used to create links e.g. to Technorati.</li>
							<li><code>%flickr%</code> &ndash; Replaced by the encoded version of the tag which conforms to Flickr link standards., e.g. <em>coffeeandtea</em>.</li>
							<li><code>%delicious%</code> &ndash; Replaced by the encoded version of the tag which conforms to del.icio.us link standards., e.g. <em>coffeeandtea</em>.</li>
							<li><code>%count%</code> &ndash; Replaced by the actual number of times the tag has been used.</li>
							<li><code>%scale%</code> &ndash; Replaced by the value the tag is scaled to.</li>
						</ul>'),
			    array('cloud_tagseparator', 'Cloud tag separator(s):', 'text', 80),
			    array('cloud_includecats', 'Include categories in tag cloud:', 'checkbox', '1'),
			    array('cloud_sortorder', 'Tag cloud sort order:', 'dropdown', 'natural/countup/countdown/asc/desc/random', 
						'<ul>
							<li><code>Natural</code> &ndash; natural case sorting (i.e. treats capital & non-capital the same).</li>
							<li><code>Alpha</code> &ndash; strict alphabetic order (capitals first).</li>
							<li><code>Countup/Asc</code> &ndash; ascending order by tag usage.</li>
							<li><code>Countdown/Desc</code> &ndash; descending order by tag usage.</li>
							<li><code>Random</code> &ndash; randomized every time the page is loaded.</li>
						</ul>'),
			    array('cloud_displaymax', 'Maximum number of tags to display:', 'text', 40,
			            "Set to zero (0) to show all tags."),
			    array('cloud_displaymin', 'Minimum tag count required:', 'text', 40, 
			            "Tags must be used at least this many times to show up in the cloud."),
			    array('cloud_scalemax', 'Tag count scaling maximum:', 'text', 40, 
			            "Set to zero (0) to disable tag scaling."),
			    array('cloud_scalemin', 'Tag count scaling minimum:', 'text', 40, 
			            "Use with the maximum scale to limit the range between your most popular and least popular tags."),
			    array('cloud_notagstext', 'Text to display if no tags found:', 'text', 80)
		    ),
			'Related Posts' => array(
			    array('related_format', 'Related Posts format:', 'text', 80,
						'<ul>
							<li><code>%date%</code> &ndash; Replaced by the post date.</li>
							<li><code>%permalink%</code> &ndash; Replaced by the post\'s permalink.</li>
							<li><code>%title%</code> &ndash; Replaced by the post\'s title.</li>
							<li><code>%commentcount%</code> &ndash; Replaced by the number of comments of the post.</li>
						</ul>'),
			    array('related_postsseparator', 'Related posts separator(s):', 'text', 80),
				array('related_sortorder', 'Related Posts sort order:', 'dropdown', 'date-desc/date-asc/alpha/random', 
						'<ul>
							<li><code>Date-desc</code> &ndash; sorting by post date, descending.</li>
							<li><code>Date-asc</code> &ndash; sorting by post date, ascending.</li>
							<li><code>Alpha</code> &ndash; alphabetic order.</li>
							<li><code>Random</code> &ndash; randomized every time the page is loaded.</li>
						</ul>'),
			    array('related_limit_qty', 'Maximum number of related posts to display:', 'text', 40),
			    array('related_limit_days', 'Maximum number of days to be considered:', 'text', 40, 
			            "'365' means that related posts of the past 365 days are being considered."),
			    array('related_dateformat', 'Format of the post’s date \'%date%\':', 'text', 40, 
						'Check out <a href="http://www.php.net/date">Formatting Date and Time</a> and <a href="http://www.php.net/date">PHP manual for date()</a> to see which format character you can use here.'),
			    array('related_nothingfound', 'Text to display if no related posts found:', 'text', 80),
			    array('related_includepages', 'Include pages in related posts list:', 'checkbox', '1'),
			),
			'Related Tags' => array(
			    array('relatedtags_format', 'Related Tags format:', 'text', 80,
						'<ul>
							<li><code>%count%</code> &ndash; Replaced by the number of posts that do have this tag.</li>
							<li><code>%tagname%</code> &ndash; Replaced by the tag name.</li>
							<li><code>%taglink%</code> &ndash; Replaced by the tag link.</li>
							<li><code>%taglink_plus%</code> &ndash; Replaced by the tag link that adds the current tag to the currently browsing tag(s).</li>
						</ul>'),
			    array('relatedtags_tagseparator', 'Related tag separator(s):', 'text', 80),
				array('relatedtags_sortorder', 'Related Tags sort order:', 'dropdown', 'alpha-asc/count-desc/count-asc/alpha-desc', 
						'<ul>
							<li><code>Alpha-asc</code> &ndash; alphabetic order.</li>
							<li><code>Count-desc</code> &ndash; descending order by number of tags.</li>
							<li><code>Count-asc</code> &ndash; ascending order by number of tags.</li>
							<li><code>Alpha-desc</code> &ndash; descending alphabetic order.</li>
						</ul>'),
				array('relatedtags_nonfoundtext', 'Text to display if no related tags found:', 'text', 80)
			),
			'Related Tags: Links for Removing Tags' => array(
			    array('relatedtags_remove_format', 'Format for \'removing tag\' links:', 'text', 80,
						'<ul>
							<li><code>%url%</code> &ndash; Replaced by the permalink of the URL without the appropriate tag.</li>
							<li><code>%tagname%</code> &ndash; Replaced by the tag name.</li>
						</ul>'),
			    array('relatedtags_remove_separator', 'Separator(s) for removing tags:', 'text', 80),
			    array('relatedtags_remove_nonfoundtext', 'Text to display if there are no tags to remove:', 'text', 80),
			),
			'Administration' => array(
				array('admin_max_suggest', 'Maximum number of tags:', 'text', 40, 'The maximum number of tags displayed under <em>Write > Post</em>.
				Zero (0) means no limit and shows all tags.'),
			    array('admin_tag_suggested', 'Display suggested tags only:', 'checkbox', '1',
				'Displays suggested tags only instead of all tags. Tags will be suggested if they are part of the post. In adddition, tags that are
				used more often will be considered first.'),
			    array('admin_tag_sort', 'Tag cloud sort order:', 'dropdown', 'alpha/relevance', 
						'<ul>
							<li><code>Alpha</code> &ndash; alphabetic order.</li>
							<li><code>Relevance</code> &ndash; most relevant/used tags first.</li>
						</ul>'),
			),
			'Embedded Tags' => array(
			    array('tag_embed_use', 'Use embedded tags:', 'checkbox', '1', 'Enabling this will 
				cause Wordpress to look for embedded tags when saving and displaying posts. Such set of tags 
				is marked <code>[tags]like this, and this[/tags]</code>, and is added to the post when the post is saved, 
				but does not display on the post.'),
			    array('tag_embed_start', 'Prefix for embedded tags:', 'text', 40),
			    array('tag_embed_end', 'Suffix for embedded tags:', 'text', 40),

			),


	); // $option_data = array	

		
		// handle form actions
		if (!empty($_POST['updateoptions'])) { //Pressed button: Update Options
			foreach($this->sTagObj->option as $key => $value) {
				switch ($key) {
					case 'version_for_install':
					case 'tags_table':
						// don't update these
						break;
					default:
						$newval = (isset($_POST[$key])) ? stripslashes($_POST[$key]) : '0';
						if ($newval != $value)
							$this->sTagObj->setOption($key, $newval);
						break;
				}
			}
			$this->sTagObj->saveOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => 'Simple Tagging options saved');
		} elseif (!empty($_POST['resetoptions'])) { //Pressed button: Reset Options
			$this->sTagObj->resetToDefaultOptions();
			$pagemsgArr = array('type' => 'updated', 'msg' => 'Simple Tagging options resetted to default options!');
		}

		// Display message
		$res['pagemsg'] = '';
		if (!empty($pagemsgArr)) {
		    $res['pagemsg'] = '<div id="message" class="' . $pagemsgArr['type'] . ' fade"><p><strong>' . $pagemsgArr['msg'] . '</strong></p></div>';
		}

		// URL for form actions
		$actionurl = $_SERVER['REQUEST_URI'];
		$actionurl = stripslashes($actionurl);	// additional slashes are added when saving, so we remove 'em... 
		
		// Put submit & reset buttons into string
	    $res['buttons'] = '
			<p class="submit">
				<input type="submit" name="updateoptions" value="' . __('Update Options') . ' &raquo;" />
				<input type="submit" name="resetoptions" class="stpwarn" onclick=\'return confirm("Do you really want to restore the default options?");\' value="' . __('Reset Options') . '" />
		    </p>';


		// Put options into string
		$res['options'] = '';
		foreach($option_data as $section => $options) {

		    $res['options'] .= "\n" . '<fieldset class="options">
				<legend>' . __($section) . '</legend>
				<table class="optiontable">
				';

			foreach($options as $option) {
				////////////////////////////////////////////////////////////////
			    // Determine input type -- $input_type
				////////////////////////////////////////////////////////////////
			    switch($option[2]) {
			        case 'checkbox':     // checkbox
			            $input_type = '<input type="checkbox" id="' . $option[0] . '" name="' . $option[0] .
			                          '" value="' . htmlspecialchars($option[3]) . '" ' . 
			                          ( ($this->sTagObj->option[ $option[0] ]) ? 'checked="checked"' : '') . ' />';
			            break;
			        case 'dropdown':     // select/dropdown
			            $selopts = explode('/', $option[3]);
			            $seldata = '';
			            foreach($selopts as $sel) {
			                $seldata .= '<option value="' . $sel . '" ' . 
			                            (($this->sTagObj->option[ $option[0] ] == $sel) ? 'selected="selected"' : '') . 
			                            ' >' . ucfirst($sel) . '</option>';
			            }
			            $input_type = '<select id="' . $option[0] . '" name="' . $option[0] . '">' . 
			                          $seldata . '</select>';
			            break;
			        default;    // text input
			            $input_type = '<input type="text" ' . (($option[3]>50) ? ' style="width: 95%" ' : '') .
			                          'id="' . $option[0] . '" name="' . $option[0] .
			                          '" value="' . htmlspecialchars($this->sTagObj->option[ $option[0] ]) . '" size="' . $option[3] .'" />';
			            break;
			    }
				////////////////////////////////////////////////////////////////
			    // Additional Information
				////////////////////////////////////////////////////////////////
				$extra = '';
				if( $option[4] != '' ) {
					$extra = '<div class="stpexplan">' . __($option[4]) . '</div>';
				}

				////////////////////////////////////////////////////////////////
			    // Output
				////////////////////////////////////////////////////////////////
				$res['options'] .= '
			      <tr style="vertical-align: top;">
			        <th scope="row">' . __($option[1]) . '</th>
			        <td>' . $input_type . '
			          ' . $extra . '</td>
			      </tr>';
			
			} // foreach($options as $option)
			$res['options'] .= '</table>' . "\n";
			$res['options'] .= '</fieldset>';

		}	
		
		########################### OUTPUT #####################################		
		?>
		<style type="text/css">
		input.stpwarn:hover { background: #ce0000; color: #fff; }
		.stpexplan { font-size: 95%; line-height:120%; }
		.stpexplan ul, .stpexplan ul li {margin: 0; padding:0; line-height:120%;  }
		.stpexplan ul { margin-left: 20px; }
		</style>

		<?php echo $res['pagemsg']; ?>

		<div class="wrap">  
		<h2>Simple Tagging: Options</h2>
		<p>Visit the <a href="http://sw-guide.de/wordpress/plugins/simple-tagging/">plugin's homepage</a> for further details.</p>
		<form action="<?php echo $actionurl; ?>" method="post"> 			

		<?php echo $res['buttons']; ?>

		<?php echo $res['options']; ?>

		<?php echo $res['buttons']; ?>

		</form>
		
		<p style="text-align: center; font-size: .85em;">&copy; Copyright 2007&nbsp;&nbsp;<a href="http://sw-guide.de">Michael W&ouml;hrer</a></p>

		</div> <!-- [wrap] -->
		<?php

	} // function stp_menu_options()
	

	
	
   	######################################################################################################################
    # ADMIN MENU: MANAGE TAGS
   	######################################################################################################################
	function stp_menu_manage() {

		switch ((isset($_POST['tag_action'])) ? $_POST['tag_action'] : '') {
		    case 'renametag':
		        $oldtag = stripslashes( (isset($_POST['renametag_old'])) ? $_POST['renametag_old'] : '');
		        $newtag = stripslashes( (isset($_POST['renametag_new'])) ? $_POST['renametag_new'] : '');
		        $pagemsg = $this->updateTags($oldtag, $newtag, true);
		        break;
		    case 'deletetag':
		        $todelete = stripslashes( (isset($_POST['deletetag_name'])) ? $_POST['deletetag_name'] : '');
		        $pagemsg = $this->deleteTagsByTagList($todelete);
		        break;
		    case 'addtag':
		        $matchtag = stripslashes( (isset($_POST['addtag_match'])) ? $_POST['addtag_match'] : '');
		        $newtag   = stripslashes( (isset($_POST['addtag_new'])) ? $_POST['addtag_new'] : '');
		        $pagemsg = $this->updateTags($matchtag, $newtag, false);
		        break;
		    default:
		        // no action
		        $pagemsg = (isset($_REQUEST['tag_message'])) ? stripslashes($_REQUEST['tag_message']) : '';
		        break;
		}
		if (!empty($pagemsg))
		    $pagemsg = '<div id="message" class="updated fade"><p><strong>' . $pagemsg . '</strong></p></div>';
		
		
		
		/* URL for form actions */
		$actionurl = $_SERVER['REQUEST_URI'];
		
		/* tag sort order */
		$tag_listing = '<p style="margin:0; padding:0;">Sort Order:</p><p style="margin:0 0 10px 10px; padding:0;">';
		$order_array = array('desc' => 'Most&nbsp;Popular', 'asc' => 'Least&nbsp;Used', 'natural' => 'Alphabetical');
		$sortorder = strtolower((isset($_REQUEST['tag_sortorder'])) ? $_REQUEST['tag_sortorder'] : 'desc');
		$sortbaseurl = preg_replace('/&?tag_sortorder=[^&]*/', '', $actionurl, 1);
		foreach($order_array as $sort => $caption)
		    $tag_listing .= ($sort == $sortorder) ? " <span style='color: red;'>$caption</span> <br />" : 
		                    " <a href=\"{$sortbaseurl}&tag_sortorder=$sort\">$caption</a> <br/>";
		$tag_listing .= '</p>';
		
		/* create tag listing */
		$all_tags = $this->getAllTags($sortorder);
		foreach($all_tags as $tag => $count) {
/*
		    $tag_listing .= "<li>
		        <span style=\"cursor: pointer;\" 
		            onclick=\"javascript:updateTagFields(this.innerHTML);\">$tag</span>&nbsp;
		        <a href=\"" . get_bloginfo('home') . '/?tag=' . str_replace('%2F', '/', urlencode($tag)) .
				 
		        "\" title=\"View all posts tagged with $tag\">($count)</a></li>\n";
*/
		    $tag_listing .= '<li>
		        <span style="cursor: pointer;" onclick="javascript:updateTagFields(this.innerHTML);"> ' . $tag . 
				'</span>&nbsp;<a href="' . $this->sTagObj->getTagPermalink($tag) .   '" title="'
				. __('View all posts tagged with') . ' ' . $tag . '">(' . $count . ')</a></li>' . "\n";
		}

		
		?>
		
		
		
		<style type="text/css">
		<!--
			fieldset#taglist ul { list-style: none; margin: 0; padding: 0; }
			fieldset#taglist ul li { margin: 0; padding: 0; font-size: 85%; }

		//--> 
		</style>

		<?php echo $pagemsg; ?>

		<div class="wrap">
		  
		  <h2><?php echo 'Simple Tagging: ' . __('Manage Tags'); ?></h2>
			<p>Visit the <a href="http://sw-guide.de/wordpress/plugins/simple-tagging/">plugin's homepage</a> for further details.</p>
		  <table>
		  <tr><td style="vertical-align: top; border-right: 1px dotted #ccc;">
		  
		    <fieldset class="options" id="taglist"><legend>Existing Tags</legend>
		      <ul>
		        <?php echo $tag_listing; ?>
		      </ul>
		    </fieldset>
		  
		  </td><td style="vertical-align: top;">
		
		    <fieldset class="options"><legend>Rename Tag</legend>
		      <p>Enter the tag to rename and its new value.  You can use this feature to merge tags too.
		         Click "Rename" and all posts which use this tag will be updated.</p>
		      <p>You can specify multiple tags to rename by separating them with commas.</p>
		      <form action="<?php echo $actionurl; ?>" method="post">
		        <input type="hidden" name="tag_action" value="renametag" />
		        <table>
		        <tr><th>Tag(s) to Rename:</th><td> <input type="text" id="renametag_old" name="renametag_old" value="" size="40" /> </td></tr>
		        <tr><th>New Tag Name(s):</th><td> <input type="text" id="renametag_new" name="renametag_new" value="" size="40" /> </td></tr>
		        <tr><th></th><td> <input type="submit" name="Rename" value="Rename" /> </td></tr>
		        </table>
		      </form>
		    </fieldset>
		
		    <fieldset class="options"><legend>Delete Tag</legend>
		      <p>Enter the name of the tag to delete.  This tag will be removed from all posts.</p>
		      <p>You can specify multiple tags to delete by separating them with commas.</p>
		      <form action="<?php echo $actionurl; ?>" method="post">
		        <input type="hidden" name="tag_action" value="deletetag" />
		        <table>
		        <tr><th>Tag(s) to Delete:</th><td> <input type="text" id="deletetag_name" name="deletetag_name" value="" size="40" /> </td></tr>
		        <tr><th></th><td> <input type="submit" name="Delete" value="Delete" /> </td></tr>
		        </table>
		      </form>
		    </fieldset>
		    
		    <fieldset class="options"><legend>Add Tag</legend>
		      <p>This feature lets you add one or more new tags to all posts which match any of the tags given.</p>
		      <p>You can specify multiple tags to add by separating them with commas.  If you want the tag(s)
		         to be added to all posts, then don't specify any tags to match.</p>
		      <form action="<?php echo $actionurl; ?>" method="post">
		        <input type="hidden" name="tag_action" value="addtag" />
		        <table>
		        <tr><th>Tag(s) to Match:</th><td> <input type="text" id="addtag_match" name="addtag_match" value="" size="40" /> </td></tr>
		        <tr><th>Tag(s) to Add:</th><td>   <input type="text" id="addtag_new" name="addtag_new" value="" size="40" /> </td></tr>
		        <tr><th></th><td> <input type="submit" name="Add" value="Add" /> </td></tr>
		        </table>
		      </form>
		    </fieldset>
		    
		  </td></tr>
		  </table>
		
		  <script type="text/javascript">
		    if(document.all && !document.getElementById) {
		        document.getElementById = function(id) { return document.all[id]; }
		    }
		    function addTag(tag, input_element) {
		        if (input_element.value.length > 0 && !input_element.value.match(/,\s*$/))
		            input_element.value += ", ";
		        var re = new RegExp(tag + ",");
		        if (!input_element.value.match(re))
		            input_element.value += tag + ", ";
		    }
		    function updateTagFields(tag) {
		        addTag(tag, document.getElementById("renametag_old"));
		        addTag(tag, document.getElementById("deletetag_name"));
		        addTag(tag, document.getElementById("addtag_match"));
		    }
		
		  </script>
		
		
		<p style="text-align: center; font-size: .85em;">&copy; Copyright 2007&nbsp;&nbsp;<a href="http://sw-guide.de">Michael W&ouml;hrer</a></p>
		</div> <!-- wrap -->

		<?php

	} // function stp_menu_manage()



   	######################################################################################################################
    # ADMIN MENU: IMPORT
   	######################################################################################################################
	function stp_menu_import() {
		global $wpdb;

		$UTW_tTags			= $this->import['UTW_tTags'];
		$UTW_tTagsID		= $this->import['UTW_tTagsID'];
		$UTW_tTagsName		= $this->import['UTW_tTagsName'];
		$UTW_tP2T			= $this->import['UTW_tP2T'];
		$UTW_tP2TtagID		= $this->import['UTW_tP2TtagID'];
		$UTW_tP2TpostID		= $this->import['UTW_tP2TpostID'];

		// Handle form actions
		switch ((isset($_POST['tag_action'])) ? $_POST['tag_action'] : '') {

			case 'convertcatstotags':
				if (isset($_POST['convertcatstotags_confirm']) && $_POST['convertcatstotags_confirm'] == 1 ) {
		        	$pagemsg = $this->STP_ConvertCatsToTags();
				} else {
					$pagemsg = 'Error: You clicked the &laquo;Category&raquo; import button but you did not confirm that you have backuped your database.';
				}
		        break;

			case 'importfromutw':
				if (isset($_POST['importfromutw_confirm']) && $_POST['importfromutw_confirm'] == 1 ) {
		        	$pagemsg = $this->STP_ImportFromUTW();
				} else {
					$pagemsg = 'Error: You clicked the &laquo;UTW&raquo; import button but you did not confirm that you have backuped your database.';
				}
		        break;
			case 'importfromjk19':
				if (isset($_POST['importfromjk19_confirm']) && $_POST['importfromjk19_confirm'] == 1 ) {
		        	$pagemsg = $this->STP_ImportFromJK19();
				} else {
					$pagemsg = 'Error: You clicked the &laquo;Jerome\'s Keywords 1.9&raquo; import button but you did not confirm that you have backuped your database.';
				}
		        break;

			case 'importfromjk20beta':
				if (isset($_POST['importfromjk20beta_confirm']) && $_POST['importfromjk20beta_confirm'] == 1 ) {
		        	$pagemsg = $this->STP_ImportFromJK20Beta();
				} else {
					$pagemsg = 'Error: You clicked the &laquo;Jerome\'s Keywords 2.0&raquo; import button but you did not confirm that you have backuped your database.';
				}
		        break;

		    default:
		        // no action
		        $pagemsg = (isset($_REQUEST['tag_message'])) ? stripslashes($_REQUEST['tag_message']) : '';
				break;
		}
		if (!empty($pagemsg))
		    $pagemsg = '<div id="message" class="updated fade"><p><strong>' . $pagemsg . '</strong></p></div>';






		$actionurl = $_SERVER['REQUEST_URI'];

		?>
		<?php echo $pagemsg; ?>
		<div class="wrap">
		
		<h2><?php echo 'Simple Tagging: ' . __('Import Tags'); ?></h2>

		<p>Howdy! Here you can import tags from several different sources.</p>
		<p>These importers are smart enough not to import duplicates, so you can run these multiple times without worry if — for whatever reason — they don't finish.</p>
		<p><strong>Please note</strong> that importing tags could break your entire blog. Make sure you back up your
			database before clicking the button.</p>
		<p>Visit the <a href="http://sw-guide.de/wordpress/plugins/simple-tagging/">plugin's homepage</a> for further details.</p>
		<hr />
		<!-- **************************************************************************************** -->		
		<h3>Convert Categories to Tags</h3>
	
			<fieldset class="options">
				<form action="<?php echo $actionurl; ?>" method="post">
				<input type="hidden" name="tag_action" value="convertcatstotags" />
				<label for="convertcatstotags_confirm">
					<input name="convertcatstotags_confirm" type="checkbox" id="convertcatstotags_confirm" value="1" /><span style="color: blue;"> I've backuped my database.</span>
				</label>
				<br /><br />
				<input type="submit" name="Convert Categories to Tags" value="Convert Categories to Tags" />
				</form>
		    </fieldset>


		<hr />		
		<!-- **************************************************************************************** -->
		<h3>Ultimate Tag Worrior Plugin (UTW) Version 3.141</h3>
		<?php
		
		// Do UTW tables exist?
		$notexistingtable = array();
		if($wpdb->get_var("SHOW TABLES LIKE '$UTW_tTags'") != $UTW_tTags) {
			$notexistingtable[] = $UTW_tTags;
		}
		if($wpdb->get_var("SHOW TABLES LIKE '$UTW_tP2T'") != $UTW_tP2T) {
			$notexistingtable[] = $UTW_tP2T;
		}
		if (count($notexistingtable) != 0 ) {
			echo '<p>A supported UTW installation could not be found, missing table(s): <code>' . implode(' , ', $notexistingtable) . '</code>';
		} else {
		
		?>
			<p>This function has been tested with UTW version 3.141 only, other versions may not work and
			if you use another version it is even more important to back up your database before you click the button.</p>
				
		    
			<fieldset class="options">
				<form action="<?php echo $actionurl; ?>" method="post">
				<input type="hidden" name="tag_action" value="importfromutw" />
				<label for="importfromutw_confirm">
					<input name="importfromutw_confirm" type="checkbox" id="importfromutw_confirm" value="1" /><span style="color: blue;"> I've backuped my database.</span>
				</label>
				<br /><br />
				<input type="submit" name="Import from UTW" value="Import from UTW" />
				</form>
		    </fieldset>
		<?php 
		
		} // if (count($notexistingtable) != 0 ) {
		?>
		
		<hr />
		<!-- **************************************************************************************** -->
		<h3>Jerome's Keywords Plugin Version 1.9</h3>
		
		
<?php

        /* import Jerome's Keywords 1.9 */
        $qry = "SELECT post_id, meta_id, meta_key, meta_value
                FROM  {$wpdb->postmeta} meta
                WHERE meta.meta_key = 'keywords'";
        $metakeys = $wpdb->get_results($qry);
        if (count($metakeys) > 0) {
			?>
		    
			<fieldset class="options">
				<form action="<?php echo $actionurl; ?>" method="post">
				<input type="hidden" name="tag_action" value="importfromjk19" />
				<label for="importfromjk19_confirm">
					<input name="importfromjk19_confirm" type="checkbox" id="importfromjk19_confirm" value="1" /><span style="color: blue;"> I've backuped my database.</span>
				</label>
				<br /><br />
				<input type="submit" name="Import from Jerome's Keywords 1.9" value="Import from Jerome's Keywords 1.9" />
				</form>
		    </fieldset>
			<?php

        } else {
			echo '<p>Tags from Jerome\'s Keywords Plugin version 1.9 have not been found...';
		}

?>		
		<hr />

		<h3>Jerome's Keywords Plugin Version 2.0 Beta 3</h3>
				
		<?php
		global $table_prefix;
		$jk2table = $table_prefix . 'jkeywords';

		if($wpdb->get_var("SHOW TABLES LIKE '$jk2table'") != $jk2table) {
			echo '<p>A supported Jerome\'s Keywords 2.0 installation could not be found.';
		} else {
			?>
	    
			<fieldset class="options">
				<form action="<?php echo $actionurl; ?>" method="post">
				<input type="hidden" name="tag_action" value="importfromjk20beta" />
				<label for="importfromjk20beta_confirm">
					<input name="importfromjk20beta_confirm" type="checkbox" id="importfromjk20beta_confirm" value="1" /><span style="color: blue;"> I've backuped my database.</span>
				</label>
				<br /><br />
				<input type="submit" name="Import from Jerome's Keywords 2.0 Beta" value="Import from Jerome's Keywords 2.0 Beta" />
				</form>
		    </fieldset>
		<?php
		} //
		?>
	
		<p style="text-align: center; font-size: .85em;">&copy; Copyright 2007&nbsp;&nbsp;<a href="http://sw-guide.de">Michael W&ouml;hrer</a></p>
		</div> <!-- wrap -->

	<?php
	} // function stp_menu_import()


   	######################################################################################################################
    # ADMIN MENU: DISPLAY NOT TAGGED ARTICLES
   	######################################################################################################################
	function stp_menu_not_tagged() {
		global $wpdb;

		$actionurl = $_SERVER['REQUEST_URI'];


		// Prepare drop-down
		$dposts = ($this->sTagObj->is_wp_21) ? "AND posts.post_type = 'post' AND posts.post_status = 'publish'" : "AND posts.post_status = 'publish'";
		$dpages = ($this->sTagObj->is_wp_21) ? "AND posts.post_type = 'page' AND posts.post_status = 'publish'" : "AND posts.post_status = 'static'";
		$dboth = ($this->sTagObj->is_wp_21) ? "AND posts.post_type IN('page','post') AND posts.post_status = 'publish'" : "AND posts.post_status IN('static','publish')";

		$opt = array (
			'sortorder' => 	array(
							'title'		=> array('Sort by Title', 'posts.post_title ASC'),
							'date_desc'	=> array('Sort by Date, descending', 'posts.post_date DESC'),
							'date_asc'	=> array('Sort by Date, ascending', 'posts.post_date ASC')
							),	
			'postspages' => array(
							'posts'			=> array('Show Posts', $dposts),
							'pages'			=> array('Show Pages', $dpages),
							'postsandpages'	=> array('Show Posts and Pages', $dboth)
							)
		);

		foreach ($opt as $optname => $optionvalues) {
			$list[$optname] = '';
			foreach ($optionvalues as $optval => $cont) {
				$selected = (isset($_POST[$optname]) && $optval == $_POST[$optname] ) ? ' selected="selected"' : '';
				$list[$optname] .= '<option' . $selected . ' value="' . $optval . '">' . $cont[0] . '</option>';		
			}
			if ( isset($_POST[$optname] ) ) { 
				$curr[$optname] = $optionvalues[$_POST[$optname]][1];
			} else {
				// Standard value is first value of array, so let's retrieve it:
				$output = array_values($optionvalues);		
				$curr[$optname] = $output[0][1];
			}

		}

		// SQL query
		$qry = "SELECT posts.post_title, posts.post_date, posts.id
				FROM {$wpdb->posts} AS posts LEFT JOIN {$this->sTagObj->info['stptable']} AS tags
				ON posts.id = tags.post_id
				WHERE tags.post_id IS NULL
				{$curr['postspages']}
				AND posts.post_date < '" . current_time('mysql') . "'
				GROUP BY posts.post_title
				ORDER BY {$curr['sortorder']}
				";

		$queryresult = $wpdb->get_results($qry);
		
		$result = '';
		if (!is_null($queryresult) && is_array($queryresult)) {
			foreach ($queryresult as $postloop) {

				$lp_date = mysql2date(get_option('date_format'), $postloop->post_date);
				$lp_title = $postloop->post_title;
				$lp_id = $postloop->id;
				$lp_perma = get_permalink($postloop->id);
				
				$result .= '<li><a title="' . $lp_title . ' (ID: ' . $lp_id . ')" href="' . $lp_perma . '">' . $lp_title . '</a> (' . $lp_date . ')';
				if ( current_user_can('edit_post', $lp_id) )
					$result .= ' [<a title="' . __('Edit Post') . '" href="post.php?action=edit&amp;post=' . $lp_id . '">' . __('Edit') . '</a>]';
				$result .= '</li>';

			}

		} 


		echo "\n" . '<div class="wrap">' . "\n"; 
		echo '<h2>Simple Tagging: ' . __('Not Tagged Articles') . '</h2>' . "\n";

?>

			<form name="viewposts" action="<?php echo $actionurl; ?>" method="post" >
			<fieldset>
    			<select name='sortorder'>
					<?php echo $list['sortorder']; ?>
				</select>
    			<select name='postspages'>
					<?php echo $list['postspages']; ?>
				</select>
				<input type="submit" name="changelist" value="Show"  /> 
			</fieldset>
			</form>
			<br />



<?php
 
		if ($result == '') {
			echo '<p>Every article is tagged :-)</p>';
		} else {
			echo '<p>The following articles are not tagged:</p>';		

			echo "\n" . '<ul>' . "\n"; 
			echo $result;
			echo "\n" . '</ul>' . "\n";




		}

		echo '
		<p style="text-align: center; font-size: .85em;">&copy; Copyright 2007&nbsp;&nbsp;<a href="http://sw-guide.de">Michael W&ouml;hrer</a></p>
		</div> <!-- wrap -->
		';


	} // function stp_menu_not-tagged()

    

} // class SimpleTaggingAdmin


?>