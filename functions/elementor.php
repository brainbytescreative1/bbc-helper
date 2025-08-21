<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ) {

  // create elementor options page
  if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
      'page_title'    => 'Elementor Styles',
      'menu_title'    => 'Elementor Styles',
      'menu_slug'     => 'elementor_options',
      'post_id' 		=> 'elementor_options',
      'capability'    => 'edit_posts',
      'redirect'      => false,
      'icon_url' => 'dashicons-welcome-widgets-menus',
      'position' => 59,
    ));
  }

  // enable elementor options
  $enable_custom_elementor_options = get_field('enable_custom_elementor_options', 'elementor_options');

  if ( $enable_custom_elementor_options === 'enable' ) {

    // enqueue custom stylesheet and scripts
    add_action( 'wp_enqueue_scripts', 'bbc_elementor_css_js' , 100 );
    function bbc_elementor_css_js() {
      wp_enqueue_style( 'bbc_elementor_style', plugin_dir_url('bbc-helper/css/') . 'css/bbc-elementor.css', array(), PLUGIN_VERSION );
      //wp_enqueue_script( 'bbc_elementor_scripts', plugin_dir_url('bbc-helper/js/') . 'js/bbc-elementor.js', array(), PLUGIN_VERSION );
    }

    // add dynamic styles to head
    add_action('wp_head', 'bbc_add_elementor_root_styles');
    function bbc_add_elementor_root_styles(){

      // get default container padding
      $container_padding = get_field('container_padding', 'elementor_options');
      $container_padding_tablet = get_field('container_padding_tablet', 'elementor_options');
      $container_padding_mobile = get_field('container_padding_mobile', 'elementor_options');

      // get header heights
      $header_height = get_field('header_height', 'elementor_options');
      $header_height_tablet = get_field('header_height_tablet', 'elementor_options');
      $header_height_mobile = get_field('header_height_mobile', 'elementor_options');

      // calculate bottom mobile menu height
      $bottom_mobile_menu_height = get_field('bottom_mobile_menu_height', 'elementor_options');
      if ( $bottom_mobile_menu_height && $header_height_mobile ) {
        $header_height_mobile = $header_height_mobile + $bottom_mobile_menu_height;
      }

      // calculate if logged in
      if ( is_user_logged_in() ) {
        $header_height = $header_height + 32;
        $header_height_tablet = $header_height_tablet + 32;
        $header_height_mobile = $header_height_mobile + 46;
      }

      // root output
      ?>
      <style class="elementor-options">
:root {
<?php if ( $container_padding ) { ?>
  --container_padding: <?=$container_padding?>px;
<?php } ?>
<?php if ( $container_padding_tablet ) { ?>
  --container_padding_tablet: <?=$container_padding_tablet?>px;
<?php } ?>
<?php if ( $container_padding_mobile ) { ?>
  --container_padding_mobile: <?=$container_padding_mobile?>px;
<?php } ?>
<?php if ( $header_height ) { ?>
  --header_height: <?=$header_height?>px;
<?php } ?>
<?php if ( $header_height_tablet ) { ?>
  --header_height_tablet: <?=$header_height_tablet?>px;
<?php } ?>
<?php if ( $header_height_mobile ) { ?>
  --header_height_mobile: <?=$header_height_mobile?>px;
<?php } ?>
}
      </style>
      <?php
    }

    /*
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
    */

  }

}