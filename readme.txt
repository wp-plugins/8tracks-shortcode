=== Plugin Name ===
Contributors: songsthatsaved
Tags: music, 8tracks, mixtape, shortcode
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.0
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to embed mixtapes from 8tracks.com via a shortcode.

== Description ==

Much like other WordPress shortcodes do for YouTube or Vimeo, this plugin allows you to embed mixtapes from 8tracks.com via a shortcode.  

== Usage ==

Some useful syntax examples: 

1. A Specific Mix [8tracks url=""]  
2. A Specific DJ [8tracks dj="some username"]  
3. A Specific Collection [8tracks smart_id="collection url"]  
4. Random Collection from Tags [8tracks tags="some, tags, here"]
5. Random Collection by Artist [8tracks artist="some artist"]
6. Random Collection from 8tracks' Charts [8tracks sort="recent, hot, popular"]

Parameters:
	
You can also add the 'height', 'width', 'flash', 'playops', 'artist', 'dj', 'sort', 'smart_id', and 'tags' parameters to the shortcode.

This would look like: 

[8tracks url="" height="" width="" playops="" flash="" tags="" artist="" dj="" sort="" smart_id=""]

Height and Width are optional, and default to 250 and 300 respectively.
Height and Width for Collections defaults to 500 and 500.
Width for widgets is 100%.

Flash allows you to choose whether you want to use the new HTML5 player (default setting), or 
whether you'd like to stick with Flash.  To do this, add flash="yes" into your shortcode.
	
Playops is optional, and can be set to 'shuffle', 'autoplay', or 'shuffle+autoplay'.  
	These shuffle your mix, autostart your mix, or both.
		
	Note about shuffle: Shuffle is done for each user - on first play - by 8tracks.  
	It's a randomized mix, but you can still exit and resume where you were.
	
NOTE: url cannot be used in conjunction with tags, artist, dj, or smart_id. This should be fairly straightforward, as these
allow you to search for sets of mixes, and url specifies a particular mix.

== Installation ==

The short version:

1. Visit the Plugins Section of your WordPress install and choose "add new."
2. Search for "8tracks shortcode."
3. Select "install now" and activate the plugin.

The longer version:

1. Download the current version from: http://wordpress.org/extend/plugins/8tracks-shortcode/
2. Visit the Plugins Section of your WordPress install and choose "add new."
3. Select "upload" from the links at the top, and then select the file you downloaded.
4. Activate the plugin once the upload completes.

That's it!  Enjoy!



== Frequently Asked Questions ==

= Can I customize the plugin's output? =

Yes!  To help you customize the output via CSS, I've added a div around the iframes, which has this id: "tracks-div."  
The iframe has a class also: "tracks-iframe."  

Just add your custom CSS, and you're good to go!

= I don't want to use HTML5, and I don't want to say flash="yes" in every single shortcode.  What can I do? =

This one's a little more complicated, but not too cumbersome. From your dashboard, go to plugins > editor > 8tracks-shortcode > 8tracks-shortcode.php.

Now, search for " 'flash' => '' " and replace it with " 'flash' => 'yes' ". 
(Ignore the double quotation marks.)

That's it.  You will now default to Flash instead of HTML5.

You should note that 8tracks is no longer updating the Flash player, even though they plan to keep it around for now.

= Is there any practical reason to change this? = 

Some themes render the iframe that the HTML5 player uses strangely, and then nothing plays.  In these cases,
fallback to Flash is the preferred option.  Additionally, Flash is conspicuously absent on many Apple products.  HTML5 is not. :)

Also, Queen wrote a catchy song about Flash, but so far nothing about HTML5.

== Upgrade Notice ==

= 1.0 =
This is a major upgrade. It introduces the ability to add collections, the ability to add multiple widgets, a revamped editor button for easier configuration, and the ability to add mixes by artist, tags, dj, or meta type (new, trending, or popular). You can also customize the output styling (see FAQ).

== Changelog ==

= 1.0 =
You can now have the shortcode display random mixes by specifying tags, artists, or a specific dj.  These features have also been added to the widget, and the button in the post editor.  Support has also been added for the 8tracks' new collection embeds (Example: http://8tracks.com/mix_sets/collection:645:favorite-artwork/player).  It is also possible to customize the plugin's output via CSS, as I have added an apply_filters call to the output.  Enjoy!

= 0.99 =
Added a widget for placing 8tracks mixes in sidebars and footers.  Also added a button to the tinymce editor which will help with adding mixes to your posts.

= 0.98 =
Fixed a typo that would prevent $playops from working in the HTML5 player.  (Thanks, Justin S. of WordPress for catching it!)

= 0.97 =
Added an option to allow the user to specify whether s/he would like to use Flash or HTML5 to play mixes.
This will be the release version, if no bugs are found.

= 0.96 =
This version is the first pass at using 8tracks' HTML5 player (player_v3_universal) rather than Flash.
If you find any bugs, please let me know.  I'd love to get us away from Flash for good, one day. :)

= 0.95 =
Updated the plugin to append an API Key to requests for xml data from 8tracks (in keeping with their new key requirement).  
This should resolve the "blank mix" problem, and let the rock (or hip hop/dubstep/smooth jazz) continue!

= 0.9 =
Lots of excellent security checking, courtesy of Justin S, developer at WordPress.com

= 0.82 =
Added some extra security checks on user-supplied URLs.

= 0.75 =
Modified syntax as some mix titles with special characters weren't working when passed as $content.
Because of this, the 'url' parameter is back in and $content is out.
This will require a slight adjustment to any embedded mixes (see new syntax at top).

= 0.7 =
Replaced cURL with Wordpress' internal http API.
Removed unused 'url' value from 8tracks_shortcode array.

= 0.6 =
Fixed typo.
Added some code to convert all URL values to the numerical id style link to the same mix.  


= 0.3 =
Added the "playops" parameter, which allows for shuffling and autoplaying.


= 0.2 =
Cleaned up a minor typo.
Removed an errant '\' from the embedded code (did not affect functionality).

= 0.1 =
Initial release.
