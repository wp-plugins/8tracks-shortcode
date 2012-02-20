=== Plugin Name ===
Contributors: songsthatsaved
Tags: music, 8tracks, mixtape, shortcode
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 0.95

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
	
You can also add the 'height', 'width', and 'playops' parameters to the shortcode.

This would look like: 

	[8tracks url="" height="" width="" playops=""]

	Height and Width are optional, and default to 250 and 300 respectively.

	Playops is optional, and can be set to 'shuffle', 'autoplay', or 'shuffle+autoplay'.  
		These shuffle your mix, autostart your mix, or both.
		
		Note about shuffle: Shuffle is done for each user - on first play - by 8tracks.  
		It's a randomized mix, but you can still exit and resume where you were.


== Installation ==


1. Upload `8tracks_shortcode.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

That's it!  Enjoy!


NOTE: 0.75 uses wp_remote_retrieve_body (from WP's HTTP API) to fetch information from 8tracks.
      If you'd rather use cURL, 0.6 is the version for you!
      
      0.3 will do all the same stuff, except for fetching info 
	  (i.e. auto-converting mix names to numerical IDs).
      
      Both can be found under "other versions" here: 
     
      http://wordpress.org/extend/plugins/8tracks-shortcode/download/


== Changelog ==

= 0.95 =
MINIMUM REQUIRED VERSION

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
