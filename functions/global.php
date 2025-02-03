<?php

function bbc_global_fields( $wp_customize ) {

  // global email
  $wp_customize->add_setting(
    'global_email',
    array(
      'default' => '',
      'type' => 'option',
      'capability' => 'edit_theme_options'
    ),
  );
  $wp_customize->add_control( new WP_Customize_Control(
    $wp_customize,
    'global_email',
    array(
      'label'      => __( 'Global email', 'textdomain' ),
      'description' => __( '[bbc_global_email link="true"] or set link to false', 'bbc' ),
      'settings'   => 'global_email',
      'priority'   => 101,
      'section'    => 'title_tagline',
      'type'       => 'text',
    )
  ) );
  // usage
  // $global_email = get_option('global_email');

  // global phone
  $wp_customize->add_setting(
    'global_phone',
    array(
      'default' => '',
      'type' => 'option',
      'capability' => 'edit_theme_options'
    ),
  );
  $wp_customize->add_control( new WP_Customize_Control(
    $wp_customize,
    'global_phone',
    array(
      'label'      => __( 'Global Phone', 'textdomain' ),
      'description' => __( '[bbc_global_phone link="true"] or set link to false', 'bbc' ),
      'settings'   => 'global_phone',
      'priority'   => 101,
      'section'    => 'title_tagline',
      'type'       => 'text',
    )
  ) );
  // usage
  // $global_phone = get_option('global_phone');

  // global address
  $wp_customize->add_setting(
    'global_address',
    array(
      'default' => '',
      'type' => 'option',
      'capability' => 'edit_theme_options'
    ),
  );
  $wp_customize->add_control( new WP_Customize_Control(
    $wp_customize,
    'global_address',
    array(
      'label'      => __( 'Global address', 'textdomain' ),
      'description' => __( '[bbc_global_address]', 'bbc' ),
      'settings'   => 'global_address',
      'priority'   => 101,
      'section'    => 'title_tagline',
      'type'       => 'text',
    )
  ) );
  // usage
  // $global_address = get_option('global_address');

}
add_action( 'customize_register', 'bbc_global_fields' );

// site name
if ( ! function_exists('bbc_site_name_shortcode') ) {
  function bbc_site_name_shortcode() {
    return get_bloginfo('name');
  }
  add_shortcode( 'bbc_site_name', 'bbc_site_name_shortcode' );
}

// site url
if ( ! function_exists('bbc_site_url_shortcode') ) {
  function bbc_site_url_shortcode() {
    return '<a href="' . get_site_url() . '">' . get_site_url() . '</a>';
    return ;
  }
  add_shortcode( 'bbc_site_url', 'bbc_site_url_shortcode' );
}

// global email
if ( ! function_exists('bbc_global_email_shortcode') ) {
  function bbc_global_email_shortcode($atts) {

    $default = array(
      'link' => false,
    );

    $link = shortcode_atts($default, $atts);

    $global_email = get_option('global_email');

    if ( $global_email ) {
      if ( $link['link'] ) {
        return '<a href="mailto:' . $global_email . '">' . $global_email . '</a>';
      } else {
        return $global_email;
      }
    }
  }
  add_shortcode( 'bbc_global_email', 'bbc_global_email_shortcode');
}

// global phone
if ( ! function_exists('bbc_global_phone_shortcode') ) {
  function bbc_global_phone_shortcode($atts) {

    $default = array(
      'link' => false,
    );

    $link = shortcode_atts($default, $atts);

    $global_phone = get_option('global_phone');

    if ( $global_phone ) {

      if ( $link['link'] ) {
        $global_phone_clean = preg_replace('/[^a-z\d]/i', '', $global_phone);
        return '<a href="tel:+1' . $global_phone_clean . '">' . $global_phone . '</a>';
      } else {
        return $global_phone;
      }

    }
  }
  add_shortcode( 'bbc_global_phone', 'bbc_global_phone_shortcode');
}

// global address
if ( ! function_exists('bbc_global_address_shortcode') ) {
  function bbc_global_address_shortcode() {
    return get_option('global_address');
  }
  add_shortcode( 'bbc_global_address', 'bbc_global_address_shortcode' );
}

// get year
if ( ! function_exists('bbc_year_shortcode') ) {
  function bbc_year_shortcode() {
    return date('Y');
  }
  add_shortcode( 'bbc_year', 'bbc_year_shortcode' );
}

if ( ! function_exists('year_shortcode') ) {
  function year_shortcode() {
    return date('Y');
  }
  add_shortcode( 'year', 'year_shortcode' );
}