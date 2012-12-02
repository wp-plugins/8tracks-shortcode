<?php

/*
Plugin Name: 8tracks Shortcode Plugin
Plugin URI: http://wordpress.org/extend/plugins/8tracks-shortcode/
Description: Allows you to embed 8tracks playlists via a shortcode.
Version: 0.96
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

// Usage: [8tracks url ="" height="some value" width="some value" playops="some value(s)" useflash="yes|no"]

// Note:     height, width, and playops are optional, URL is not.
// height:   Pick a number, any number.  Standard is 250.
// width:    Yep, pick a number.  Standard is 300.
// playops:  Can be set to "shuffle", "autoplay", or "shuffle+autoplay". 



add_shortcode( "8tracks", "eighttracks_shortcode" );

function eighttracks_shortcode( $atts, $content) {
			extract( shortcode_atts ( array(
			'height' => 250,
			'width' => 300,
			'playops' => '',
			'url' => '',
			), $atts ) ); 

// Make sure that a user can only enter a whitelisted set of options.
	$allowed_playops = array(
		'shuffle',
		'autoplay',
		'shuffle+autoplay',
	);
	if ( !in_array( $playops, $allowed_playops ) )
		$playops = '';

	// Make sure the URL we are loading is from 8tracks.com
	$url_bits = parse_url( $url );
	if ( '8tracks.com' != $url_bits['host'] )
		return '';

	if ( 200 > $width )
		$width = 300;
	if ( 200 > $height )
		$height = 250;

//Ok, here's where we convert our 8tracks URL from canonical to numerical,
	//and then go and fetch the mix' xml file.		
	$the_body = wp_remote_get( esc_url($url) . '.xml' .'?api_key=5b82285b882670e12d33862f4e79cf950505f6ae' );

	if ( is_wp_error( $the_body ) || $the_body['response']['code'] != '200' )
		return '';

	if ( ! isset( $the_body['body'] ) )
		return '<!-- invalid response -->';

	try {	
		$xml = new SimpleXMLElement( $the_body['body'] );	
	} catch ( Exception $e ) {
		return '<!-- invalid xml -->';
	}


		$output = '<iframe src="http://8tracks.com/mixes/' . intval($xml->mix->id) . '/player_v3_universal' . $playops .'" ';
		$output .= 'width="' .intval( $width ) . '" height="' . intval( $height ) . '" style="border: 0px none;"></iframe>';
		$output .= ' <p class="_8t_embed_p" style="font-size: 11px; line-height: 12px;">';
		$output .= '<a href="http://8tracks.com' . strval($xml->mix->path) .'">' . strval($xml->mix->name) . '</a> from ';
		$output .= '<a href="http://8tracks.com/' . strval($xml->mix->user->slug) . '">' . strval($xml->mix->user->login) . '</a> on ';
		$output .= '<a href="http://8tracks.com">8tracks Radio</a>.';
		return $output;


?>
