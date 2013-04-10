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
		echo do_shortcode('[8tracks url="'.($url).'" height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'"  collection="no" sort="' . ($sort) . '"]');
}
    else if ((empty($url)) && (!empty($dj))) {
    	echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'" dj="'.($dj).'" collection="yes" sort="' . ($sort) . '"]');
}
    else if ((empty($url)) && (!empty($tags))) {
    	echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'" tags="'.($tags).'"  collection="yes" sort="' . ($sort) . '"]');
}   
    else if ((empty($url)) && (!empty($artist))) {
        echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" flash="'.($flash).'" artist="'.($artist).'"  collection="yes" sort="' . ($sort) . '"]');
}
    else if ( (empty($url)) && (!empty($smart_id))) {
        echo do_shortcode('[8tracks height="'.intval($height).'" width="'.intval($width).'" smart_id="'.($smart_id).'" collection="yes" sort="' . ($sort) . '"]');
}
    echo '</div>';
    echo ($args['after_widget']);
 }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;
 
    $instance['title']                      = strip_tags($new_instance['title']);
    $instance['eighttracks_embed_type']     = strip_tags($new_instance['eighttracks_embed_type']);
    $instance['eighttracks_url']            = strip_tags($new_instance['eighttracks_url']);
    $instance['eighttracks_flash']          = strip_tags($new_instance['eighttracks_flash']);
    $instance['eighttracks_height']         = strip_tags($new_instance['eighttracks_height']);
    $instance['eighttracks_width']          = strip_tags($new_instance['eighttracks_width']);
    $instance['eighttracks_tags']           = strip_tags($new_instance['eighttracks_tags']);
    $instance['eighttracks_artist']         = strip_tags($new_instance['eighttracks_artist']);
    $instance['eighttracks_dj']             = strip_tags($new_instance['eighttracks_dj']);
    $instance['eighttracks_collection']     = strip_tags($new_instance['eighttracks_collection']);
    $instance['eighttracks_sort']           = strip_tags($new_instance['eighttracks_sort']);
    $instance['eighttracks_smartid']        = strip_tags($new_instance['eighttracks_smartid']);
	
	return $instance;
  }

  function form($instance){
    $defaults = array('eighttracks_embed_type' => 'mix', 'title' => '', 'flash' => 'no', 'height' => '250', 'width' => '100%');
    $instance = wp_parse_args( (array) $instance, $defaults);

    $title      = strip_tags($instance['title']);
    $embed_type = strip_tags($instance['eighttracks_embed_type']);
    $width      = strip_tags($instance['eighttracks_width']); 
    $height     = strip_tags($instance['eighttracks_height']); 
    $url        = strip_tags($instance['eighttracks_url']);
    $flash      = strip_tags($instance['eighttracks_flash']);
    $tags       = strip_tags($instance['eighttracks_tags']);
    $artist     = strip_tags($instance['eighttracks_artist']);
    $dj         = strip_tags($instance['eighttracks_dj']);
    $collection = strip_tags($instance['eighttracks_collection']);
    $sort       = strip_tags($instance['eighttracks_sort']);
    $smart_id   = strip_tags($instance['eighttracks_smartid']);
    
    ?>

    <p>
        Title:<br />
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
    </p>

    <p>Type:<br />
    <select class="eighttracks_embed_type" id="<?php echo $this->get_field_id('eighttracks_embed_type'); ?>" name="<?php echo $this->get_field_name('eighttracks_embed_type'); ?>" data-container="<?php echo $this->get_field_id('eighttracks_embed_options'); ?>">
        <option value="mix" <?php echo($embed_type == 'mix' ? 'selected' : '') ?>>Mix</option>
        <option value="collection" <?php echo($embed_type == 'collection' ? 'selected' : '') ?>>Collection</option>
        <option value="dj" <?php echo($embed_type == 'dj' ? 'selected' : '') ?>>DJ's latest mixes</option>
        <option value="artist" <?php echo($embed_type == 'artist' ? 'selected' : '') ?>>Artist search</option>
        <option value="tags" <?php echo($embed_type == 'tags' ? 'selected' : '') ?>>Tag Search</option>
    </select>
    </p>
    

    <div id="<?php echo $this->get_field_id('eighttracks_embed_options'); ?>">

        <!-- begin dynamic options -->
        <P>
        <div class="eighttracks_mix_options">
            Mix URL:<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_url'); ?>" name="<?php echo $this->get_field_name('eighttracks_url'); ?>" value="<?php echo $url; ?>" />
        </div>

        <div class="eighttracks_collection_options" style="display: none;">
            Smart ID (replace with collection URL):<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_smartid'); ?>" name="<?php echo $this->get_field_name('eighttracks_smartid'); ?>" value="<?php echo esc_attr($smart_id); ?>" />
        </div>

        <div class="eighttracks_dj_options" style="display: none;">
            Specific DJ:<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_dj'); ?>" name="<?php echo $this->get_field_name('eighttracks_dj'); ?>" value="<?php echo esc_attr($dj); ?>" />
            <!--label for="eighttracksList">Show:</label>
            <select name="eighttracksList" id="eighttracksList" style="width: 50%;">
                <option value="">DJ's latest mixes</option>
                <option value="liked">Liked</option>
                <option value="listen_later">Listen Later</option>
                <option value="listened">History</option>
                <option value="recommended">Recommended</option>
            </select-->
        </div>

        <div class="eighttracks_tags_options" style="display: none;">
            Tag(s):<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_tags'); ?>" name="<?php echo $this->get_field_name('eighttracks_tags'); ?>" value="<?php echo esc_attr($tags); ?>" />
        </div>

        <div class="eighttracks_artist_options" style="display: none;">
            Artist:
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_artist'); ?>" name="<?php echo $this->get_field_name('eighttracks_artist'); ?>" value="<?php echo esc_attr($artist); ?>" />
        </div>

        <div class="eighttracks_sort_options" style="display: none;">
            Sort: <span class="color: #ccc;">optional: hot, new, popular
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_sort'); ?>" name="<?php echo $this->get_field_name('eighttracks_sort'); ?>" value="<?php echo esc_attr($sort); ?>" />
        </div>
        </p>

        <!-- end dynamic options -->    

        <p>
            Width:<br />
            <input id="<?php echo $this->get_field_id('eighttracks_width'); ?>" name="<?php echo $this->get_field_name('eighttracks_width'); ?>" type="text" value="<?php echo $width; ?>" class="eighttracks_width" placeholder="100%"/>
        </p>

    	<p>
            Height:<br />
    	   <input id="<?php echo $this->get_field_id('eighttracks_height'); ?>" name="<?php echo $this->get_field_name('eighttracks_height'); ?>" type="text" value="<?php echo $height; ?>" class="eighttracks_height" placeholder="250"/>
    	</p>

        <input type="hidden" name="submitted" value="1" />
    </div>

    <script type="text/javascript">
        jQuery('select.eighttracks_embed_type').trigger('change'); //initialize to saved values on load
    </script>

    <?php
  }
}


add_action('widgets_init', 'eighttracks_widgets_init');

function eighttracks_widgets_init() {
  register_widget('eighttracks_widget');
}
?>
