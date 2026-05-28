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
            'position' => 98,
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

    // disable default Yoast schema
    // check whether Yoast installed
    if ( class_exists( 'WPSEO_Options' ) ) {
        // enable filter
        add_filter( 'wpseo_json_ld_output', 'bbc_disable_yoast_schema_on_specific_pages', 10, 1 );
        function bbc_disable_yoast_schema_on_specific_pages( $data ) {

            // get default schema value    
            $default_schema = get_post_meta( get_the_ID(), 'default_schema', true );

            // create posts array
            $posts_schema_disable = [];

            // check if post
            global $post;
            if ( $post ) {

                // if post has default schema disabled
                if ( $default_schema && ( $default_schema === 'disable' ) ) {

                    // get post id
                    $id = $post->ID;

                    // add post list to array
                    $posts_schema_disable[] = $id;

                }

            }

            // array of the IDs of the pages you want to target
            if ( is_page( ! $posts_schema_disable ) ) {
                return false;
            }
            return $data;
            
        }
    }

}