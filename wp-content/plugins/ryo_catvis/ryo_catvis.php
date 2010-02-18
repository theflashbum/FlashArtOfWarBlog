<?php
/*
Plugin Name: RYO Category Visibility
Plugin URI: http://ryowebsite.com/wp-plugins
Description: Alter the visibility settings for categories shown in the front page, sidebar. For WordPress 2.8+.
Author: Rich Hamilton
Version: 2.8-beta-.04
Author URI: http://ryowebsite.com
*/
/*

    Beta .04  Fixed an initialization problem for certain configurations of php.
              Moved the options page from Plugins to Settings.
    
    Beta .03  Fixed a situation where Posts were not always "merging" requests with excludes 
              from other plugins.
    Beta .02  Added User Level options as per the original Category Visibility.

    v2.8    Faster logic and better compatibility than our earlier Category Visibility.
            Based on an earlier concept by Keith McDuffee but with entirely new logic.
            The old version maintained a separate data table, changed joins and wheres,
            but this one just tells WP what NOT to show.

*/
/*
============================================================================================================
    Copyright (C) 2009  Rich Hamilton (http://ryowebsite.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
============================================================================================================ 
*/


function ryocatvis_posts($qobj) {
    if ( 'posts' == get_option('show_on_front') && $qobj->is_home ) $cycle = 'front';
    elseif ( $qobj->is_archive && !$qobj->is_category ) $cycle = 'archives';
    elseif ( $qobj->is_search && !$qobj->is_admin ) $cycle = 'search';
    elseif ( $qobj->is_feed ) $cycle = 'feed';
    else $cycle = '';
    if ($cycle) {
        $op = ryocatvis_vals();
        $orig = (isset($qobj->query_vars['cat'])) ? (array) explode(',',$qobj->query_vars['cat']) : array();
        $cats = '-'.implode(',-',ryocatvis_excludelevels(array_merge($orig,$op[$cycle]),$op['level']));
        //$cats = '-'.implode(',-',ryocatvis_excludelevels($op[$cycle],$op['level']));
        if (strlen($cats)>1) $qobj->query_vars=array_merge( (array)$qobj->query_vars, array('cat' => urlencode($cats)) );
        if ($cycle!='search' && $op['postmash'] && function_exists('postMash_main'))
           $qobj->query_vars=array_merge( (array)$qobj->query_vars, array('orderby' => 'menu_order', 'order' => 'ASC') );
    }
}
add_action('pre_get_posts','ryocatvis_posts',10,1);

function ryocatvis_lists($terms, $taxonomies, $args){
    if (isset($args['get']) && $args['get']=='all') return $terms;
    if (in_array('category',$taxonomies) && !is_admin()) {
        $ret = array();
        $op = ryocatvis_vals();
        $skipcats = ryocatvis_excludelevels($op['list'],$op['level']);
        if($skipcats){
            foreach ($terms as $key => $term) {
                if (!in_array($term->term_id,$skipcats)) $ret[$key]=$term;
            }
            return $ret;
        }
    }
    return $terms;
}
add_filter('get_terms', 'ryocatvis_lists',10,3);

function ryocatvis_excludelevels($exclude,$oplevels) {
    global $user_level;
    get_currentuserinfo();
    if (!is_numeric($user_level)) $user_level = 0;
    if (!is_array($exclude)) $exclude=array();
    foreach ((array)$oplevels as $cat => $level) {
        if ($level > $user_level && !in_array($cat,$exclude)) $exclude[] = $cat;
    }
    return $exclude;
}

function ryocatvis_vals() {
    $op = get_settings('ryocatvis');
    if (!is_array($op)) $op=array('exclude'=>0,'postmash'=>0,'level'=>array());
    $op['cycles']=array('front','list','search','feed','archives');
    foreach ($op['cycles'] as $cycle) { if (!isset($op[$cycle]) || !is_array($op[$cycle])) $op[$cycle]=array();  }
    return $op;
}

