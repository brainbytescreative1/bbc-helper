<?php

// site name
if ( ! function_exists('bbc_site_name_shortcode') ) {
    function bbc_site_name_shortcode() {
        return get_bloginfo('name');
    }
    add_shortcode( 'bbc_site_name', 'bbc_site_name_shortcode' );
}

// get year
if ( ! function_exists('bbc_year_shortcode') ) {
    function bbc_year_shortcode() {
        return date('Y');
    }
    add_shortcode( 'bbc_year', 'bbc_year_shortcode' );
}

// get bbc attribution
if ( ! function_exists('bbc_attribution_shortcode') ) {
    function bbc_attribution_shortcode($atts) {
        $default = array(
            'classes' => false,
        );
        $link = shortcode_atts($default, $atts);
        return 'Website by <a class="'. $link['classes'] .'" href="https://www.brainbytescreative.com/" target="_blank">Brain Bytes Creative</a>';
    }
    add_shortcode( 'bbc_attribution', 'bbc_attribution_shortcode' );
}

if ( ! function_exists('bbc_copyright_shortcode') ) {
    function bbc_copyright_shortcode($atts) {
        $default = array(
            'classes' => false,
        );
        $link = shortcode_atts($default, $atts);

        ob_start();
        ?>
            &copy; [bbc_year] [bbc_site_name] | [bbc_attribution classes="text-dark text-decoration-none fw-bold"]
        <?php        
        return ob_get_clean();
    }
    add_shortcode( 'bbc_copyright', 'bbc_copyright_shortcode' );
}