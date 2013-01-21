<?php

class eighttracks_widget extends WP_Widget {

  function __construct() {
		$widget_ops = array( 'classname' => '8tracks', 'description' => __('Add an 8tracks mix to your sidebar.') ); 
		$control_ops = array('id_base' => 'eighttracks-widget');
		parent::__construct('eighttracks-widget', __('8tracks'), $widget_ops, $control_ops);
  }

  function widget($args, $instance) {
    extract($args);
    $title    	= apply_filters('widget_title',$instance['title']);
	$url		= trim($instance['eighttracks_url']);
	$height		= trim($instance['eighttracks_height']);
	$width		= trim($instance['eighttracks_width']);
	$flash		= trim($instance['eighttracks_flash']);
	$tags		= trim($instance['eighttracks_tags']);
	$artist		= trim($instance['eighttracks_artist']);
	$dj			= trim($instance['eighttracks_dj']);
	$mixset		= trim($instance['eighttracks_mixset']);
	$collection	= trim($instance['eighttracks_collection']);
	$perpage	= trim($instance['eighttracks_perpage']);
	$sort		= trim($instance['eighttracks_sort']);
	
// Initializing the output code.
	if ($url)  {
	    $output = '';
	
	    $output .= $before_widget;
	                      
	    if ($title) {
	      $output .= $before_title . $title . $after_title;
	    }
	  
		$url_bits = parse_url( $url );
		if ( '8tracks.com' != $url_bits['host'] )
			return '';
		
		$the_body = wp_remote_get( esc_url($url) . '.xml' .'?api_key=d7ac7187f6230c9232f2d7d08dc12e4b50d1b8d8' );
	
		if (is_wp_error($the_body) || $the_body['response']['code'] != '200')
			return '';
	
		if (!isset( $the_body['body']))
			return '';
	
		try {	
			$xml = new SimpleXMLElement($the_body['body']);	
		} catch (Exception $e) {
			return '';
		}
	
		if ($eighttracks_url != '' or $eighttracks_tags != '' or $eighttracks_artist != '' or $eighttracks-dj != '' or $eighttracks_mixset != '') {

		echo do_shortcode('[8tracks url="'.($eighttracks_url).'" mixset="'.($eighttracks_mixset).'" height="'.intval($eighttracks_height).'" width="'.intval($eighttracks_width).'" flash="'.($eighttracks_flash).'" tags="'.str_replace($badchars, $goodchars, $eighttracks_tags).'" artist="'.str_replace($badchars, $goodchars, $eighttracks_artist).'" dj="'.str_replace($badchars, $goodchars, $eighttracks_dj).'" collection="'. ($eighttracks_collection) .'" sort="' . ($eighttracks_sort) . '" perpage="' . intval($eighttracks_perpage) . '"]');
    }
		echo '</div>';
	
	    $output .= $after_widget;    
	    
		echo $output;
	}
	
 }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;
 
    $instance['title']     				= strip_tags($new_instance['title']);
    $instance['eighttracks_url']  		= strip_tags($new_instance['eighttracks_url']);
    $instance['eighttracks_flash']  	= strip_tags($new_instance['eighttracks_flash']);
    $instance['eighttracks_height']     = strip_tags($new_instance['eighttracks_height']);
    $instance['eighttracks_width']   	= strip_tags($new_instance['eighttracks_width']);
	$instance['eighttracks_tags']		= strip_tags($new_instance['eighttracks_tags']);
	$instance['eighttracks_artist']		= strip_tags($new_instance['eighttracks_artist']);
	$instance['eighttracks_dj']			= strip_tags($new_instance['eighttracks_dj']);
	$instance['eighttracks_mixset']		= strip_tags($new_instance['eighttracks_mixset']);
	$instance['eighttracks_collection']	= strip_tags($new_instance['eighttracks_collection']);
	$instance['eighttracks_perpage']	= strip_tags($new_instance['eighttracks_perpage']);
	$instance['eighttracks_sort']		= strip_tags($new_instance['eighttracks_sort']);
	
