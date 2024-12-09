<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ) {

  // create elementor options page
  if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
      'page_title'    => 'Elementor Options',
      'menu_title'    => 'Elementor Options',
      'menu_slug'     => 'elementor_options',
      'post_id' 		=> 'elementor_options',
      'capability'    => 'edit_posts',
      'redirect'      => false,
      'icon_url' => 'dashicons-analytics',
      'position' => 63.2,
    ));
  }

  // enqueue custom stylesheet and scripts
  add_action( 'wp_enqueue_scripts', 'bbc_elementor_css_js' , 100 );
  function bbc_elementor_css_js() {
    wp_enqueue_style( 'bbc_elementor_style', plugin_dir_url('bbc-helper/css/') . 'css/bbc-elementor.css', array(), PLUGIN_VERSION );
    wp_enqueue_script( 'bbc_elementor_scripts', plugin_dir_url('bbc-helper/js/') . 'js/bbc-elementor.js', array(), PLUGIN_VERSION );
  }

  $enable_custom_sticky_nav = get_field('enable_custom_sticky_nav', 'elementor_options');

  if ( $enable_custom_sticky_nav === 'enable' ) {

    $enable_custom_sticky_nav = get_field('enable_custom_sticky_nav', 'elementor_options');

    // add footer functions
    function bbc_elementor_add_to_footer() {
      $sticky_nav_id = get_field('sticky_nav_id', 'elementor_options');
      $header_height = get_field('header_height', 'elementor_options');
      if ( $sticky_nav_id ) { ?>
        <script type="text/javascript" class="sticky-header">stickyNav("<?=$sticky_nav_id?>", "<?=$header_height?>");</script>
      <?php }
    }
    add_action( 'wp_footer', 'bbc_elementor_add_to_footer' );

  }

}