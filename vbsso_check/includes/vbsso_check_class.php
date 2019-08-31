<?php

class vbsso_check_widget extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    function __construct()
    {
        parent::__construct(
            'vbsso_check_widget', // Base ID
            esc_html__('vBSSO Check', 'vbsso_check_domain'), // Name
            array('description' => esc_html__('Widget that checks vBSSO info', 'vbsso_check_domain'),) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     * @see WP_Widget::widget()
     *
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        ?>
        <form id="check">

            <label for="check_url">Enter URL to check</label>
            <input type="text" value="" id="check_url">
            <br>
            <label for="check_platform_list">Choose platform</label>
            <select id="check_platform_list">
                <option value="vBulletin">vBulletin</option>
                <option value="Dokuwiki">Dokuwiki</option>
                <option value="Drupal">Drupal</option>
                <option value="Joomla">Joomla</option>
                <option value="Magento">Magento</option>
                <option value="Mediawiki">Mediawiki</option>
                <option value="Moodle">Moodle</option>
                <option value="Prestashop">Prestashop</option>
                <option value="WordPress">WordPress</option>
            </select>
            <br>
            <div id="g-recaptcha"></div>
            <input type="submit" value="Check">
        </form>

        <div id="check-result"></div>

        <?php


        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     * @see WP_Widget::form()
     *
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('New title', 'vbsso_check_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'vbsso_check_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     * @see WP_Widget::update()
     *
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';

        return $instance;
    }
}