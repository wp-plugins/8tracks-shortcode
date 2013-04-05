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

/*  Copyright 2011-13  Jonathan Martin  (email : jon@songsthatsavedyourlife.com)

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

/*  A huge thanks to Justin S, WordPress.com Developer, and Matthew Cieplak at 8tracks.com, for their enormous assistance with the plugin!
*/

/* Usage: [8tracks url ="" height="some value" width="some value" playops="some value(s)" flash="yes/no" tags="your, favorite, genres" collection="yes/no"]

 Note:    height, width, and playops are optional. You must specify either a URL, some tags, a dj, an artist, or a particular collection or mix set.
 height:      Pick a number, any number.  Standard for single mixes is 250, and 500 for collections.
 width:       Yep, pick a number.  Standard is 300 for single mixes, and 500 for collections.
 playops:     Can be set to "shuffle", "autoplay", or "shuffle+autoplay". 
 flash:       Can be set to "yes" to use the Flash player for your mixes, or left empty to use the default HTML5 player.
 tags:        Use this if you want to explore by genre. Simply insert a comma-separated list of tags, and you'll get a random mix.
 artist:      Use this if you want to search for mixes with a given artist.
 dj:          Use this to specify a particular user/dj on 8tracks.
 smart_id:    This allows you to copy a smart id from the 8tracks site in order to generate a collection.
 sort:        Can be combined with tags or artist, or used on its own. Options are "recent", "hot", or "popular".
*/

//Some useful global values for retrieving mixes.
define( 'api_key', '?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
define( 'api_version', '&api_version=2' );

//Begin Custom Editor Button
function tcustom_addbuttons() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;

	if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "add_tcustom_tinymce_plugin");
		add_filter('mce_buttons', 'register_tcustom_button');
}}

function register_tcustom_button($buttons) {
	array_push($buttons, "|", "eighttracks_button");
	return $buttons;
} 
function add_tcustom_tinymce_plugin($plugin_array) {
	$plugin_array['eighttracks_button'] = WP_PLUGIN_URL.'/8tracks-shortcode/8tracks.js';
	return $plugin_array;
}

add_action('init', 'tcustom_addbuttons');
//End Custom Editor Button

function add_widget_script() {
  wp_enqueue_script( 'widgets.php', WP_PLUGIN_URL.'/8tracks-shortcode/widget.js');
}

add_action('admin_enqueue_scripts', 'add_widget_script');
// init process for button control

add_shortcode( "8tracks", "eighttracks_shortcode" );

