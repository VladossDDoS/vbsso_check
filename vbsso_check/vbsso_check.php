<?php
/**
 * Plugin Name: vBSSO Check
 */

// Load class
require_once (plugin_dir_path(__FILE__) . "/includes/vbsso_check_class.php");

//Load scripts
require_once (plugin_dir_path(__FILE__) . '/includes/vbsso_check_scripts.php');

//Register widget
function register_vbsso_check(){
    register_widget('vbsso_check_widget');
}

//Hook in function
add_action('widgets_init', 'register_vbsso_check');
