<?php

if ( class_exists('acf') ) {

    // add page/post schema
    if ( ! function_exists('bbc_add_page_schema_to_header') ) {
        add_action('wp_head', 'bbc_add_page_schema_to_header');
        function bbc_add_page_schema_to_header(){
            $schema_format = get_field('schema_format');
            if ( $schema_format ) {
                if ( $schema_format === 'JSON Upload' ) {
                    $upload_schema_json = get_field('upload_schema_json');
                    if ( isset( $upload_schema_json['url'] ) && $upload_schema_json['url'] ) {
                        echo '<script type="application/ld+json" id="page-schema">' . file_get_contents($upload_schema_json['url']) . '</script>';
                    }
                } elseif ( $schema_format === 'Paste Code' ) {
                    $paste_schema_code = get_field('paste_schema_code');
                    if ( $paste_schema_code ) {
                        echo '<script type="application/ld+json" id="page-schema">' . $paste_schema_code . '</script>';
                    }
                }
            }
        }
    }

    // add global schema
    if ( ! function_exists('bbc_add_global_schema_to_header') ) {
        add_action('wp_head', 'bbc_add_global_schema_to_header');
        function bbc_add_global_schema_to_header(){
            $global_schema = get_field('global_schema', 'schema');
            if ( $global_schema ) {
                foreach ( $global_schema as $schema ) {
                    $schema_format = null;
                    if ( isset( $schema['global_schema_module']['schema_format'] ) ) {
                        $schema_format = $schema['global_schema_module']['schema_format'];
                    }
                    if ( $schema_format ) {
                        if ( $schema_format === 'JSON Upload' ) {
                            if ( isset ( $schema['global_schema_module']['upload_schema_json'] ) ) {
                                $upload_schema_json = $schema['global_schema_module']['upload_schema_json'];
                                if ( isset( $upload_schema_json['url'] ) && $upload_schema_json['url'] ) {
                                    echo '<script type="application/ld+json" id="global-schema">' . file_get_contents($upload_schema_json['url']) . '</script>';
                                }
                            }
                        } elseif ( $schema_format === 'Paste Code' ) {
                            if ( isset ( $schema['global_schema_module']['upload_schema_json'] ) ) {
                                $paste_schema_code = $schema['global_schema_module']['paste_schema_code'];
                                if ( $paste_schema_code ) {
                                    echo '<script type="application/ld+json" id="global-schema">' . $paste_schema_code . '</script>';
                                }
                            }
                        }
                    }
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