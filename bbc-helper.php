<?php
/**
 * BBC Helper
 *
 * @package       BBC
 * @author        Brain Bytes Creative
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   BBC Helper
 * Plugin URI:    https://www.brainbytescreative.com/
 * Description:   Helper plugin for BBC websites
 * Version:       1.0.0
 * Author:        Brain Bytes Creative
 * Author URI:    https://www.brainbytescreative.com/
 * Text Domain:   bbc-helper
 * Domain Path:   /languages
 * GitHub Plugin URI: https://github.com/brainbytescreative1/bbc-helper
**/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/*** acf load functions ***/
if ( class_exists('acf') ) {

    // load json
    if ( ! function_exists('bbc_acf_json_load_point') ) {
        add_filter('includes/acf/settings/load_json', 'bbc_acf_json_load_point');
        function bbc_acf_json_load_point( $paths ) {
            unset($paths[0]);
            $paths[] = plugin_dir_path( __FILE__ ) . '/acf-json';
            return $paths;    
        }
        add_filter( 'acf/settings/load_json', 'bbc_acf_json_load_point' );
    }

    // save json
    if ( ! function_exists('bbc_acf_json_save_point') ) {
        function bbc_acf_json_save_point( $path ) {
            return plugin_dir_path( __FILE__ ) . 'acf-json';
        }
        add_filter( 'acf/settings/save_json', 'bbc_acf_json_save_point' );
    }

}

/*** plugin functions ***/
if ( class_exists('acf') ) {

}