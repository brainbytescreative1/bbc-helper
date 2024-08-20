<?php

if ( class_exists('acf') ) {

    // get schema global function
    if ( ! function_exists('bbc_get_schema') ) {
        function bbc_get_schema( $format = false, $json = false, $code = false ) {
            if ( $format && $json && $code ) {

                // initialize fields
                $schema_format = null;
                $upload_schema_json = null;
                $paste_schema_code = null;

                // determine format
                $schema_format = $format;
                if ( $schema_format ) {
                    if ( $schema_format === 'JSON Upload' ) {
                        // determine if array
                        if ( is_array($json) ) {
                            $upload_schema_json = $json['url'];
                        }

                        // determine if url
                        if ( isset( $json['url'] ) ) {
                            $upload_schema_json = $json['url'];
                            if ( $upload_schema_json ) {
                                echo '<script type="application/ld+json" id="page-schema">' . file_get_contents($upload_schema_json) . '</script>';
                            }
                        }
                    } elseif ( $schema_format === 'Paste Code' ) {
                        // determine if array
                        if ( !is_array($code) ) {
                            $paste_schema_code = $code;
                        }
                        
                        // determine if code block
                        if ( $paste_schema_code ) {
                            echo '<script type="application/ld+json" id="page-schema">' . $paste_schema_code . '</script>';
                        }
                    }
                }

            } else {
                // return if all fields not passed
                return null;
            }
        }
    }

    // add page/post schema
    if ( ! function_exists('bbc_add_page_schema_to_header') ) {
        add_action('wp_head', 'bbc_add_page_schema_to_header');
        function bbc_add_page_schema_to_header(){

            // echo global function
            echo bbc_get_schema( get_field('schema_format'), get_field('upload_schema_json'), get_field('paste_schema_code') );
            
        }
    }

    // add global schema
    if ( ! function_exists('bbc_add_global_schema_to_header') ) {
        add_action('wp_head', 'bbc_add_global_schema_to_header');
        function bbc_add_global_schema_to_header(){
            $global_schema = get_field('global_schema', 'schema');
            if ( $global_schema ) {
                foreach ( $global_schema as $schema ) {

                    // echo global function
                    echo bbc_get_schema( $schema['global_schema_module']['schema_format'], $schema['global_schema_module']['upload_schema_json'], $schema['global_schema_module']['paste_schema_code'] );

                }
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