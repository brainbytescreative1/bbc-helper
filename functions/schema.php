<?php

if ( class_exists('acf') ) {

    // get schema function
    function bbc_get_schema($field = false) {
        if ( $field ) {
            $post_schema = $field;
            foreach ( $post_schema as $schema ) {

                $schema_format = null;
                $upload_schema_json = null;
                $paste_schema_code = null;

                if ( isset( $schema['schema_format'] ) ) {
                    $schema_format = $schema['schema_format'];
                }
                
                if ( $schema_format ) {
                    if ( $schema_format === 'JSON Upload' ) {
                        if ( isset( $schema['upload_schema_json']['url'] ) ) {
                            $upload_schema_json = $schema['upload_schema_json']['url'];
                        }
                        if ( $upload_schema_json ) {
                            echo '<script type="application/ld+json" id="page-schema">' . file_get_contents($upload_schema_json) . '</script>';
                        }
                    } elseif ( $schema_format === 'Paste Code' ) {
                        if ( isset( $schema['paste_schema_code'] ) ) {
                            $paste_schema_code = $schema['paste_schema_code'];
                        }
                        if ( $paste_schema_code ) {
                            echo '<script type="application/ld+json" id="page-schema">' . $paste_schema_code . '</script>';
                        }
                    }
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

            global $post;
            $id = $post->ID;

            // global schema
            echo bbc_get_schema(get_field('schema_repeater', 'schema'));
            
            // page schema
            echo bbc_get_schema(get_field('post_schema_repeater', $id));
            
        }
    }

    // create schema options page
    if( function_exists('acf_add_options_page') ) {
        acf_add_options_page(array(
            'page_title'    => 'Schema',
            'menu_title'    => 'Schema',
            'menu_slug'     => 'schema',
            'post_id' 		=> 'schema',
            'capability'    => 'edit_posts',
            'redirect'      => false,
            'icon_url' => 'dashicons-admin-site-alt3',
            'position' => 62,
        ));
    }

    // allow json file uploads
    if ( ! function_exists('bbc_add_upload_mimes') ) {
        function bbc_add_upload_mimes( $types ) { 
            $types['json'] = 'text/plain';
            $types['json'] = 'application/json';
            return $types;
        }
        add_filter( 'upload_mimes', 'bbc_add_upload_mimes' );
    }

}