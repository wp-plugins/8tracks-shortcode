<?php

/*
Plugin Name: 8tracks Shortcode Plugin
Plugin URI: http://wordpress.org/extend/plugins/8tracks-shortcode/
Description: Allows you to embed 8tracks playlists via a shortcode.
Version: 1.0
Author: Jonathan Martin
Author URI: http://www.shh-listen.com
License: GPL2 (http://www.gnu.org/licenses/gpl-2.0.html)
*/

/*  Copyright 2011  Jonathan Martin  (email : jon@songsthatsavedyourlife.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*  A huge thanks goes to Justin S, WordPress.com Developer, for his enormous assistance with the plugin!
*/

// Usage: [8tracks url ="" height="some value" width="some value" playops="some value(s)" flash="yes/no" tags="your, favorite, genres" collection="yes/no" perpage="some number"]

// Note:    height, width, and playops are optional. You must specify either a URL, tags, or artist.
// height:  Pick a number, any number.  Standard is 250.
// width:   Yep, pick a number.  Standard is 300.
// playops: Can be set to "shuffle", "autoplay", or "shuffle+autoplay". 
// flash: Can be set to "yes" to use the Flash player for your mixes, or left empty to use the new, HTML5 player.
// tags: Use this if you want to explore by genre. Simply insert a comma-separated list of tags, and you'll get a random mix.
// artist: Use this if you want to search for mixes with a given artist.
// dj: Use this to specify a particular user/dj on 8tracks.
// collection: Set this to "yes" to embed the collection player, which will give you a set of mixes matching your tags or artist.
// mixset: This value is for mixes that are found on the 8tracks site.  (Example: collection:645:favorite-artwork)
// perpage: Set this to the number of mixes you'd like to see on each page of your collection.  Default is 4.
// sort: Can be combined with tags or artist, or stand-alone. Options are "recent", "hot", or "popular".

//Begin Custom Editor Button
function tcustom_addbuttons() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;

	if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "add_tcustom_tinymce_plugin");
		add_filter('mce_buttons', 'register_tcustom_button');
	}
}
function register_tcustom_button($buttons) {
	array_push($buttons, "|", "example");
	return $buttons;
} 
function add_tcustom_tinymce_plugin($plugin_array) {
	$plugin_array['example'] = WP_PLUGIN_URL.'/8tracks-shortcode/8tracks.js';
	return $plugin_array;
}
// init process for button control
add_action('init', 'tcustom_addbuttons');
//End Custom Editor Button

add_shortcode( "8tracks", "eighttracks_shortcode" );

