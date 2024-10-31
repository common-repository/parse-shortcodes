<?php
/*
Plugin Name: Allow Shortcodes in Text Widgets
Plugin URI: http://www.codeandreload.com/wp-plugins/allow-shortcodes-in-text-widgets/
Description: This plugin allow the blog-owner to enable/disable the usage of shortcodes in the default text widget provided by WordPress along with smilies if enabled for the blog.
Author: Robert Wise
Version: 1.0
Author URI: http://www.codeandreload.com/
*/

include("admin_page.php");

add_action('init', 'parseshortcode_globals_init');

function parseshortcode_globals_init(){
	$mc_ps_parse = explode( "," , get_option(mc_ps_parse));
	if (in_array("shortcode", $mc_ps_parse)){
		add_filter('widget_text', 'do_shortcode');
	}
	if (in_array("smilies", $mc_ps_parse)){
		add_filter('widget_text', 'convert_smilies');
	}
}

?>
