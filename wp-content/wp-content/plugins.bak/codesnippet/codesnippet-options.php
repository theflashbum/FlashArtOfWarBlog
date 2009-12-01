<?php
/**
 * Code Snippet plugin options part
 * http://blog.enargi.com/codesnippet/
 *
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
 load_plugin_textdomain('codesnippet'); // NLS
 include_once ("codesnippet.php");
  $CodeSnippet = new CodeSnippet();
  
 $location = get_option('siteurl') . '/wp-admin/admin.php?page=codesnippet/codesnippet-options.php'; // Form Action URI
  
 /*add some default options if they don't exist*/
add_option('codesnippet_line_numbers', false);
add_option('codesnippet_css_style', $CodeSnippet->getDefaultStyle());


/*check form submission and update options*/
if ('process' == $_POST['stage'])
{
update_option('codesnippet_css_style', $_POST['codesnippet_css_style']);

if(isset($_POST['codesnippet_line_numbers'])) // If checked
	{update_option('codesnippet_line_numbers', true);}
	else {update_option('codesnippet_line_numbers', false);}
}

/*Get options for form fields*/
$codesnippet_line_numbers = stripslashes(get_option('codesnippet_line_numbers'));
$codesnippet_css_style = stripslashes(get_option('codesnippet_css_style'));

?>
<div class="wrap"> 
  <h2><?php _e('Code highlighting options', 'codesnippet') ?></h2> 
  <form name="form1" method="post" action="<?php echo $location ?>&amp;updated=true">
	<input type="hidden" name="stage" value="process" />
	
	
		<fieldset class="options">
		<legend><?php _e('Preview', 'codesnippet') ?></legend>
		
	    <table width="100%" cellpadding="5" class="editform"> 
	    <tr valign="top">
	        <th width="30%" scope="row" style="text-align: left">&nbsp;</th>
	        <td>
	        	<?php echo $CodeSnippet->sampleCodeFactory(); ?>
			</td>
	      </tr>
	      
	     </table> 
		 
	</fieldset>
	
	
	<fieldset class="options">
		<legend><?php _e('Style', 'codesnippet') ?></legend>
		
	    <table width="100%" cellpadding="5" class="editform"> 
	    <tr valign="top">
	        <th width="30%" scope="row" style="text-align: left"><?php _e('Css Style', 'codesnippet') ?></th>
	        <td>
	        	<input name="codesnippet_css_style" type="text"  size="60" id="codesnippet_css_style" value="<?php echo $codesnippet_css_style ?>"/>
			</td>
	      </tr>
	      
	     </table> 
		 
	</fieldset>
	
	
	
		<fieldset class="options">
		<legend><?php _e('Line numbers', 'codesnippet') ?></legend>
		
	    <table width="100%" cellpadding="5" class="editform"> 
	      
	      <tr valign="top">
	        <th width="30%" scope="row" style="text-align: left"><?php _e('Show line numbers', 'codesnippet') ?></th>
	        <td>
	        	<input name="codesnippet_line_numbers" type="checkbox" id="codesnippet_line_numbers" value="codesnippet_line_numbers"
	        	<?php if($codesnippet_line_numbers == TRUE) {?> checked="checked" <?php } ?> />
			</td>
	      </tr>
	     </table> 
		 
	</fieldset>
	
    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Save Options', 'codesnippet') ?> &raquo;" />
    </p>
  </form> 
</div>