function eighttracks_shortcode( $atts, $content) {
			extract( shortcode_atts ( array(
			'height' => '',
			'width' => '',
			'playops' => '',
			'url' => '',
			'flash' => '',
			'tags' => '',
			'artist' => '',
			'dj' => '',
			'collection' => '',
			'mixset' => '',
			'perpage' => '',
			'sort' => '',
			'lists' => '',
			), $atts ) ); 

// Make sure that a user can only enter a whitelisted set of playops.
	$allowed_playops = array(
		'shuffle',
		'autoplay',
		'shuffle+autoplay',
	);
	if ( !in_array( $playops, $allowed_playops ) )
		$playops = '';

// Make sure flash has a value. Default is no.
	if (!isset( $flash['yes'] ))
		$flash="no";

// Make sure collection has a value. Default is no.
	if (!isset( $collection['yes'] ))
		$collection="no";

// Ensure that specific collections are displayed as collections.
	if (!empty( $mixset )) 
		$collection="yes";

// Make sure the URL we are loading is from 8tracks.com
	if (!empty($url)) {
	$url_bits = parse_url( $url );
	if ( '8tracks.com' != $url_bits['host'] )
		return '';
}

//Make sure the specific mix is of an appropriate size, if no width and height are set.
	if (empty($width) && $collection=="no")
		$width = 300;
	if (empty($height) && $collection=="no")
		$height = 250;

//Make sure the collection is of an appropriate size, if no width and height are set.
	if ($collection=="yes" && (empty($width)))
		$width = 500;
	if ($collection=="yes" && (empty($height)))
		$height = 500;

//Specify a default number of mixes (4) per page of the collection.
	if ($collection=="yes" && (empty($perpage)))
		$perpage = 4;

//Make sure our sort values are valid.
	$allowed_sorts = array(
		'recent',
		'hot',
		'popular',
	);
	if ( !in_array( $sort, $allowed_sorts ) )
		$sort = '';

// Let's make sure our list settings are valid.
	$allowed_lists = array(
		'liked',
		'listen_later',
		'listened',
		'recommended',
	);
	if ( !in_array( $lists, $allowed_lists ) )
		$lists = '';

//We need to do a little extra work to get the correct value when $dj is specified:

	if (!empty($dj)) {
		if (strpos($dj,"http://8tracks.com/") =="true") {
		$dj_body = wp_remote_get ('' . ($dj) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
		} else {
		$dj_body = wp_remote_get ('http://8tracks.com/' . ($dj) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
		}
		//Handle Errors in case DJ returns 404.
		if ( is_wp_error( $dj_body ) || $dj_body['response']['code'] != '200' ) {
		return '';
		} else {
		//Convert the canonical DJ name given by the user to its numerical equivalent.
		$djxml = new SimpleXMLElement( $dj_body['body'] );	
		$dj = ($djxml->user->id);
		}
	}

//Let's combine our $lists value with our numerical $dj value and make a new $mixset value.

	if ((!empty($lists)) && (!empty($dj))) {
		$collection = "yes";
		$newlist = '' . ($lists) . ':' . ($dj) . '';
		$mixset = $newlist;
	}
	
//A little extra work to make the 8track mix sets work properly:
	
	if (!empty($mixset)) {
		if (strpos($mixset,"http://8tracks.com/mix_sets") =="true") {
				$mixset_body = wp_remote_get ('' . ($mixset) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
		} else {
			$mixset_body = wp_remote_get ('http://8tracks.com/mix_sets/' . ($mixset) .'.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
		} 
		//Handle Errors in case MIXSET returns 404.
		if ( is_wp_error( $mixset_body ) || $mixset_body['response']['code'] != '200' ) {
		return '';
		} else {
		//Convert the canonical mixset name to its numerical equivalent.
		$mixsetxml = new SimpleXMLElement( $mixset_body['body'] );
		$mixset = ($mixsetxml->id);
		}
	}

//These arrays contain character substitutions to ensure the URLs are well-formed for querying 8tracks.
	$badchars = array(' ', '.', ',', ', ');
	$goodchars = array('_', '%5E', '%2B', '%2B');
	
//Ok, here's where we convert our 8tracks URL from canonical to numerical, and then go and fetch the mix' xml file.		

	//The basic URL method:
	if (!empty($url)) {
		$the_body = wp_remote_get( esc_url($url) . '.xml' .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	//Here follow mixes where tags, artist, or dj are specified and collection is turned off.
	} else if (!empty($tags) && (empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mixes.xml?tags=' . str_replace($badchars, $goodchars, $tags) .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if (!empty($artist) && (empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mixes.xml?q=' . str_replace($badchars, $goodchars, $artist) .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if (!empty($dj) && (empty($lists)) && (empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/dj:' . str_replace($badchars, $goodchars, $dj) .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	//Here follow mixes where tags, artist, or dj are specified and collection is turned on.
	} else if ((!empty($tags)) && (!empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/tags:' . str_replace($badchars, $goodchars, $tags) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if ((!empty($artist)) && (!empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/artist:' . str_replace($badchars, $goodchars, $artist) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if ((!empty($dj)) && (!empty($collection)) && (empty($lists))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/dj:' . str_replace($badchars, $goodchars, $dj) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	//This handles mixes where sort is set, but collection is off.
	} else if ((!empty($sort)) && (!empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/all:' . ($sort) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	//This handles mix sets found on the 8tracks site.
	} else if (!empty($mixset)) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/' . ($mixset) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	}

//Error handling for URL processing.
	if ( is_wp_error( $the_body ) || $the_body['response']['code'] != '200' )
		return '';

	if ( ! isset( $the_body['body'] ) )
		return '<!-- invalid response -->';

	try {	
		$xml = new SimpleXMLElement( $the_body['body'] );	
	} catch ( Exception $e ) {
		return '<!-- invalid xml -->';
	}
	
//User-Constructed Collection Output (HTML5 only)
if ($collection=="yes" && (!empty($tags))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/tags:' . str_replace($badchars, $goodchars, $tags) . ':' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
} else if ($collection=="yes" && (!empty($artist))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/artist:' . str_replace($badchars, $goodchars, $artist) . ':' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
} else if ($collection=="yes" && ((!empty($dj)) && (empty($lists)))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/dj:' . str_replace($badchars, $goodchars, $dj) . ':' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
} else if ($collection=="yes" && (!empty($sort))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/all:' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
}
//Collections from 8tracks site
  else if (!empty($mixset)) {
	$output = '<iframe src="http://8tracks.com/mix_sets/' . intval($mixset) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
}
//Output mixes where tags, artist, or dj is requested, and Flash is turned on.
  else if ($flash=="yes" && (!empty($tags))) {
	$output = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
	$output .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" ';
	$output .= 'height="' . intval( $height ) . '" width="' .intval( $width ) . '">';
	$output .= '<param name="movie" value="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3/' . $playops .'"></param>';
	$output .= '<param name="allowscriptaccess" value="always"><param name="allowscriptaccess" value="always">';
	$output .= '<embed height="' . intval( $height ) . '" src="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3/' . $playops . '" ';
	$output .= 'pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" ';
	$output .= 'allowscriptaccess="always" height="' . intval( $height ) . '" width="' . intval( $width ) . '"></embed></object>';
} else if ($flash=="yes" && (!empty($artist))) {
	$output = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
	$output .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" ';
	$output .= 'height="' . intval( $height ) . '" width="' .intval( $width ) . '">';
	$output .= '<param name="movie" value="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3/' . $playops .'"></param>';
	$output .= '<param name="allowscriptaccess" value="always"><param name="allowscriptaccess" value="always">';
	$output .= '<embed height="' . intval( $height ) . '" src="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3/' . $playops . '" ';
	$output .= 'pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" ';
	$output .= 'allowscriptaccess="always" height="' . intval( $height ) . '" width="' . intval( $width ) . '"></embed></object>'; 
} else if ($flash=="yes" && ((!empty($dj)) && (empty($lists)))) {
	$output = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
	$output .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" ';
	$output .= 'height="' . intval( $height ) . '" width="' .intval( $width ) . '">';
	$output .= '<param name="movie" value="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3/' . $playops .'"></param>';
	$output .= '<param name="allowscriptaccess" value="always"><param name="allowscriptaccess" value="always">';
	$output .= '<embed height="' . intval( $height ) . '" src="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3/' . $playops . '" ';
	$output .= 'pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" ';
	$output .= 'allowscriptaccess="always" height="' . intval( $height ) . '" width="' . intval( $width ) . '"></embed></object>';
}

//Output mixes where tags, artist, or dj is requested, and HTML5 is turned on.
  else if ($flash=="no" && (!empty($tags))) {
	$output = '<iframe src="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3_universal/' . $playops .'" ';
	$output .= 'width="' .intval( $width ) . '" height="' . intval( $height ) . '" style="border: 0px none;"></iframe>';
} else if ($flash=="no" && (!empty($artist))) {
	$output = '<iframe src="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3_universal/' . $playops .'" ';
	$output .= 'width="' .intval( $width ) . '" height="' . intval( $height ) . '" style="border: 0px none;"></iframe>';
} else if ($flash=="no" && ((!empty($dj)) && (empty($lists)))) {
	$output = '<iframe src="http://8tracks.com/mixes/' . intval($xml->mixes->mix->id) . '/player_v3_universal/' . $playops .'" ';
	$output .= 'width="' .intval( $width ) . '" height="' . intval( $height ) . '" style="border: 0px none;"></iframe>';
}
 
//Output a mix where URL is set and Flash is turned on.
  else if ($flash=="yes" && $tags=="" && $artist=="" && $dj=="") {
	$output = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
	$output .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" ';
	$output .= 'height="' . intval( $height ) . '" width="' .intval( $width ) . '">';
	$output .= '<param name="movie" value="http://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3/' . $playops .'"></param>';
	$output .= '<param name="allowscriptaccess" value="always"><param name="allowscriptaccess" value="always">';
	$output .= '<embed height="' . intval( $height ) . '" src="http://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3/' . $playops . '" ';
	$output .= 'pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" ';
	$output .= 'allowscriptaccess="always" height="' . intval( $height ) . '" width="' . intval( $width ) . '"></embed></object>';
} 
//Output a mix where URL is set and HTML5 is turned on.
  else {
	$output = '<iframe src="http://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3_universal/' . $playops .'" ';
	$output .= 'width="' .intval( $width ) . '" height="' . intval( $height ) . '" style="border: 0px none;"></iframe>';
}		
return $output;
}

//Include Widget Code

include_once dirname( __FILE__ ) . '/widget.php';
?>