=== Plugin Name ===
Contributors: songsthatsaved
Tags: music, 8Tracks, mixtape
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 0.5

Allows you to embed mixtapes from 8tracks.com via a shortcode.

== Description ==

Much like other Wordpress shortcodes do for YouTube or Vimeo, this plugin allows you to embed mixtapes from 8Tracks.com via a shortcode.  

== Usage ==

The syntax is: [8tracks]URL[/8tracks]

URL may contain either the numerical ID of your mix(for example, http://8tracks.com/mixes/388942), 
or the mix's name (for example, http://8tracks.com/songsthatsaved/the-fall-version).  

	NOTE: Regardless of input, the plugin will convert your URL to the format 	
                    'http://8tracks.com/mixes/id/'.  
              This is transparent to the user, and keeps things tidy.

Parameters:
	
You can also add the 'height', 'width', and 'playops' parameters to the shortcode.
This would look like: [8tracks height="" width="" playops=""]URL[/8tracks]

	Height and Width are optional, and default to 250 and 300 respectively.

	Playops is also optional, and can be set to 'shuffle', 'autoplay', or 'shuffle+autoplay'.  
		These shuffle your mix, autostart your mix, or both.
		
		Note about shuffle: Shuffle is done for each user - on first play - by 8tracks.  
		It's a randomized mix, but you can still exit and resume where you were.


== Installation ==


1. Upload `8tracks_shortcode.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

That's it!  Enjoy!


NOTE: 0.5 uses cURL to fetch information from 8tracks.
      If your host doesn't use cURL, 0.3 is the version for you!
      
      0.3 will do all the same stuff, except for auto-converting mix names.
      You can find it under "other versions" here: 
     
      http://wordpress.org/extend/plugins/8tracks-shortcode/download/


== Changelog ==

= 0.5 =
Added some code to convert all URL values to the numerical id style link to the same mix.  


= 0.3 =
Added the "playops" parameter, which allows for shuffling and autoplaying.


= 0.2 =
Cleaned up a minor typo.
Removed an errant '\' from the embedded code (did not affect functionality).

= 0.1 =
Initial release.
