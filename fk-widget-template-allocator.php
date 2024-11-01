<?php
/*
Plugin Name: Widget: FK Template Allocator
Plugin URI: http://filipekiss.com.br/wordpress/widget-template-allocator/english
Description: Allows you to insert a template part in a sidebar of your blog.
Version: 1.0.0
Author: Filipe Kiss
Author URI: http://filipekiss.com.br/
*/



add_action('widgets_init', create_function('', 'return register_widget("fk_template_allocator");'));

class fk_template_allocator extends WP_Widget
{
    protected $_templates = array();

    function fk_template_allocator()
    {
		$widget_ops = array('classname' => 'fk_template_allocator_widget', 'description' => 'Includes a template file');
		$this->WP_Widget('fk_template_allocator_widget', 'FK Template Allocator', $widget_ops);
    }

    function widget($args, $instance)
    {
        global $wpdb;
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
		if(@$instance['templatename']):
			$terms = explode('-',$instance['templatename']);
			echo (@$instance['container']) ? $before_widget : "";
			if($title){
				echo $before_title . $title . $after_title;
			}
			if(file_exists(TEMPLATEPATH."/".$instance['templatename'].".php")):
				?>
					<div class="widget-container">
				<?php
				get_template_part($instance['templatename']);
				$instance['error'] = "";
				?>
					</div>
				<?php
			else:
				$instance['error'] = "File not found";
			endif;
			echo (@$instance['container']) ? $after_widget : "";
		endif;
	}

	function form($instance)
    {
		$title = $instance['title'];
		$container = $instance['container'];
		if(file_exists(TEMPLATEPATH."/".$instance['templatename'].".php")):
			$instance['error'] = "";
		else:
			$instance['error'] = "File not found";
		endif;
		
		$instance = wp_parse_args((array) $instance, array(
			'title' => '',
			'templatename' => 'slug-name',
			'container' => ''
        ));
    ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('container'); ?>" name="<?php echo $this->get_field_name('container'); ?>" <?php checked( $container ); ?> />
		<label for="<?php echo $this->get_field_id('container'); ?>"><?php _e( 'Use Wrapper?' ); ?></label><br />
		
		<p>Template to load: (uses Wordpress <i>get_template_part</i> function)</p>
		<input type="text" name="<?php echo $this->get_field_name("templatename") ?>" value="<?php echo $instance['templatename'] ?>" id="<?php echo $this->get_field_id("templatename"); ?>">
		<?php if($instance['error'] != ""): ?>
			<div><strong>File not found</strong></div>
		<?php endif; ?>
    <?php
	}

	function update($new_instance, $old_instance)
    {
		$instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['templatename'] = $new_instance['templatename'];
		$instance['container'] = !empty($new_instance['container']) ? 1 : 0;
        return $instance;
	}
}
