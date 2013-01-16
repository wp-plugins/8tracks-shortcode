=== Plugin Name ===
Contributors: songsthatsaved
Tags: music, 8tracks, mixtape, shortcode
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 0.98

Allows you to embed mixtapes from 8tracks.com via a shortcode.

== Description ==

Much like other WordPress shortcodes do for YouTube or Vimeo, this plugin allows you to embed mixtapes from 8tracks.com via a shortcode.  

== Usage ==

The syntax is: [8tracks url=""]

URL may contain either the numerical ID of your mix(for example, http://8tracks.com/mixes/388942 ), 
or the mix's name (for example, http://8tracks.com/songsthatsaved/the-fall-version ).  

	NOTE: Regardless of input, the plugin will convert your URL to the format 	
                    'http://8tracks.com/mixes/id/'.  
              This is transparent to the user, and keeps things tidy.

Parameters:
	
You can also add the 'height', 'width', 'flash,' and 'playops' parameters to the shortcode.

This would look like: 

	[8tracks url="" height="" width="" playops="" flash=""]

	Height and Width are optional, and default to 250 and 300 respectively.

	Flash allows you to choose whether you want to use the new HTML5 player (default setting), or 
	whether you'd like to stick with Flash.  To do this, add flash="yes" into your shortcode.
	
	Playops is optional, and can be set to 'shuffle', 'autoplay', or 'shuffle+autoplay'.  
		These shuffle your mix, autostart your mix, or both.
		
		Note about shuffle: Shuffle is done for each user - on first play - by 8tracks.  
		It's a randomized mix, but you can still exit and resume where you were.


== Installation ==


1. Upload `8tracks_shortcode.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

That's it!  Enjoy!



== Frequently Asked Questions ==

= I don't want to use HTML5, and I don't want to say flash="yes" in every single shortcode.  What can I do? =

I hear you.  Change is hard.  From your dashboard, go to plugins > editor > 8tracks-shortcode > 8tracks-shortcode.php.

Now, search for " 'flash' => '' " and replace it with " 'flash' => 'yes' ". 
(Ignore the double quotation marks.)

That's it.  You will now default to Flash instead of HTML5.

= Is there any practical reason to change this? = 

Some themes render the iframe that the HTML5 player uses strangely, and then nothing plays.  In these cases,
fallback to Flash is the preferred option.  Additionally, Flash is conspicuously absent on many Apple products.  HTML5 is not. :)

Also, Queen wrote a catchy song about Flash, but so far nothing about HTML5.

== Changelog ==

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
