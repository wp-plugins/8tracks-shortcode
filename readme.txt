=== Plugin Name ===
Contributors: songsthatsaved
Tags: music, 8Tracks, mixtape
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 0.3

Allows you to embed mixtapes from 8Tracks.com via a shortcode.

== Description ==

Much like other Wordpress shortcodes do for YouTube or Vimeo, this plugin allows you to embed mixtapes from 8Tracks.com via a shortcode.  

== Usage ==

The syntax is: [8tracks]URL[/8tracks]

URL may contain either the numerical ID of your mix(for example, http://8tracks.com/mixes/388942), 
or the mix's name (for example, http://8tracks.com/songsthatsaved/the-fall-version).  

--->	UNLESS, you are using the "playops" feature! In this case, you must use the numerical link.  
	
	Here's how to get it:
	
	1) Find a mix you like, and mouse over it.
	2) Right-click and choose "copy link" (or similar). 
	3) Paste it where you can look at it, and copy the number that follows 'mix_set_id='
	4) Now, make a URL like this: http://8tracks.com/mixes/"The number of the mix"
	5) Paste as above, and have fun!

You can also add the 'height', 'width', and 'playops' parameters to the shortcode.
This would look like: [8tracks height="" width="" playops=""]URL[/8tracks]

	Height and Width are optional, and default to 250 and 300 respectively.

	Playops is also optional, and can be set to 'shuffle', 'autoplay', or 'shuffle+autoplay'.  
	These shuffle your mix, autostart your mix, or both.


== Installation ==


1. Upload `8tracks_shortcode.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

That's it!  Enjoy!


== Changelog ==

= 0.3 =
Added the "playops" parameter, which allows for shuffling and autoplaying.


= 0.2 =
Cleaned up a minor typo.
Removed an errant '\' from the embedded code (did not affect functionality).

= 0.1 =
Initial release.
