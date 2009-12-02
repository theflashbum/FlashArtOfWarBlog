=== RYO Category Visibility ===
Contributors: RichHamilton
Donate link: http://www.ryowebsite.com/
Tags: category, categories, visibility, hidden
Requires at least: 2.8
Tested up to: 2.8
Stable tag: 2.8

RYO Category Visibility hides posts from specified categories, keeping them from appearing
on your front page, archives, searches, even feeds. It also hides categories from lists
that make up menus in the sidebar or elsewhere. An easy-to-use interface makes it easy to 
hide categories.

== Description ==

**RYO Category Visibility** hides posts from specified categories, keeping them from appearing
on your front page, archives, searches, even feeds. It also hides categories from lists
that make up menus in the sidebar or elsewhere. An easy-to-use interface makes it easy to 
hide categories.

You can also hide posts by category from users with a numeric user level below a designated level.

The user interface lets you designate separately which categories should appear in each place.
You can hide posts from a category so they don't appear on the front page, but still have that
category appear in the menu in the sidebar. 

When you add categories they are automatically shown, but can easily be hidden by unchecking
them in the user interface.

Normally you check the categories you want to show. But an option allows you to change the 
user interface so you check the categories you want to hide.

Whichever method you choose, the plugin functions by designating categories that should be
excluded from lists and pages, using standard WordPress selection criteria. On occasion, with
posts in multiple categories, this may not give you the expected results. 

== Installation ==

1. Download and unzip `ryo_catvis.zip`. This will unzip to a folder called `ryo_catvis`.
1. Upload the `ryo_catvis` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. The user interface is in the Settings section of the Dashboard. Make your selections there.

With WordPress 2.8 or later you can use the plugin "Upload" utility to upload and install the plugin.

== Frequently Asked Questions ==

= I have a post in two categories, but the post won't show on my front page. Why? =

Multiple categories creates a special situation. The plugin "hides" posts from certain
categories. Another way of explaining this is that it passes WordPress a list of categories
to "exclude" from the display. If a Post is associated with a hidden category, it won't appear.

= But I checked the categories I want to appear, not the ones I want to hide? =

Yes, but the plugin converts that selection to a list of categories to hide. This way you
can create a new category and those Posts will not be hidden, unless you go to the user
panel and change the settings to hide the category.

= Can you give me an example using this plugin? =

I have a website that serves my web customers, most of whom wouldn't have any idea how to install 
a plugin. I didn’t want plugin Posts to show to my regular users, but wanted to make the plugins 
available to other WordPress users who would want them.

I created a WP Plugins category and unchecked "Front" and "List." This keeps these Posts off the 
home page, and the listing off the sidebar.

I can provide a link to the category page, which lists the plugins. You can see the category page 
and the associated Posts just fine. But that category does not show up in the sidebar, or on the 
home page.

This way, you get access, in fact, anyone can search for them and find them. But they’re not "out there"
on the home page or in category links in the sidebar confusing my regular users.

= My feeds don't seem to reflect my recent changes. How can I fix that? =

Your browser caches feeds. Wait for the cache to expire.


== Screenshots ==

No screenshots.

== Changelog ==

= 2.8.0 =
* Complete rewrite to use WP internals available in WP 2.3+. Not compatible before WP 2.3.
* New logic eliminates most database queries; more efficient, less overhead.

= 1.0.1 =
* Fixed archives so posts show up on category pages but not in other archive pages.

= 1.0.04 =
* Added function, cv_get_posts(), duplicates the WordPress get_posts() function.
* Fixed a problem displaying categories with apostrophes in sidebar. 
* Changed approach to archives, improving results when archives is unchecked.
* A fix for an 'Unknown table wp_post2cat'

= 1.0.03 =
* Fixed problem displaying categories with apostrophes in sidebar. 
* Changed approach to archives, improving results when archives is unchecked.
* Possible fix for Unknown table error but uncertain as to side effects.

= 1.0.02 =
* Added static array storage to cv_visible_cats.
* Removed mysql query from top of cv_alter_vis_catlist to reduce repetitive queries. 

= 1.0.01 =
* Corrected error that left Child Categories off the admin list.
* Restored cat_ID number to listing

= 1.0.0 =
* Initial release.