	return $instance;
  }

  function form($instance){
    $defaults = array('title' => '', 'flash' => 'no', 'eighttracks_height' => '250', 'eighttracks_width' => '250');
    $instance = wp_parse_args( (array) $instance, $defaults);
    
    $title    	= strip_tags($instance['title']);
    $width 		= strip_tags($instance['eighttracks_width']); 
    $height 	= strip_tags($instance['eighttracks_height']); 
    $url    	= strip_tags($instance['eighttracks_url']);
	$flash 		= strip_tags($instance['eighttracks_flash']);
	$tags 		= strip_tags($instance['eighttracks_tags']);
	$artist		= strip_tags($instance['eighttracks_artist']);
	$dj 		= strip_tags($instance['eighttracks_dj']);
	$mixset 	= strip_tags($instance['eighttracks_mixset']);
	$collection = strip_tags($instance['eighttracks_collection']);
	$perpage 	= strip_tags($instance['eighttracks_perpage']);
	$sort	 	= strip_tags($instance['eighttracks_sort']);
	
    ?>
    Widget Title:<br />
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
    <br /><br />
	<b>Random mix?</b> <br /><br />
	Tag(s) (Example: a, b, c):
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_tags'); ?>" name="<?php echo $this->get_field_name('eighttracks_tags'); ?>" value="<?php echo esc_attr($tags); ?>" />
	<br />
	Artist:
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_artist'); ?>" name="<?php echo $this->get_field_name('eighttracks_artist'); ?>" value="<?php echo esc_attr($artist); ?>" />
	<br />
	<hr>
	<b>Specific Mix?</b><br /><br />
    8tracks Mix URL:<br />
    <input type="text" id="<?php echo $this->get_field_id('eighttracks_url'); ?>" name="<?php echo $this->get_field_name('eighttracks_url'); ?>" value="<?php echo $url; ?>" />
    <br /><br />
	Specific DJ:<br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_dj'); ?>" name="<?php echo $this->get_field_name('eighttracks_dj'); ?>" value="<?php echo esc_attr($dj); ?>" />
	<br />
	Specific Collection:<br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_mixset'); ?>" name="<?php echo $this->get_field_name('eighttracks_mixset'); ?>" value="<?php echo esc_attr($mixset); ?>" />
	<br />
	<hr>
	Mix Options:<br />
	Display as Collection? (yes/no)
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_collection'); ?>" name="<?php echo $this->get_field_name('eighttracks_collection'); ?>" value="<?php echo esc_attr($collection); ?>" />
	<br />
	Mixes Per Collection Page:
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_perpage'); ?>" name="<?php echo $this->get_field_name('eighttracks_perpage'); ?>" value="<?php echo esc_attr($perpage); ?>" />
	<br />
	List Type (Optional: recent, hot, popular):
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_sort'); ?>" name="<?php echo $this->get_field_name('eighttracks_sort'); ?>" value="<?php echo esc_attr($sort); ?>" />
	<br />
	Mix Height:<br />
	<input id="<?php echo $this->get_field_id('eighttracks_height'); ?>" name="<?php echo $this->get_field_name('eighttracks_height'); ?>" type="text" value="<?php echo $height; ?>" />
	<br /><br />
	Mix Width:<br />
	<input id="<?php echo $this->get_field_id('eighttracks_width'); ?>" name="<?php echo $this->get_field_name('eighttracks_width'); ?>" type="text" value="<?php echo $width; ?>" />
	<br /><br />
	Use Flash? (yes/no)<br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_flash'); ?>" name="<?php echo $this->get_field_name('eighttracks_flash'); ?>" value="<?php echo esc_attr($flash); ?>" />
	<br />
    <input type="hidden" name="submitted" value="1" />
    <?php
  }
}


add_action('widgets_init', 'eighttracks_widgets_init');

function eighttracks_widgets_init() {
  register_widget('eighttracks_widget');
}
?>
