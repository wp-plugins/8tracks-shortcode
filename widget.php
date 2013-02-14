<?php

class eighttracks_widget extends WP_Widget {

  function __construct() {
		$widget_ops = array( 'classname' => '8tracks', 'description' => __('Add an 8tracks mix or collection to your sidebar.') ); 
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
	$dj		    = trim($instance['eighttracks_dj']);
	$collection	= trim($instance['eighttracks_collection']);
	$perpage	= trim($instance['eighttracks_perpage']);
	$sort		= trim($instance['eighttracks_sort']);
    $smart_id   = trim($instance['eighttracks_smartid']);

// Initializing the output code.
    echo ($args['before_widget']);
    echo ($args['before_title']);
    echo ($title);
    echo ($args['after_title']);
    echo '<div class="textwidget">';
  
  //Outputting the mixes.
    if ($url != '') {
		echo do_shortcode('[8tracks url="'.($url).'" height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'"  collection="no" sort="' . ($sort) . '" perpage="' . intval($perpage) . '"]');
}
    else if ((empty($url)) && (!empty($dj))) {
    	echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'" dj="'.($dj).'" collection="yes" sort="' . ($sort) . '" perpage="' . intval($perpage) . '"]');
}
    else if ((empty($url)) && (!empty($tags))) {
    	echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'" tags="'.($tags).'"  collection="yes" sort="' . ($sort) . '" perpage="' . intval($perpage) . '"]');
}   
    else if ((empty($url)) && (!empty($artist))) {
        echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'" artist="'.($artist).'"  collection="yes" sort="' . ($sort) . '" perpage="' . intval($perpage) . '"]');
}
    else if ( (empty($url)) && (!empty($smart_id))) {
        echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" smart_id="'.($smart_id).'" collection="yes" sort="' . ($sort) . '" perpage="' . intval($perpage) . '"]');
}
    echo '</div>';
    echo ($args['after_widget']);
 }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;
 
    $instance['title']     			        = strip_tags($new_instance['title']);
    $instance['eighttracks_url']  		    = strip_tags($new_instance['eighttracks_url']);
    $instance['eighttracks_flash']  		= strip_tags($new_instance['eighttracks_flash']);
    $instance['eighttracks_height']     	= strip_tags($new_instance['eighttracks_height']);
    $instance['eighttracks_width']   		= strip_tags($new_instance['eighttracks_width']);
    $instance['eighttracks_tags']		    = strip_tags($new_instance['eighttracks_tags']);
    $instance['eighttracks_artist']		    = strip_tags($new_instance['eighttracks_artist']);
    $instance['eighttracks_dj']			    = strip_tags($new_instance['eighttracks_dj']);
    $instance['eighttracks_collection']		= strip_tags($new_instance['eighttracks_collection']);
    $instance['eighttracks_perpage']		= strip_tags($new_instance['eighttracks_perpage']);
    $instance['eighttracks_sort']		    = strip_tags($new_instance['eighttracks_sort']);
    $instance['eighttracks_smartid']        = strip_tags($new_instance['eighttracks_smartid']);
	
	return $instance;
  }

  function form($instance){
    $defaults = array('title' => '', 'flash' => 'no', 'height' => '250', 'width' => '250');
    $instance = wp_parse_args( (array) $instance, $defaults);

    $title    	= strip_tags($instance['title']);
    $width 		= strip_tags($instance['eighttracks_width']); 
    $height 	= strip_tags($instance['eighttracks_height']); 
    $url    	= strip_tags($instance['eighttracks_url']);
	$flash 		= strip_tags($instance['eighttracks_flash']);
	$tags 		= strip_tags($instance['eighttracks_tags']);
	$artist		= strip_tags($instance['eighttracks_artist']);
	$dj 		= strip_tags($instance['eighttracks_dj']);
	$collection = strip_tags($instance['eighttracks_collection']);
	$perpage 	= strip_tags($instance['eighttracks_perpage']);
	$sort	 	= strip_tags($instance['eighttracks_sort']);
	$smart_id   = strip_tags($instance['eighttracks_smartid']);
    
    ?>
    <b>Widget Title:</b><br />
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
    <br /><br />
	<b>Random mix?</b> <br /><br />
	Tag(s):
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_tags'); ?>" name="<?php echo $this->get_field_name('eighttracks_tags'); ?>" value="<?php echo esc_attr($tags); ?>" />
	<br />
	Artist:
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_artist'); ?>" name="<?php echo $this->get_field_name('eighttracks_artist'); ?>" value="<?php echo esc_attr($artist); ?>" />
	<br />
	<hr>
	<b>Specific Mix?</b><br /><br />
    8tracks Mix URL:<br />
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_url'); ?>" name="<?php echo $this->get_field_name('eighttracks_url'); ?>" value="<?php echo $url; ?>" />
    <br /><br />
	Specific DJ:<br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_dj'); ?>" name="<?php echo $this->get_field_name('eighttracks_dj'); ?>" value="<?php echo esc_attr($dj); ?>" />
	<br /><br />
    8tracks Smart ID:<br />
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_smartid'); ?>" name="<?php echo $this->get_field_name('eighttracks_smartid'); ?>" value="<?php echo esc_attr($smart_id); ?>" />
    <br /><br />
	<hr>
	<b>Mix Options:</b><br />
	Mixes Per Collection Page:
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_perpage'); ?>" name="<?php echo $this->get_field_name('eighttracks_perpage'); ?>" value="<?php echo esc_attr($perpage); ?>" />
	<br /><br />
	List Type (Optional: recent, hot, popular):
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_sort'); ?>" name="<?php echo $this->get_field_name('eighttracks_sort'); ?>" value="<?php echo esc_attr($sort); ?>" />
	<br /><br />
	Mix Height:<br />
	<input id="<?php echo $this->get_field_id('eighttracks_height'); ?>" name="<?php echo $this->get_field_name('eighttracks_height'); ?>" type="text" value="<?php echo $height; ?>" />
	<br /><br />
	Mix Width:<br />
	<input id="<?php echo $this->get_field_id('eighttracks_width'); ?>" name="<?php echo $this->get_field_name('eighttracks_width'); ?>" type="text" value="<?php echo $width; ?>" />
	<br /><br />
    <input type="hidden" name="submitted" value="1" />
    <?php
  }
}


add_action('widgets_init', 'eighttracks_widgets_init');

function eighttracks_widgets_init() {
  register_widget('eighttracks_widget');
}
?>
