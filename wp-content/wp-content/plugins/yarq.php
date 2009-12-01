<?php
/*
Plugin Name: Yet Another Random Quote
Plugin URI: http://openmonday.org/2006/07/31/yet-another-random-quote/
Description: Yet Another Random Quote, a Wordpress plugin which randomly displays quotes.
Version: 2.5.0
Author: Frank van den Brink
Author URI: http://openmonday.org

$Id: yarq.php 54579 2008-07-13 16:31:49Z ChristianB $

	Copyright 2006 Frank van den Brink (email: frank@fsfe.org)
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

//
// Table name
//
global $wpdb;
define('YARQ_QUOTES_TABLE', $wpdb->prefix . 'yarq_quotes');
define('YARQ_DB_VERSION', '2.5');

//
// Install the plugin
//
function yarq_install()
{
	global $wpdb;

	add_option('yarq_format', '<blockquote id="yarq_quote" cite="%source%">
<p>%quote%</p>
</blockquote>
<p id="yarq_author">by %author%</p>');
	add_option("yarq_db_version", YARQ_DB_VERSION);
	// clean installation
	if ($wpdb->get_var("show tables like '" . YARQ_QUOTES_TABLE . "'") != YARQ_QUOTES_TABLE) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta("CREATE TABLE " . YARQ_QUOTES_TABLE . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			author VARCHAR(255) NOT NULL,
			source VARCHAR(255) NOT NULL,
			quote TEXT NOT NULL,
			UNIQUE KEY id (id)
		);");

		$wpdb->query("INSERT INTO " . YARQ_QUOTES_TABLE . " (author, source, quote) VALUES ('WordPress', 'http://wordpress.org', 'Code is poetry.');");
	}
	// update table (if needed)
	$installed_ver = get_option("yarq_db_version");
    if( $installed_ver != YARQ_DB_VERSION ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta("CREATE TABLE " . YARQ_QUOTES_TABLE . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			author VARCHAR(255) NOT NULL,
			source VARCHAR(255) NOT NULL,
			quote TEXT NOT NULL,
			UNIQUE KEY id (id)
		);");

      update_option("yarq_db_version", YARQ_DB_VERSION);
  }
}

//
// Generate links in the admin menu to the YARQ admin pages
//
function yarq_generate_admin_menu()
{
	if (function_exists('add_options_page'))
	{
		add_options_page(__('Yet Another Random Quote', 'yarq'), __('YARQ', 'yarq'), 10, basename(__FILE__), 'yarq_admin_options');
		add_management_page(__('Manage Random Quotes', 'yarq'), __('Quotes', 'yarq'), 10, basename(__FILE__), 'yarq_admin_manage');
	}
}

//
// YARQ Admin options panel
//
function yarq_admin_options()
{
	if (isset($_POST['update_options']))
	{
		update_option('yarq_format', $_POST['yarq_format']);
		echo '<div class="updated"><p><strong>' . __('Options updated.', 'yarq') . '</strong></p></div>';
	}

	echo '<div class="wrap">' . "\n";
	echo '<h2>' . __('Yet Another Random Quote Options', 'yarq') . '</h2>' . "\n";

	echo '<form action="" method="post">' . "\n";

	echo '<fieldset class="options">' . "\n";
	echo '<legend>' . __('Format', 'yarq') . '</legend>' . "\n";
	echo '<p>' . __('The format and style used to display the quote. <code>%quote%</code> is replaced with the random quote, <code>%author%</code> is replaced by the name of the author of the quote, and <code>%source%</code> is replaced by the source of the quote.', 'yarq') . '</p>' . "\n";
	echo '<p><textarea id="yarq_format" name="yarq_format" cols="60" rows="4" style="width: 98%;" class="code">' . stripslashes(get_option('yarq_format')) . '</textarea></p>' . "\n";

	echo '<div class="submit"><input type="submit" name="update_options" value="' . __('Update Options', 'yarq') . '" /></div>' . "\n";

	echo '</form></div>' . "\n";
}

//
// YARQ Admin manage quotes panel
//
function yarq_admin_manage()
{
	global $wpdb;

	//
	// Delete a quote
	//
	if (isset($_GET['delete']))
	{
		$wpdb->query("DELETE FROM " . YARQ_QUOTES_TABLE . " WHERE id = '" . intval($_GET['delete'])  . "'");
		echo '<div class="updated"><p><strong>' . sprintf(__('Quote %d has been deleted.', 'yarq'), intval($_GET['delete'])) . '</strong></p></div>';
	}

	//
	// Add a quote
	//
	if (isset($_POST['add']))
	{
		$errors = array();

		if (empty($_POST['yarq_quote']))
		{
			$errors[] = __('You did not enter a quote.', 'yarq');
		}

		if (empty($_POST['yarq_author']))
		{
			$errors[] = __('You did not enter an author.', 'yarq');
		}

		if (count($errors) > 0)
		{
			echo '<div class="error"><ul>' . "\n";
			foreach ($errors as $error)
			{
				echo '<li><strong>' . __('ERROR', 'yarq') . '</strong>: ' . $error . '</li>' . "\n";
			}
			echo '</ul></div>' . "\n";
		}
		else
		{
			$wpdb->query("INSERT INTO " . YARQ_QUOTES_TABLE . " (author, source, quote) VALUES ('" . $wpdb->escape($_POST['yarq_author']) . "', '" . $wpdb->escape($_POST['yarq_source']) . "', '" . $wpdb->escape($_POST['yarq_quote']) . "');");
			echo '<div class="updated"><p><strong>' . __('Quote added.', 'yarq') . '</strong></p></div>';
		}
	}

	//
	// List quotes
	//

	echo '<div class="wrap">' . "\n";
	echo '<h2>' . __('Manage Quotes', 'yarq') . '</h2>' . "\n";

	$quotes = $wpdb->get_results("SELECT * FROM " . YARQ_QUOTES_TABLE);
	if ($quotes)
	{
		echo '<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3">' . "\n";
		echo '<tr>' . "\n";
		echo '<th scope="col">' . __('ID', 'yarq') . '</th>' . "\n";
		echo '<th scope="col">' . __('Quote', 'yarq') . '</th>' . "\n";
		echo '<th scope="col">' . __('Author', 'yarq') . '</th>' . "\n";
		echo '<th scope="col">' . __('Source', 'yarq') . '</th>' . "\n";
		echo '<th scope="col"></th>' . "\n";
		echo '</tr>' . "\n";

		$alternate = true;
		foreach($quotes as $quote)
		{
			echo '<tr id="quote-' . $quote->id . '"';
			if ($alternate)
			{
				echo ' class="alternate"';
				$alternate = false;
			}
			else
			{
				$alternate = true;
			}

			echo '>' . "\n";
			echo '<th scope="row">' . $quote->id . '</th>' . "\n";
			echo '<td>' . $quote->quote . '</td>' . "\n";
			echo '<td>' . $quote->author . '</td>' . "\n";

			if (!empty($quote->source))
			{
				echo '<td><a href="' . $quote->source . '" title="' . sprintf(__('Source of quote #%d.', 'yarq'), $quote->id) . '">' . $quote->source . '</a></td>' . "\n";
			}
			else
			{
				echo '<td></td>' . "\n";
			}

			echo '<td><a href="edit.php?page=yarq.php&amp;delete=' . $quote->id . '" title="' . sprintf(__('Delete quote #%d.', 'yarq'), $quote->id) . '" class="delete">' . __('Delete', 'yarq') . '</a></td>' . "\n";
			echo '</tr>' . "\n";
		}

		echo '</table>' . "\n";
	}
	else
	{
		echo '<p><strong>' . __('No quotes found.', 'yarq') . '</strong></p>';
	}

	echo '</div>' . "\n";

	//
	// Add form
	//
	echo '<div class="wrap">' . "\n";
	echo '<h2>' . __('Add Quote', 'yarq') . '</h2>' . "\n";

	echo '<form action="edit.php?page=yarq.php" method="post">' . "\n";

	echo '<table class="editform" width="100%" cellspacing="2" cellpadding="5">' . "\n";
	echo '<tr><th scope="row" width="33%">' . __('Quote', 'yarq') . '</th><td width="66%"><textarea id="yarq_quote" name="yarq_quote" rows="4" cols="40" style="width: 98%;" class="code"></textarea></td></tr>' . "\n";
	echo '<tr><th scope="row">' . __('Author', 'yarq') . '</th><td><input name="yarq_author" type="text" id="yarq_author" value="" /></td></tr>' . "\n";
	echo '<tr><th scope="row">' . __('Source URL', 'yarq') . '</th><td><input name="yarq_source" type="text" id="yarq_source" value="" /></td></tr>' . "\n";
	echo '</table>' . "\n";

	echo '<div class="submit"><input type="submit" name="add" value="' . __('Add Quote', 'yarq') . '" /></div>' . "\n";

	echo '</form></div>' . "\n";

	echo '</div>' . "\n";
}

//
// Display a random quote
//
function yarq_display($format = '')
{
	global $wpdb;

	if (empty($format))
	{
		$format = stripslashes(get_option('yarq_format'));
	}

	$quote = $wpdb->get_row("SELECT * FROM " . YARQ_QUOTES_TABLE . " ORDER BY RAND() LIMIT 1");

	$output = str_replace('%quote%', $quote->quote, $format);
	$output = str_replace('%author%', $quote->author, $output);
	echo str_replace('%source%', $quote->source, $output);
}

//
// Display a random quote in a widget (if widgets are available)
//
function widget_yarq_register() {
	if ( function_exists('register_sidebar_widget') ) :
	function widget_yarq($args) {
    extract($args);?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . __('Inspiration', 'yarq')
                . $after_title;
            yarq_display();
			echo $after_widget; ?>
	<?php
	}

	register_sidebar_widget(__('Random Quote', 'yarq'), 'widget_yarq');
	endif;
}
//
// Add hooks
//
//add_action('activate_yarq.php', 'yarq_install');
register_activation_hook(__FILE__,'yarq_install');
add_action('admin_menu', 'yarq_generate_admin_menu');
add_action('init', 'widget_yarq_register');
load_plugin_textdomain('yarq');
?>