function eighttracks_shortcode( $atts, $content) {
			extract( shortcode_atts ( array(
			'height' => '',
			'width' => '',
			'playops' => '',
			'url' => NULL,
			'flash' => '',
			'tags' => NULL,
			'artist' => NULL,
			'dj' => NULL,
			'collection' => '',
			'smart_id' => NULL,
			'sort' => '',
			'lists' => '',
			), $atts, 'eighttracks' ) ); 

//If anything other than a URL is defined, you probably want a collection.
    if (isset($url)) {
        $collection = "no";
}   else {
		$collection = "yes";
}

// Let's set some default mix size parameters.
	if ((empty($width)) && (empty($height))) {
		
//Make sure the collection is of an appropriate size, if no width and height are set, and you haven't said you don't want a collection.
		if (($collection=="yes")) {
			$width = 500;
			$height = 500;
		//Make sure the specific mix is of an appropriate size, if no width and height are set.
	} 	else if ($collection=="no") {
			$width = 300;
			$height = 250;
	}
}

// Make sure that a user can only enter a whitelisted set of playops.
	$allowed_playops = array(
		'shuffle',
		'autoplay',
		'shuffle+autoplay',
	);
	if ( !in_array( $playops, $allowed_playops ) )
		$playops = '';

//Tweak the playops for collections:
	if ($playops=="shuffle" || $playops=="autoplay") {
		$options = '&options=' . ($playops) . '';
} 	else if ($playops=="shuffle+autoplay") {
		$options = "&options=shuffle,autoplay";
}
		
// Make sure flash has a value. Default is no.
	if (!isset( $flash['yes'] ))
		$flash="no";

// Make sure the URL we are loading is from 8tracks.com
	if (isset($url)) {
	$url_bits = parse_url( $url );
	if ( '8tracks.com' != $url_bits['host'] )
		return '';
}

//Make sure our sort values are valid.
	$allowed_sorts = array(
		'recent',
		'hot',
		'popular',
	);
	if ( !in_array( $sort, $allowed_sorts ) ) {
		$sort = '';
}  
//Make sure our list settings are valid.
	$allowed_lists = array(
		'liked',
		'listen_later',
		'listened',
		'recommended',
	);
	if ( !in_array( $lists, $allowed_lists ) )
		$lists = '';
        
//These arrays contain character substitutions to ensure the URLs are well-formed for querying 8tracks.
	$badchars = array(' ', '_', '/', '.', ',', ', ');
	$goodchars = array('_', '__', '\\', '%5E', '%2B', '%2B');

//We should probably make sure our smart_id is free of non-id elements before processing.
    $needle = "http://8tracks.com/mix_sets/";
        
    if ((strpos($smart_id, $needle)) !== false) {    
        $smart_id = str_replace("http://8tracks.com/mix_sets/", "", $smart_id);
}

//We'll also make sure that any DJ URLs are stripped down to just the DJ's ID.
    $dj_needle = "http://8tracks.com/";
    
    if ((strpos($dj, $dj_needle)) !== false) {
        $dj = str_replace("http://8tracks.com/", "", $dj);
}

//Let's do some mix set processing:
    if (is_null($url)) {
    
//Did we specify a sort?  Let's make sure that works.
    if ((in_array( $sort, $allowed_sorts )) && ((isset($tags)) || (isset($artist)) || (isset($dj)))) {
        $sort = ':' . ($sort) . '';
}
//Here, we create the smart id from tags or artist:
    if (isset($tags)) 
        $smart_id = 'tags:' . str_replace($badchars, $goodchars, $tags) . '' . ($sort) . '';
    if (isset($artist))
        $smart_id = 'artist:' . str_replace($badchars, $goodchars, $artist) . '' . ($sort) . '';

//We also need to make sure that smart IDs we copy from 8tracks have their characters escaped.
    if (isset($smart_id)) {
        $smart_id = str_replace($badchars, $goodchars, $smart_id);
}

//This handles collections made from smart_id, dj, or sort.
      	
    if (!is_null($smart_id)) {       
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/' . ($smart_id) . '.xml' . (api_key) . '' );
}   
    else if (!empty($dj)) { //Not escaping dj strings fixes the problem of missing DJ sets from users with _ in their name.
        $the_body = wp_remote_get ('http://8tracks.com/' . ($dj) . '.xml' . (api_key) . '' );
} 	
    else if (!empty($sort)) {   //This handles collections where only sort is set.
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/all:' . ($sort) . '.xml' . (api_key) . '' );
}
    
//Error handling for URL processing.
	if ( is_wp_error( $the_body ) || $the_body['response']['code'] != '200' )
		return '';

	if ( ! isset( $the_body['body'] ) )
		return '<!-- invalid response -->';

	try {	
		$xml = new SimpleXMLElement( $the_body['body'] );	
} 	catch ( Exception $e ) {
		return '<!-- invalid xml -->';
}
 //Collection processing:
    if ((!empty($smart_id)) && (empty($dj))) { //This handles smart-ids (as distinct from DJs).
		$output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http://8tracks.com/mix_sets/' . intval($xml->id) . '/player?' . ($options) . '" ';
		$output .= 'width="' . intval( $width ) .'" height="' . intval( $height ) . '" ';
		$output .= 'border="0" style="border: 0px none;"></iframe></div>';
}   
    else if (!empty($sort)) { //This handles meta lists.  That is: new, trending, or popular.
		$output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http://8tracks.com/mix_sets/' . intval($xml->id) . '/player?' . ($options) . '" ';
		$output .= 'width="' . intval( $width ) .'" height="' . intval( $height ) . '" ';
		$output .= 'border="0" style="border: 0px none;"></iframe></div>';
}
    else if ((!empty($lists)) && (!empty($dj))) {  // This is a collection made from lists (recent, popular, etc.).
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http://8tracks.com/mix_sets/' . ($lists) . ':' . intval($xml->user->id) . '/player?' . ($options) . '" ';
		$output .= 'width="' . intval( $width ) .'" height="' . intval( $height ) . '" ';
		$output .= 'border="0" style="border: 0px none;"></iframe></div>';
} 	
    else if (!empty($dj)) {  //This handles DJs.
        $output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http://8tracks.com/mix_sets/dj:' . intval($xml->user->id) . '/player?' . ($options) . '" ';
		$output .= 'width="' . intval( $width ) .'" height="' . intval( $height ) . '" ';
		$output .= 'border="0" style="border: 0px none;"></iframe></div>';
    }  
}
   
//This is for single mix processing:
    if (!is_null($url)) {
		$the_body = wp_remote_get( esc_url($url) . '.xml' .'' . (api_key) . '' . (api_version) . '' );

//Error handling for URL processing.
	if ( is_wp_error( $the_body ) || $the_body['response']['code'] != '200' )
		return '';

	if ( ! isset( $the_body['body'] ) )
		return '<!-- invalid response -->';

	try {	
		$xml = new SimpleXMLElement( $the_body['body'] );	
} 	catch ( Exception $e ) {
		return '<!-- invalid xml -->';
}

//Output a mix where URL is set and HTML5 is turned on.
	if ($flash=="no" && (!is_null($url))) { 
		$output = '<div class="tracks-div"><iframe class="tracks-iframe" src="http://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3_universal/' . $playops .'" ';
		$output .= 'width="' .intval( $width ) . '" height="' . intval( $height ) . '" style="border: 0px none;"></iframe></div>';
}   
	else if ($flash=="yes") {  //This is a single mix with Flash.
		$output = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
		$output .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" ';
		$output .= 'height="' . intval( $height ) . '" width="' .intval( $width ) . '">';
		$output .= '<param name="movie" value="http://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3/' . $playops .'"></param>';
		$output .= '<param name="allowscriptaccess" value="always"><param name="allowscriptaccess" value="always">';
		$output .= '<embed height="' . intval( $height ) . '" src="http://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3/' . $playops . '" ';
		$output .= 'pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" ';
		$output .= 'allowscriptaccess="always" height="' . intval( $height ) . '" width="' . intval( $width ) . '"></embed></object>';
    }
}   
		
$output = apply_filters('eighttracks_shortcode', $output, $atts);
if ( $output != '' )
	return $output;
}

//Include Widget Code

include_once dirname( __FILE__ ) . '/widget.php';
?>