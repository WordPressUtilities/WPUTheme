<?php

/* ----------------------------------------------------------
  Widget Push v 0.1
---------------------------------------------------------- */

if (!function_exists('wputh_push_register_widgets')) {
    add_action('widgets_init', 'wputh_push_register_widgets');
    function wputh_push_register_widgets() {
        register_widget('wputh_push');
    }
}

class wputh_push extends WP_Widget {
    function wputh_push() {
        parent::WP_Widget(false, '[WPU] Push', array(
            'description' => 'Push'
        ));
    }
    function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Title');
        $link = !empty($instance['link']) ? $instance['link'] : home_url();
        $attachment_id = !empty($instance['attachment_id']) ? $instance['attachment_id'] : 0;
?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr($link); ?>">
        </p>
        <div class="widget_push_uploader" id="<?php echo $this->get_field_id('widget_push_uploader'); ?>">
            <input type="submit" class="button widget_push_uploader__button" value="<?php _e('Select an Image', 'image_widget'); ?>" />
            <div class="image_preview" id="<?php echo $this->get_field_id('preview'); ?>">
            <?php
        $image_attributes = wp_get_attachment_image_src($attachment_id);

        // returns an array
        if ($image_attributes) { ?>
            <img src="<?php echo $image_attributes[0]; ?>" />
            <?php
        } ?>
            </div>
            <input class="attachment_id" type="hidden" id="<?php echo $this->get_field_id('attachment_id'); ?>" name="<?php echo $this->get_field_name('attachment_id'); ?>" value="<?php echo abs($instance['attachment_id']); ?>" />
        </div>
        <script>
        jQuery('#<?php echo $this->get_field_id('widget_push_uploader'); ?>').click(function(e) {
            var item = jQuery(this);
            e.preventDefault();
            var custom_uploader = wp.media({
                multiple: false
            })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                item.find('.image_preview').html('<img src="'+attachment.url+'" alt="" />');
                item.find('.attachment_id').val(attachment.id);
            })
            .open();
        });
        </script>
        <?php
    }
    function update($new_instance, $old_instance) {
        wp_enqueue_media();
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['link'] = (!empty($new_instance['link'])) ? strip_tags($new_instance['link']) : '';
        $instance['attachment_id'] = (!empty($new_instance['attachment_id'])) ? strip_tags($new_instance['attachment_id']) : '';
        return $instance;
    }
    function widget($args, $instance) {
        $title = $instance['title'];
        $link = $instance['link'];
        $image_attributes = wp_get_attachment_image_src($instance['attachment_id']);

        // returns an array
        if (!$image_attributes) {
            return;
        }
        echo $args['before_widget'];
        if (!empty($link)) {
            echo '<a href="' . $link . '" title="' . esc_attr($title) . '">';
        }
        echo '<img src="' . $image_attributes[0] . '" alt="' . esc_attr($title) . '" />';
        if (!empty($link)) {
            echo '</a>';
        }
        echo $args['after_widget'];
    }
}
