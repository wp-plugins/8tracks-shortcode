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
			'height' => 250,
			'width' => 300,
			'playops' => '',
			'url' => '',
			'flash' => '',
			'tags' => '',
			'artist' => '',
			'dj' => '',
			'collection' => '',
			'perpage' => '',
			'sort' => '',
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

// Make sure the URL we are loading is from 8tracks.com
	if (!empty($url)) {
	$url_bits = parse_url( $url );
	if ( '8tracks.com' != $url_bits['host'] )
		return '';
}

//Make sure the mix is of an appropriate size, if no width and height are set.
	if ( 200 > $width )
		$width = 300;
	if ( 200 > $height )
		$height = 250;

//Make sure the collection is of an appropriate size, if no width and height are set.
	if ($collection=="yes" && ( 500 > $width))
		$width = 500;
	if ($collection=="yes" && ( 500 > $height))
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
		
//We need to do a little extra work to get the correct value when $dj is specified:

	if (!empty($dj)) {
		$dj_body = wp_remote_get ('http://8tracks.com/' . ($dj) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );	
		$djxml = new SimpleXMLElement( $dj_body['body'] );	
		$dj = ($djxml->user->id);
}

	
//These arrays contain character substitutions to ensure the URLs are well-formed for querying 8tracks.
	$badchars = array(' ', '.', ',', ', ');
	$goodchars = array('_', '%5E', '%2B', '%2B');
	
