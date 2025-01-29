<?php

if ( class_exists('acf') ) {

    // get schema function
    function bbc_get_schema($field = false, $type = false) {

        if ( $field ) {

            $post_schema_repeater = $field;

            if ( $post_schema_repeater && is_countable($post_schema_repeater) ) {

                if ( ! $type ) {
                    $type = '';
                }

                echo '<!-- custom '. $type .' schema -->';
                echo "\r\n";

                $count = 1;

                foreach ( $post_schema_repeater as $post_schema_item ) {

                    $schema_format = $post_schema_item['schema_format'];

                    if ( $schema_format === 'JSON Upload' ) {

                        $post_schema_item = $post_schema_item['upload_schema_json'];

                        if ( isset($post_schema_item) && $post_schema_item ) {

                            echo '<!-- JSON schema '. $count .' -->';
                            echo "\r\n";

                            if ( isset($post_schema_item['url']) && $post_schema_item['url']) {

                                $post_schema_item = $post_schema_item['url'];

                                $arrContextOptions=array(
                                    "ssl"=>array(
                                        "verify_peer"=>false,
                                        "verify_peer_name"=>false,
                                    ),
                                ); 
                              
                                $post_schema_item = file_get_contents($post_schema_item, false, stream_context_create($arrContextOptions));

                            }

                        }

                    } elseif ( $schema_format === 'Paste Code' ) {

                        echo '<!-- pasted schema '. $count .' -->';
                        echo "\r\n";

                        $post_schema_item = $post_schema_item['paste_schema_code'];

                    }

                    // output script
                    echo '<script type="application/ld+json" id="'. $type .'-schema-'. $count .'">';
                    echo "\r\n";

                        echo $post_schema_item;
                        echo "\r\n";

                    echo '</script>';
                    echo "\r\n";
                    echo "\r\n";

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
            $global_schema = bbc_get_schema( get_field('schema_repeater', 'schema'), 'global' );
            if ( isset($global_schema) && $global_schema ) {
                echo $global_schema;
            }

            // post schema
            global $post;
            if ( $post ) {
                $id = $post->ID;
                $post_schema = bbc_get_schema( get_field('post_schema_repeater', $id), 'post' );
                if ( $post_schema ) {
                    echo $post_schema;
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
            'position' => 63.1,
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