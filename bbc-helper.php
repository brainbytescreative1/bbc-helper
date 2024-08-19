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
 * GitHub Plugin URI: https://github.com/brainbytescreative1/pico-helper
**/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/*** acf load functions ***/
if ( class_exists('acf') ) {

    add_filter('includes/acf/settings/load_json', 'pico_acf_json_load_point');

    // load json
    function my_acf_json_load_point( $paths ) {
        unset($paths[0]);
        $paths[] = plugin_dir_path( __FILE__ ) . '/acf-json';
        return $paths;    
    }
    add_filter( 'acf/settings/load_json', 'my_acf_json_load_point' );

    // save json
    function my_acf_json_save_point( $path ) {
        return plugin_dir_path( __FILE__ ) . 'acf-json';
    }
    add_filter( 'acf/settings/save_json', 'my_acf_json_save_point' );

}

/*** theme functions ***/
if ( ( 'picostrap5' === wp_get_theme()->parent_theme ) && class_exists('acf') ) {

    function bbc_include_theme_functions(){
        $include_files = Array(
            '/functions/advanced.php',
            '/functions/background.php',
            '/functions/buttons.php',
            '/functions/colors.php',
            '/functions/dividers.php',
            '/functions/forms.php',
            '/functions/images.php',
            '/functions/integrations.php',
            '/functions/js.php',
            '/functions/menus.php',
            '/functions/misc.php',
            '/functions/options-pages.php',
            '/functions/shortcodes.php',
            '/functions/text-styles.php',
        );

        if ( $include_files ) {
            foreach ($include_files as $file) {
                if(file_exists(__DIR__ . $file)) {
                    include_once(__DIR__ . $file);
                }
            }
        }
    }
    add_action( 'init', 'bbc_include_theme_functions', 10 );

    // add options pages
    add_action( 'init', 'bbc_add_options_pages', 10 );

    // prevent deferring custom functions
    add_filter( 'rocket_defer_inline_exclusions', function( $inline_exclusions_list ) {
        if ( ! is_array( $inline_exclusions_list ) ) {
            $inline_exclusions_list = array();
        }
    
        // Duplicate this line if you need to exclude more
        $inline_exclusions_list[] = 'essential-scripts';
        $inline_exclusions_list[] = 'firstElementSpacing';
        $inline_exclusions_list[] = 'headerHeight';
        $inline_exclusions_list[] = 'stickyNav';
        $inline_exclusions_list[] = 'backToTopButton';
    
        return $inline_exclusions_list;
    } );

    // add gutenberg styles
    add_action( 'after_setup_theme', 'pico_gutenberg_css' );
    function pico_gutenberg_css(){
        add_theme_support( 'editor-styles' );
        add_editor_style( '/css-output/bundle.css' );
    }

    // enqueue custom stylesheet
    add_action( 'wp_enqueue_scripts', 'bbc_stylesheet_css_js' , 100 );
    function bbc_stylesheet_css_js() {
        wp_enqueue_style( 'bbc_style', plugin_dir_url( __FILE__ ) . 'css/bbc-style.css', array(), null );
        //wp_enqueue_script( 'bbc_scripts', plugin_dir_url( __FILE__ ) . 'css/bbc-scripts.js', array(), null );
    }

    // enqueue admin stylesheet
    add_action( 'admin_enqueue_scripts', 'load_admin_style' );
    function load_admin_style() {
        wp_enqueue_style( 'admin_style', plugin_dir_url( __FILE__ ) . 'css/bbc-admin-style.css', array(), null );
        wp_enqueue_style( 'bbc_style_admin', plugin_dir_url( __FILE__ ) . 'css/bbc-style.css', array(), null );
    }

    // add dynamic styles to head
    add_action('wp_head', 'bbc_add_dynamic_styles');
    function bbc_add_dynamic_styles(){

        $head_code = get_field('head', 'code');
        $body_code = get_field('body', 'code');
        
        include __DIR__ . '/css/dynamic.php';
        
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
    }

    add_action('admin_head', 'bbc_add_dynamic_admin_styles');
    function bbc_add_dynamic_admin_styles(){ 
        $include_files = Array(
            '/css/dynamic.php',
            '/css/dynamic-admin.php',
            '/css/admin-root.php'
        );

        if ( $include_files ) {
            foreach ($include_files as $file) {
                if(file_exists(__DIR__ . $file)) {
                    include_once(__DIR__ . $file);
                }
            }
        }
    }

    // register blocks
    add_action( 'init', 'register_acf_blocks' );
    function register_acf_blocks() {
        register_block_type( __DIR__ . '/template-parts/blocks/section' );
    }

    // hide default pico footer
    add_filter('picostrap_enable_footer_elements', '__return_false');

    // hide default pico header
    add_filter('picostrap_enable_header_elements', '__return_false');

    // add header from plugin
    function add_code_to_body() {
        $template = get_page_template_slug();
        if ( ( $template !== 'page-templates/blank.php' ) && ( $template !== 'page-templates/blank-nofooter.php' ) && ( $template !== 'page-templates/empty.php' ) ) {
            $header_style = get_field('header_style', 'header');
            if ( $header_style ) {
                $header_style = 'header-' . $header_style;
            } else {
                $header_style = 'header-flush';
            }

            echo '<header id="theme-header" class="'. $header_style .'">';
            if ( function_exists('get_navbar_wrapper') ) {
                echo get_navbar_wrapper();
            }
            echo '</header>';
        }
    }
    add_action( 'wp_body_open', 'add_code_to_body' );

    // add footer functions
    function footer_widget() {

        $template = get_page_template_slug();
        if ( ( $template !== 'page-templates/blank.php' ) && ( $template !== 'page-templates/blank-nofooter.php' ) && ( $template !== 'page-templates/empty.php' ) ) {
            echo '<footer id="theme-footer">';
                dynamic_sidebar( 'reviews' );
                dynamic_sidebar( 'footerfull' );
            echo '</footer>';
        }

        echo '<button type="button" class="btn-back-to-top d-none" id="btn-back-to-top"><i class="bi bi-chevron-up"></i></button>';

    }
    add_action( 'wp_footer', 'footer_widget' );

    // modify the path to the icons directory
    add_filter('acf_icon_path_suffix', 'acf_icon_path_suffix');
    function acf_icon_path_suffix($path_suffix) {
        return '/vendor/twbs/bootstrap-icons/icons/';
    }

    // modify the path to the above prefix
    add_filter('acf_icon_path', 'acf_icon_path');
    function acf_icon_path($path_suffix) {
        return plugin_dir_path(__FILE__);
    }

    // modify the URL to the icons directory to display on the page
    add_filter('acf_icon_url', 'acf_icon_url');
    function acf_icon_url($path_suffix) {
        return plugin_dir_url( __FILE__ );
    }

    // override page templates
    function override_page_templates( $template ) {
        if ( is_page() ) {
            $template = plugin_dir_path(__FILE__) . 'page-templates/page.php';
        } elseif ( is_single() ) {
            $template = plugin_dir_path(__FILE__) . 'page-templates/single.php';
        }
        return $template;
    }
    add_filter( 'template_include', 'override_page_templates' );

    // mce fix
    function slug_editor_body_margin_fix( $settings ) {
        if ( isset( $settings['content_style'] ) ) {
            $settings['content_style'] .= ' body#tinymce { margin: 9px 10px; }';
        } else {
            $settings['content_style'] = 'body#tinymce { margin: 9px 10px; }';
        }
        return $settings;
    }
    add_filter( 'tiny_mce_before_init', 'slug_editor_body_margin_fix' );

}