//Ok, here's where we convert our 8tracks URL from canonical to numerical, and then go and fetch the mix' xml file.		

	if (!empty($url)) {
		$the_body = wp_remote_get( esc_url($url) . '.xml' .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if (!empty($tags) && (empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mixes.xml?tags=' . str_replace($badchars, $goodchars, $tags) .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if (!empty($artist) && (empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mixes.xml?q=' . str_replace($badchars, $goodchars, $artist) .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if (!empty($dj) && (empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/dj:' . str_replace($badchars, $goodchars, $dj) .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if ((!empty($tags)) && (!empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/tags:' . str_replace($badchars, $goodchars, $tags) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if ((!empty($artist)) && (!empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/artist:' . str_replace($badchars, $goodchars, $artist) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if ((!empty($dj)) && (!empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/dj:' . str_replace($badchars, $goodchars, $dj) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
	} else if ((!empty($sort)) && (!empty($collection))) {
		$the_body = wp_remote_get ('http://8tracks.com/mix_sets/all:' . ($sort) . '.xml?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );
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
	
//Collection Output (HTML5 only)
if ($collection=="yes" && (!empty($tags))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/tags:' . str_replace($badchars, $goodchars, $tags) . ':' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
} else if ($collection=="yes" && (!empty($artist))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/artist:' . str_replace($badchars, $goodchars, $artist) . ':' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
} else if ($collection=="yes" && (!empty($dj))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/dj:' . str_replace($badchars, $goodchars, $dj) . ':' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
	$output .= 'width="' . intval( $width ) .'" height="' . ( $height ) . '" ';
	$output .= 'border="0" style="border: 0px none;"></iframe>';
} else if ($collection=="yes" && (!empty($sort))) {
	$output = '<iframe src="http://8tracks.com/mix_sets/all:' . ($sort) . '/player?per_page=' . intval($perpage) . '" ';
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
} else if ($flash=="yes" && (!empty($dj))) {
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
} else if ($flash=="no" && (!empty($dj))) {
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

//Begin Widget Code

wp_register_sidebar_widget(
    'eighttracks_widget',   
    '8Tracks',
    'eighttracks_widget_display',
    array(               
        'description' => 'Insert 8Tracks mixes as a widget'
    )
);

wp_register_widget_control(
	'eighttracks_widget',		// id
	'eighttracks_widget',		// name
	'eighttracks_widget_control'	// callback function
);

//These are the Widget Options

function eighttracks_widget_control($args=array(), $params=array()) {
    //the form is submitted, save into database
    if (isset($_POST['submitted'])) {
    	update_option('eighttracks_widget_title', $_POST['widgettitle']);
    	update_option('eighttracks_widget_eighttracksurl', $_POST['eighttracksurl']);
		update_option('eighttracks_widget_eighttracksheight', $_POST['eighttracksheight']);
		update_option('eighttracks_widget_eighttrackswidth', $_POST['eighttrackswidth']);
		update_option('eighttracks_widget_eighttracksflash', $_POST['eighttracksflash']);
		update_option('eighttracks_widget_eighttrackstags', $_POST['eighttrackstags']);
		update_option('eighttracks_widget_eighttracksartist', $_POST['eighttracksartist']);
		update_option('eighttracks_widget_eighttracksdj', $_POST['eighttracksdj']);
		update_option('eighttracks_widget_eighttrackscollection', $_POST['eighttrackscollection']);
		update_option('eighttracks_widget_eighttrackssort', $_POST['eighttrackssort']);
		update_option('eighttracks_widget_eighttracksperpage', $_POST['eighttracksperpage']);
    }
    //load options
    $widgettitle = get_option('eighttracks_widget_title');
    $eighttracksurl = get_option('eighttracks_widget_eighttracksurl');
	$eighttracksheight = get_option('eighttracks_widget_eighttracksheight');
	$eighttrackswidth = get_option('eighttracks_widget_eighttrackswidth');
	$eighttracksflash = get_option('eighttracks_widget_eighttracksflash');
	$eighttrackstags = get_option('eighttracks_widget_eighttrackstags');
	$eighttracksartist = get_option('eighttracks_widget_eighttracksartist');
	$eighttracksdj = get_option('eighttracks_widget_eighttracksdj');
	$eighttrackscollection = get_option('eighttracks_widget_eighttrackscollection');
	$eighttrackssort = get_option('eighttracks_widget_eighttrackssort');
	$eighttracksperpage = get_option('eighttracks_widget_eighttracksperpage');
    ?>
    Widget Title:<br />
    <input type="text" class="widefat" name="widgettitle" value="<?php echo ($widgettitle); ?>" />
    <br /><br />
	<b>Random mix?</b> <br /><br />
	Tag(s) (Example: a, b, c):
	<input type="text" class="widefat" name="eighttrackstags" value="<?php echo ($eighttrackstags); ?>" />
	<br />
	Artist:
	<input type="text" class="widefat" name="eighttracksartist" value="<?php echo ($eighttracksartist); ?>" />
	<br />
	<hr>
	<b>Specific Mix?</b><br /><br />
    8tracks Mix URL:<br />
    <input type="text" class="widefat" name="eighttracksurl" value="<?php echo ($eighttracksurl); ?>" />
    <br /><br />
	Specific DJ:<br />
	<input type="text" class="widefat" name="eighttracksdj" value="<?php echo ($eighttracksdj); ?>" />
	<br />
	<hr>
	Mix Options:<br />
	Display as Collection? (yes/no)
	<input type="text" class="widefat" name="eighttrackscollection" value="<?php echo ($eighttrackscollection); ?>" />
	<br />
	Mixes Per Collection Page:
	<input type="text" class="widefat" name="eighttracksperpage" value="<?php echo ($eighttracksperpage); ?>" />
	<br />
	List Type (Optional: recent, hot, popular):
	<input type="text" class="widefat" name="eighttrackssort" value="<?php echo ($eighttrackssort); ?>" />
	<br />
	Mix Height:<br />
	<input type="text" class="widefat" name="eighttracksheight" value="<?php echo intval($eighttracksheight); ?>" />
	<br /><br />
	Mix Width:<br />
	<input type="text" class="widefat" name="eighttrackswidth" value="<?php echo intval($eighttrackswidth); ?>" />
	<br /><br />
	Use Flash? (yes/no)<br />
	<input type="text" class="widefat" name="eighttracksflash" value="<?php echo ($eighttracksflash); ?>" />
	<br />
    <input type="hidden" name="submitted" value="1" />
    <?php
}

//This controls the Widget Output.
function eighttracks_widget_display($args=array(), $params=array()) {
    //load options
    $widgettitle = get_option('eighttracks_widget_title');
    $description = get_option('eighttracks_widget_description');
    $eighttracksurl = get_option('eighttracks_widget_eighttracksurl');
	$eighttracksheight = get_option('eighttracks_widget_eighttracksheight');
	$eighttrackswidth = get_option('eighttracks_widget_eighttrackswidth');
	$eighttracksflash = get_option('eighttracks_widget_eighttracksflash');
	$eighttrackstags = get_option('eighttracks_widget_eighttrackstags');
	$eighttracksartist = get_option('eighttracks_widget_eighttracksartist');
	$eighttracksdj = get_option('eighttracks_widget_eighttracksdj');
	$eighttrackscollection = get_option('eighttracks_widget_eighttrackscollection');
	$eighttrackssort = get_option('eighttracks_widget_eighttrackssort');
	$eighttracksperpage = get_option('eighttracks_widget_eighttracksperpage');

    //widget output
    echo ($args['before_widget']);
    echo ($args['before_title']);
    echo ($widgettitle);
    echo ($args['after_title']);
    echo '<div class="textwidget">'.(nl2br($description));
    if ($eighttracksurl != '' or $eighttrackstags != '' or $eighttracksartist != '' or $eighttracksdj != '') {

		echo do_shortcode('[8tracks url="'.($eighttracksurl).'" height="'.intval($eighttracksheight).'" width="'.intval($eighttrackswidth).'" flash="'.($eighttracksflash).'" tags="'.str_replace($badchars, $goodchars, $eighttrackstags).'" artist="'.str_replace($badchars, $goodchars, $eighttracksartist).'" dj="'.str_replace($badchars, $goodchars, $eighttracksdj).'" collection="'. ($eighttrackscollection) .'" sort="' . ($eighttrackssort) . '" perpage="' . intval($eighttracksperpage) . '"]');
    }
    echo '</div>';
	echo ($args['after_widget']);

}
  
?>