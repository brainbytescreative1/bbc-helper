<?php
/**
 * BBC Helper
 *
 * @package       BBC Helper
 * @author        Brain Bytes Creative
 * @version       1.0.8
 *
 * @wordpress-plugin
 * Plugin Name:   BBC Helper
 * Plugin URI:    https://www.brainbytescreative.com/
 * Description:   Helper plugin for BBC websites
 * Version:       1.0.8
 * Author:        Brain Bytes Creative
 * Author URI:    https://www.brainbytescreative.com/
 * Text Domain:   bbc-helper
 * Domain Path:   /languages
 * Requires Plugins: acf-extended, advanced-custom-fields-pro
 * GitHub Plugin URI: https://github.com/brainbytescreative1/bbc-helper
**/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// acf load functions
if ( class_exists('acf') ) {
    // load json
    if ( ! function_exists('bbc_acf_json_load_point') ) {
        add_filter('includes/acf/settings/load_json', 'bbc_acf_json_load_point');
        function bbc_acf_json_load_point( $paths ) {
            $paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';
            return $paths;    
        }
        add_filter( 'acf/settings/load_json', 'bbc_acf_json_load_point' );
    }

    // save json
    if ( ! function_exists('bbc_acf_json_save_point') ) {
        function bbc_acf_json_save_point( $path ) {
            return plugin_dir_path( __FILE__ ) . 'acf-json';
        }
        //add_filter( 'acf/settings/save_json', 'bbc_acf_json_save_point' );
    }
}

// plugin functions
if ( ! function_exists('bbc_helper_theme_functions') ) {
    function bbc_helper_theme_functions(){
        $include_files = Array(
            '/functions/forms.php',
            '/functions/schema.php',
            '/functions/shortcodes.php',
        );

        if ( $include_files ) {
            foreach ($include_files as $file) {
                if(file_exists(__DIR__ . $file)) {
                    include_once(__DIR__ . $file);
                }
            }
        }
    }
    add_action( 'init', 'bbc_helper_theme_functions' );
}