function ryocatvis_options_page() {
    global $wp_roles;
    $op = ryocatvis_vals();
    $cycles=$op['cycles'];
    $categories = get_categories("child_of=0&hide_empty=0&hierarchical=1");
    $categories = ryocatvis_cat_tree($categories);
    if ( isset($_POST['submitted']) ) {
        check_admin_referer('ryocatvis_options_all');
        $op = array();
        $op['exclude'] = (isset($_POST['exclude']) && $_POST['exclude']) ? 1 : 0;
        $op['postmash'] = (isset($_POST['postmash']) && $_POST['postmash']) ? 1 : 0;
        $op['level'] = (isset($_POST['level']) && $_POST['level']) ? $_POST['level'] : array();
        foreach ($cycles as $cycle) {
            $afront = (array) $_POST[$cycle];
            $op[$cycle]=array();
            foreach ($categories as $category) {
                if ( ($op['exclude'] && isset($afront[$category->cat_ID])) 
                || (!$op['exclude'] && !isset($afront[$category->cat_ID])) ) $op[$cycle][]=$category->cat_ID;
            }
        }
        update_option('ryocatvis', $op);
        echo '<div id="message" class="updated fade"><p><strong>Plugin settings saved.</strong></p></div>';
    }
    $style = " scope='col' style='padding:2px 10px;background-color:#ececec;'";
    ?>
    <div class='wrap'>
        <h2>RYO Category Visibility</h2>
        <p>Select which categories you want to include (or exclude) for various parts of your website.
        </p>
        <?php if (function_exists('postMash_main')) { ?>
        <p>Since you're using postMash, you may opt to use our function to have pages sort posts<br>automatically according to the postMash order (without template modifications).</p>
        <?php } ?>
        <form name="catpostshomepage" action="" method="post">
            <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('ryocatvis_options_all'); } ?>
            <input type="hidden" name="submitted" value="1" />
            <table cellpadding="3" cellspacing="3">
                <tr><td colspan="3">
                <h3>Settings</h3>
                <ul>
                    <?php $thisval= ($op['exclude']) ? "checked='checked'" : ''; ?>
                    <li class='popular-category'><input type='checkbox' id='exclude' name='exclude' <?php echo $thisval; ?> />
                    &nbsp;<label for='exclude' class='selectit'>
                    Check to exclude selected categories. (Leave unchecked to include selected categories.)</label>
                    </li>
                    <?php 
                    if (!function_exists('postMash_main')) {
                        echo "\n<input type='hidden' name='postmash' value='{$op['postmash']}' />";
                    } else {
                        $thisval= ($op['postmash']) ? "checked='checked'" : ''; ?>
                        <li class='popular-category'><input type='checkbox' id='postmash' name='postmash' <?php echo $thisval; ?> />
                        &nbsp;<label for='postmash' class='selectit'>
                        Check to sort posts in postMash order rather than date order.)</label>
                        </li>
                        <?php                    
                    }
                    ?>
                </ul>
                <p>
                <h4>Select Categories, then click 'Save Changes':</h4>
                </p>
                </td></tr>
                <tr>
                    <th<?php echo $style; ?>><?php _e('ID') ?></th>
                    <th<?php echo $style; ?>><?php _e('Name') ?></th>
                    <th<?php echo $style; ?>><?php _e('Visibility') ?></th>
                </tr>
                <?php
                $alternate = false;
                foreach ($categories as $category) {
                    $edit = '';
                    foreach ($cycles as $cycle) {
                        $labelit = ucfirst($cycle);
                        $ischecked = (($op['exclude'] && in_array($category->cat_ID,$op[$cycle]))
                            || (!$op['exclude'] && !in_array($category->cat_ID,$op[$cycle])) ) ? "checked='checked'" : '';
                        $edit  .= "\n  <label for='{$cycle}[{$category->cat_ID}]'>".ucfirst($cycle).":</label> <input name='{$cycle}[{$category->cat_ID}]' id='{$cycle}[{$category->cat_ID}]' class='edit' type='checkbox' $ischecked/>&nbsp;&nbsp;";
                    }
                    $levelvalue = (isset($op['level'][$category->cat_ID]) && $op['level'][$category->cat_ID]>0 ) ? $op['level'][$category->cat_ID] : 0;
                    $edit .= "\n<label for='level[{$category->cat_ID}]'>User Level:</label> <input name='level[{$category->cat_ID}]' id='level[{$category->cat_ID}]' class='edit' type='text' size='3' value='$levelvalue' />";
                    $alternate = !$alternate;
                    $style = "style='padding:7px 10px;". (($alternate) ? 'background-color:#ffffff;\'' : 'background-color:#ececec;\'');
                    echo "\n<tr><th scope='row' $style>$category->cat_ID</th><td $style>$category->ryocatvis_dash".apply_filters('list_cats', $category->cat_name, $category)."</td>
                             <td  $style>$edit</td>
                            </tr>";
                }
                ?>
            <tr><td colspan="3" class="submit" align="center">
            <input name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" type="submit">
            </td></tr>
            <tr><td colspan="3">
            <hr>
            <strong>Front:</strong> Visibility on the front/main page.<br />
            <strong>List:</strong> Visibility on the list of categories on the sidebar.<br />
            <strong>Search:</strong> Visibility in search results.<br />
            <strong>Feed:</strong> Visibility in RSS/RSS2/Atom feeds.<br />
            <strong>Archive:</strong> Visibility in archive pages (i.e., calendar links).<br />
            <strong>User Level:</strong> Visibility on user level basis.<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;All users this level and higher can see categories as checked.<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Does not affect feed visibility.<br />
            <strong>Numeric User Levels:</strong><?php
            // Get Role List
            foreach($wp_roles->role_objects as $key => $role) {
                foreach($role->capabilities as $cap => $grant) {
                    $role_user_level = array_reduce(array_keys($role->capabilities), array('WP_User', 'level_reduction'), 0);
                }
                echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.ucfirst($role->name).': '.$role_user_level;
            }
            ?>
            </p><hr>
            </td></tr>
            </table>
        </form>
    </div>
    <?php
}
function ryocatvis_cat_tree($categories,$childof=0,$dash='') {
    $ret = array();
    $found = 0;
    foreach ($categories as $cat) {
        if ($cat->parent == $childof) {
            $cat->ryocatvis_dash=$dash;
            $ret[] = $cat;
            $more = ryocatvis_cat_tree($categories,$cat->cat_ID,"&#8212;$dash");
            foreach ($more as $each) {
                $ret[] = $each;
            }
        }
    }
    return $ret;
}


function ryocatvis_adminpage() {
    add_options_page(__('RYO Category Visibility'), __('RYO Category Visibility'), 'manage_options', 'ryocatvis_options_page', 'ryocatvis_options_page');
}
if ( is_admin() ) add_action('admin_menu', 'ryocatvis_adminpage');

?>