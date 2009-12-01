=== Featured Posts ===
Contributors: Gary R. Hess
Tags: post, index, categories, homepage
Requires at least: 2.6
Tested up to: 2.8
Stable tag: 0.2.1

Display a featured post on your index.php or category pages.

== Description ==

A simple featured posts plugin which allows for a full customizable display. Featured Posts allows you to display a post excerpt or custom excerpt along with an image anywhere outside the_loop.

== Installation ==

1. Upload the entire folder to your /wp-content/plugins/ folder.
2. Go to your plugins page and activate plugin.
3. Go to settings/Featured Posts and enter the ID of the post and select an image to be used along with any other settings you wish to choose.
4. Add <?php if(function_exists('featuredposts')) featuredposts(); ?> anywhere outside the loop.
5. Make any necessary changes to the CSS in the featured-posts.css file. width, font-size and margins are the most common to need changing.

== Frequently Asked Questions ==

= What is the recommended image size? =

Any number at or below the size you chose for thumbnails within the admin panel. This will help minimize bandwidth usage and allow for faster loading.

= How can I add Featured Posts to my homepage only? =

Place <?php if (is_home()) { if(function_exists('featuredposts')) featuredposts(); } ?> within your index.php but outside the loop.

= How can I add Featured Posts to a specific category? =

Place <?php if (is_category('5')) { if(function_exists('featuredposts')) featuredposts(); } ?> within your categories.php file (or index.php file is categories.php doesn't exist).

== Screenshots ==

1. This is the default look of the featured posts plugin.
