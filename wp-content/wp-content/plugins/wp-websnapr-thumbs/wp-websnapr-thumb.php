<?php
/*
    Plugin Name: WP-Websnapr-Thumb
    Plugin URI: http://blog.phpaws.net/2009/07/websnapr-thumb-plugin/
    Description: Easily add a website thumbnail to your post.
    Version: 0.0.1
    Author: Sebastian Reichmann
    Author URI: http://blog.phpaws.net

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
*/

function wp_websnapr_thumb($content) {
    $split_c  = preg_split("/\[(?i)(wst|wstlink)(?-i)]/", $content);
    $ret      = $split_c[0];

    for ($i = 1; $i < count($split_c); $i++) {
        $split_c2   = preg_split("/\[\/(?i)(wst|wstlink)(?-i)]/", $split_c[$i]);
        $do_link    = preg_match("/\[\/(?i)wstlink(?-i)]/",$split_c[$i]);
        $has_vars   = preg_match("/(l|r)/",substr(trim($split_c2[0]),0,1));
        
        if ($has_vars > 0) {
            $pre_split  = explode(" ", ltrim($split_c2[0]));
            $align      = trim($pre_split[0]);
            $url        = trim($pre_split[1]);
        } else {
            $align      = 'l';
            $url        = trim($split_c2[0]);
        }
        
        switch($align) {
            case 'l':
            default:
                $class = 'alignleft';
                break;
            case 'r':
                $class = 'alignright';
                break;
        }

        if ($do_link > 0) 
            $ret    .= '<a href="'.$url.'" target="_blank">';

        $ret        .= '<img class="websnapr-thumb" src="http://images.websnapr.com/?url='.$url.'&size=s&nocache=15" />';

        if ($do_link > 0)
            $ret    .= '</a>';

        $ret        .= $split_c2[1];
    }
    return $ret;
}
add_filter('the_content','wp_websnapr_thumb');
?>