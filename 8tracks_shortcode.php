<?php

/*
Plugin Name: 8tracks Shortcode Plugin
Plugin URI: http://www.shh-listen.com/8tracks_shortcode/8tracks_shortcode.zip
Description: Allows you to embed 8tracks playlists via a shortcode.
Version: 0.6
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

// Usage: [8tracks height="some value" width="some value" playops="some value(s)"]URL goes here[/8tracks]

// Note:    height, width, and playops are optional, URL is not.
// height:  Pick a number, any number.  Standard is 250.
// width:   Yep, pick a number.  Standard is 300.
// playops: Can be set to "shuffle", "autoplay", or "shuffle+autoplay". 



add_shortcode( "8tracks", "eighttracks_shortcode" );

function eighttracks_shortcode( $atts, $content) {
			extract( shortcode_atts ( array(
			'height' => '',
			'width' => '',
			'playops' => '',
			'url' => '',
			), $atts ) ); 


//Ok, here's where we convert our 8tracks URL from canonical to numerical
//$foo is the file we want to parse.		
$foo = '' . $content . '.' .xml .'';

//We use curl to go and get it, piping the result to $output.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $foo);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$output = curl_exec($ch);
curl_close($ch);

//Now, we pump our XML structure into $xml.
$xml = new SimpleXMLElement($output);


			if ( $height == '' ) {
					if (!$height || $height == '') $height = '250';
                      } 
              
			if ( $width == '' ) {
                    if (!$width || $width == '') $width =  '300';
              }

//See those 'strip_tags($xml->mix->id)'? That's how we insert the mix ID into our constructed URL.  Awesomeness follows.
			
             return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" height="' . esc_attr( $height ) . '" width="' .esc_attr( $width ) . '"><param name="movie" value="http://8tracks.com/mixes/' . strip_tags($xml->mix->id) . '/player_v3/' . $playops .'"></param><param name="allowscriptaccess" value="always"><param name="allowscriptaccess" value="always"><embed height="' . esc_attr( $height ) . '" src="http://8tracks.com/mixes/' . strip_tags($xml->mix->id) . '/player_v3/' . $playops . '" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" allowscriptaccess="always" height="' . esc_attr( $height ) . '" width="' . esc_attr( $width ) . '" cat="' . $foo . '"></embed></object>';
      }


?>
