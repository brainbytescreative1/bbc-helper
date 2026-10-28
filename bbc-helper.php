<?php
/**
 * BBC Helper
 *
 * @package       BBC Helper
 * @author        Brain Bytes Creative
 * @version       1.5.3
 *
 * @wordpress-plugin
 * Plugin Name:   BBC Helper
 * Plugin URI:    https://www.brainbytescreative.com/
 * Description:   Helper plugin for BBC websites
 * Version:       1.5.3
 * Author:        Brain Bytes Creative
 * Author URI:    https://www.brainbytescreative.com/
 * Text Domain:   bbc-helper
 * Domain Path:   /languages
 * Requires Plugins: acf-extended, advanced-custom-fields-pro
 * GitHub Plugin URI: https://github.com/brainbytescreative1/bbc-helper
**/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined('BBC_HELPER_VERSION') ) {
    define('BBC_HELPER_VERSION', '1.5.3');
}

// acf load functions
if ( class_exists('acf') ) {

    // only the local starter site that pushes to Github saves field groups into the plugin
    $bbc_is_starter_site = ( wp_get_environment_type() === 'local' && get_bloginfo( 'name' ) === 'Pico Starter Site' );

    // load json
    if ( ! function_exists('bbc_acf_json_load_point') ) {
        //add_filter('includes/acf/settings/load_json', 'bbc_acf_json_load_point');
        function bbc_acf_json_load_point( $paths ) {
            $paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';
            return $paths;    
        }
        //add_filter( 'acf/settings/load_json', 'bbc_acf_json_load_point' );
    }

    // save json
    if ( ! function_exists('bbc_acf_json_save_point') ) {
        function bbc_acf_json_save_point( $path ) {
            return plugin_dir_path( __FILE__ ) . 'acf-json';
        }
        // only save if local site that pushes to Github
        if ( $bbc_is_starter_site ) {
            add_filter( 'acf/settings/save_json', 'bbc_acf_json_save_point' );
        }
    }

    // load php
    if ( ! function_exists('bbc_php_load_point') ) {
        add_filter('acfe/settings/php_load', 'bbc_php_load_point');
        function bbc_php_load_point($paths){
            $paths[] = plugin_dir_path(__FILE__) . '/acfe-php';
            return $paths;
        }
    }

    // save php
    if ( ! function_exists('bbc_acfe_php_save_point') ) {
        function bbc_acfe_php_save_point($path){
            return plugin_dir_path( __FILE__ ) . '/acfe-php';
        }
        // only save if local site that pushes to Github
        if ( $bbc_is_starter_site ) {
            add_filter('acfe/settings/php_save', 'bbc_acfe_php_save_point');
        }
    }

    // delete php (inactive)
    if ( ! function_exists('bbc_should_delete_php') ) {
        //add_filter('acfe/settings/should_delete_php/key=group_6744e88b654ef', 'bbc_should_delete_php', 10, 2);
        function bbc_should_delete_php($delete, $field_group){
            return false;
        }
    }

}

// plugin functions
if ( ! function_exists('bbc_helper_theme_functions') ) {
    function bbc_helper_theme_functions(){
        $include_files = Array(
            '/functions/forms.php',            
            '/functions/elementor.php',
            '/functions/global.php',
            '/functions/schema.php',
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

// Gravity Forms capabilities editors need to manage forms and entries
if ( ! function_exists('bbc_gf_editor_caps') ) {
    function bbc_gf_editor_caps(){
        return array(
            'gravityforms_create_form',
            'gravityforms_edit_forms',
            'gravityforms_delete_forms',
            'gravityforms_preview_forms',
            'gravityforms_view_entries',
            'gravityforms_edit_entries',
            'gravityforms_delete_entries',
            'gravityforms_export_entries',
            'gravityforms_view_entry_notes',
            'gravityforms_edit_entry_notes',
        );
    }
}

// remove the Gravity Forms capabilities granted to editors
if ( ! function_exists('bbc_helper_deactivate') ) {
    function bbc_helper_deactivate(){
        $role = get_role( 'editor' );
        if ( $role ) {
            foreach ( bbc_gf_editor_caps() as $cap ) {
                $role->remove_cap( $cap );
            }
        }
        delete_option( 'bbc_gf_editor_caps_version' );
    }
    register_deactivation_hook( __FILE__, 'bbc_helper_deactivate' );
}