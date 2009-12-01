=== Code Snippet ===
Contributors: Roman Roan, Wyatt Neal
Donate Link: 
Tags: code, markup, formatting
Requires at least: 2.0
Tested up to: 2.8.1
Stable tag: 2.1.5

Code Snippet is WordPress plugin for displaying code with highlighting in blog posts using the GeSHi engine.

== Description ==
Code Snippet was created by Roman Roan and originally hosted at http://blog.enargi.com.  It is powered by the GeSHi engine and is quite possibly the best syntax highlighting engine for Wordpress.  A large number of languages are supported and it can be easily extended.  Since his blog is no longer functioning, I'm attempting to keep his wonderful contribution available so I've given it a home at http://blog.hackerforhire.org/code-snippet/.  If you find any bugs with it, let me know and I will do my best to resovle them.

Version history:
 * 2.1.5 Updating tag in plugin causing incorrect version to display
 * 2.1.4 GeSHi engine 1.0.8.4, rendering bug-fix
 * 2.1.2 Update to work with plugin security in 2.8.1
 * 2.0 Tested with Wordpress 2.0
 * 1.5 various improvements
 * 1.4 Fixed: Plugin Options panel is not displayed (Options>Code Highlighting)
 * 1.3 Fixed: RSS feeds got broken with code snippets
 * 1.2 Fixed: invalid css stylesheet link when WordPress is not installed in site root
 * 1.1 Fixed plugin produce errors under PHP 4
 * 1.0 Initial release

== Installation ==
1. Copy archive to wp-content/plugins directory
2. Extract the zip file
3. When extracted properly you should have following directory structure: /wp-content/plugins/codesnippet
4. Since I’ve not tore through the code, the plugin will probably not work if extracted/copied in diffrenet directory.
5. Enable the plugin in the Plugin interface

== Frequently Asked Questions ==

= Do I need to wrap this my code in &#60;code&#62; block? =
No.  Instead, wrap your code in a &#60;pre&#62; block and then use [code lang="lang"][/code] to wrap your code.

= How can I customize the code block layout? =
In the Wordpress Admin interface, under Options -> Code Highlighting, you can specify a custom CSS stlye to wrap your code blocks in.  While the preview button is nice, Firebug would be the tool I would recommend.

= Can I use this with custom wordpress themes? = 
Yes; however, you might need to do a little bit of tweaking.  JR found that CodeSnippet automatically adds a link if you're theme is using wp_head; however, if it isn't, you'll need to add the link to the codesnippet/codesnippet.css stlye sheet to your header and you'll also have to define the 'codesnip-container' element.  The default value is:  border:1px solid #ccc; background:#eee; padding: 5px;margin:10px;

== Usage ==
The original author recommended always wrapping your code in a &#60;pre&#62; tag; however, most times you generally don't need to. To use it, simply surround your code with as such and specify your language type:

[code lang="c"]
int main(int argc, char** argv) { return 0; }
[/code]

And the result is:

int main(int argc, char** argv) { return 0; }

== Screenshots ==

== Supported Languages ==
The following is a list of currently supported languages:

 * actionscript-french
 * actionscript
 * ada
 * apache
 * applescript
 * asm
 * asp
 * bash
 * caddcl
 * cadlisp
 * c_mac
 * c
 * cpp
 * csharp
 * css-gen
 * css
 * delphi
 * diff
 * div
 * dos
 * d
 * eiffel
 * freebasic
 * gml
 * html4strict
 * ini
 * inno
 * java
 * javascript
 * lisp
 * lua
 * matlab
 * mpasm
 * nsis
 * objc
 * oobas
 * oracle8
 * pascal
 * perl
 * php-brief
 * php
 * python
 * qbasic
 * sdlbasic
 * smarty
 * sql
 * vbnet
 * vb
 * vhdl
 * visualfoxpro
 * xml
