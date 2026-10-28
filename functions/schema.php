<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( class_exists('acf') ) {

    // get raw contents of an uploaded schema file
    function bbc_get_schema_file_contents($file = false) {

        if ( ! is_array($file) || empty($file['ID']) ) {
            return false;
        }

        // read from disk when the file is stored locally
        $path = get_attached_file( $file['ID'] );

        if ( $path && strtolower( pathinfo($path, PATHINFO_EXTENSION) ) === 'json' && is_readable($path) ) {
            return file_get_contents($path);
        }

        // fall back to a remote request for offloaded media
        if ( ! empty($file['url']) && strtolower( pathinfo( (string) wp_parse_url($file['url'], PHP_URL_PATH), PATHINFO_EXTENSION ) ) === 'json' ) {

            $response = wp_safe_remote_get( $file['url'], array( 'timeout' => 5 ) );

            if ( ! is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200 ) {
                return wp_remote_retrieve_body($response);
            }

        }

        return false;

    }

    // validate schema and return it as safely encoded JSON
    function bbc_prepare_schema_json($schema = false) {

        if ( ! is_string($schema) || trim($schema) === '' ) {
            return false;
        }

        // remove wrapping script tags if they were included
        $schema = preg_replace('/^\s*<script[^>]*>/i', '', $schema);
        $schema = preg_replace('/<\/script>\s*$/i', '', $schema);

        $decoded = json_decode( trim($schema) );

        if ( ! is_object($decoded) && ! is_array($decoded) ) {
            return false;
        }

        // JSON_HEX_TAG keeps the output from closing the script tag
        return wp_json_encode( $decoded, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION );

    }

    // get the page/post whose schema settings apply to the current request
    function bbc_get_schema_post_id() {

        if ( is_singular() ) {
            return get_queried_object_id();
        }

        // static posts page (is_home() is not singular)
        if ( is_home() && ! is_front_page() ) {
            return (int) get_option( 'page_for_posts' );
        }

        // WooCommerce shop page (a post type archive, not singular)
        if ( function_exists('is_shop') && function_exists('wc_get_page_id') && is_shop() ) {
            $shop_page_id = (int) wc_get_page_id( 'shop' );
            return $shop_page_id > 0 ? $shop_page_id : 0;
        }

        return 0;

    }

    // get schema function
    function bbc_get_schema($field = false, $type = false) {

        if ( $field ) {

            $post_schema_repeater = $field;

            if ( $post_schema_repeater && is_countable($post_schema_repeater) ) {

                if ( ! $type ) {
                    $type = '';
                }

                echo '<!-- custom '. esc_html($type) .' schema -->';
                echo "\r\n";

                $count = 1;

                foreach ( $post_schema_repeater as $post_schema_item ) {

                    $schema_format = isset($post_schema_item['schema_format']) ? $post_schema_item['schema_format'] : false;
                    $schema_label = false;
                    $schema_json = false;

                    if ( $schema_format === 'JSON Upload' ) {

                        $schema_label = 'JSON';

                        if ( isset($post_schema_item['upload_schema_json']) && $post_schema_item['upload_schema_json'] ) {
                            $schema_json = bbc_get_schema_file_contents( $post_schema_item['upload_schema_json'] );
                        }

                    } elseif ( $schema_format === 'Paste Code' ) {

                        $schema_label = 'pasted';

                        if ( isset($post_schema_item['paste_schema_code']) ) {
                            $schema_json = $post_schema_item['paste_schema_code'];
                        }

                    }

                    if ( $schema_label ) {

                        $schema_json = bbc_prepare_schema_json($schema_json);

                        if ( $schema_json ) {

                            echo '<!-- '. $schema_label .' schema '. $count .' -->';
                            echo "\r\n";

                            // output script
                            echo '<script type="application/ld+json" id="'. esc_attr($type) .'-schema-'. $count .'">';
                            echo "\r\n";

                                echo $schema_json;
                                echo "\r\n";

                            echo '</script>';
                            echo "\r\n";
                            echo "\r\n";

                        } else {

                            echo '<!-- '. $schema_label .' schema '. $count .' skipped: invalid JSON -->';
                            echo "\r\n";

                        }

                    }

                    $count++;

                }
            }

        } else {

            return null;

        }

    }

    // add page/post schema
    if ( ! function_exists('bbc_add_page_schema_to_header') ) {
        add_action('wp_head', 'bbc_add_page_schema_to_header');
        function bbc_add_page_schema_to_header(){

            // global schema
            bbc_get_schema( get_field('schema_repeater', 'schema'), 'global' );

            // post schema
            $schema_post_id = bbc_get_schema_post_id();
            if ( $schema_post_id ) {
                bbc_get_schema( get_field('post_schema_repeater', $schema_post_id), 'post' );
            }
        }
    }

    // create schema options page
    if( function_exists('acf_add_options_page') ) {
        acf_add_options_page(array(
            'page_title'    => 'Schema',
            'menu_title'    => 'Schema',
            'menu_slug'     => 'schema',
            'post_id' 		=> 'schema',
            'capability'    => 'edit_others_posts',
            'redirect'      => false,
            'icon_url' => 'dashicons-admin-site-alt3',
            'position' => 98,
        ));
    }

    // reject invalid pasted schema when saving
    if ( ! function_exists('bbc_validate_pasted_schema') ) {
        function bbc_validate_pasted_schema( $valid, $value ) {

            if ( $valid !== true || ! is_string($value) || trim($value) === '' ) {
                return $valid;
            }

            // posted values may or may not be slashed depending on the ACF version, so accept either
            if ( ! bbc_prepare_schema_json($value) && ! bbc_prepare_schema_json( wp_unslash($value) ) ) {
                return 'Schema is not valid JSON, so it would be skipped on the site. Check for trailing commas, comments or more than one script block.';
            }

            return $valid;

        }
        add_filter( 'acf/validate_value/name=paste_schema_code', 'bbc_validate_pasted_schema', 10, 2 );
    }

    // reject invalid uploaded schema files when saving
    if ( ! function_exists('bbc_validate_uploaded_schema') ) {
        function bbc_validate_uploaded_schema( $valid, $value ) {

            if ( $valid !== true || ! $value || ! is_numeric($value) ) {
                return $valid;
            }

            $file = array(
                'ID'  => (int) $value,
                'url' => wp_get_attachment_url( (int) $value ),
            );

            if ( ! bbc_prepare_schema_json( bbc_get_schema_file_contents($file) ) ) {
                return 'Schema file is not valid JSON, so it would be skipped on the site.';
            }

            return $valid;

        }
        add_filter( 'acf/validate_value/name=upload_schema_json', 'bbc_validate_uploaded_schema', 10, 2 );
    }

    // allow json file uploads
    if ( ! function_exists('bbc_add_upload_mimes') ) {
        function bbc_add_upload_mimes( $types ) {
            $types['json'] = 'application/json';
            return $types;
        }
        add_filter( 'upload_mimes', 'bbc_add_upload_mimes' );
    }

    // disable default Yoast schema
    // check whether Yoast installed
    if ( class_exists( 'WPSEO_Options' ) ) {
        // enable filter
        add_filter( 'wpseo_json_ld_output', 'bbc_disable_yoast_schema_on_specific_pages', 10, 1 );
        function bbc_disable_yoast_schema_on_specific_pages( $data ) {

            // if the current page/post has default schema disabled
            $schema_post_id = bbc_get_schema_post_id();
            if ( $schema_post_id && get_post_meta( $schema_post_id, 'default_schema', true ) === 'disable' ) {
                return false;
            }

            return $data;

        }
    }

}
