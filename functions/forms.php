<?php

if ( class_exists( 'GFCommon' ) ) {

    // add UTM fields
    function bbc_create_utm_fields_shortcode() {

        // get all forms
        $forms_list = GFAPI::get_forms();

        foreach ($forms_list as $single_form) {
            
            $form = GFAPI::get_form( $single_form['id'] );

            if ( $form ) {

                // initialize form fields array to check
                $form_fields = [];
                foreach( $form['fields'] as $field ) {
                    $form_fields[] = $field->label;
                }

                $new_form_fields = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_gclid'];

                // add new fields
                foreach ( $new_form_fields as $new_form_field ) {
                    if ( $new_form_field ) {
                        $new_field_id = 0;
                        foreach( $form['fields'] as $field ) {
                            if( $field->id > $new_field_id ) {
                                $new_field_id = $field->id;
                            }
                        }
                        $new_field_id++;
                        $properties['type'] = 'hidden';
                        $field = GF_Fields::create( $properties );
                        $field->id = $new_field_id;
                        $field->label = $new_form_field;
                        $field->defaultValue = $new_form_field;
                        $form['fields'][] = $field;
                        if ( ! in_array($new_form_field, $form_fields) ) {
                            GFAPI::update_form( $form );
                        }
                    }
                }

            }

        }

    }
    add_shortcode( 'bbc_create_utm_fields', 'bbc_create_utm_fields_shortcode' );

    // create cookie
    function bbc_create_utm_cookie_shortcode() {

        // start content
        ob_start(); ?>

        <!-- store cookie -->
        <script type="text/javascript" id="bbcCreateCookie">
            function getQueryParam(url, param) {

                let queryString = url.split('?')[1];
                if (!queryString) return null;

                let params = queryString.split('&');
                for (let i = 0; i < params.length; i++) {
                    let keyValue = params[i].split('=');
                    if (keyValue[0] === param) {
                    return decodeURIComponent(keyValue[1]);
                    }
                }
                return null;
            }

            function setCookie(name, value, days) {
                let expires = "";
                if (days) {
                    let date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }

                let sameSite = " SameSite=None; ";

                document.cookie = name + "=" + (value || "") + expires + sameSite + "; path=/";
            }

            function storeUtmParameters() {
                let utmParams = ['utm_id', 'utm_source', 'utm_medium', 'utm_name', 'utm_term', 'utm_campaign', 'utm_content', 'utm_gclid'];
                let eventData = {};
                for (let i = 0; i < utmParams.length; i++) {
                    let paramValue = getQueryParam(window.location.href, utmParams[i]);
                    if (paramValue) {
                    eventData[utmParams[i]] = paramValue;
                    }
                }
                if (Object.keys(eventData).length > 0) {
                    setCookie('bbcCookie', JSON.stringify(eventData), 30); // Store cookie for 30 days
                }
            }
            storeUtmParameters();
        </script>
        <?php

        $content = ob_get_clean();
        return $content;

    }
    add_shortcode( 'bbc_create_utm_cookie', 'bbc_create_utm_cookie_shortcode' );

    // populate utm fields
    function bbc_populate_utm_shortcode($atts) {

        $default_atts = array(
            "debug" => 'false'
        );
        $params = shortcode_atts( $default_atts, $atts );

        // start content
        ob_start();
        ?>

        <!-- get cookie -->
        <script type="text/javascript" id="bbcGetCookie">
            function getCookie(cname) {
                let name = cname + "=";
                let decodedCookie = decodeURIComponent(document.cookie);
                let ca = decodedCookie.split(';');
                for(let i = 0; i <ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
        </script>

        <!-- parse cookie -->
        <script type="text/javascript" id="bbcParseCookie">
            function cookieParser(cookieString) {
                if (cookieString === "") {
                    return {};
                }
                let pairs = cookieString.split(",");
                let splittedPairs = pairs.map(cookie => cookie.split(":"));
                const cookieObj = splittedPairs.reduce(function (obj, cookie) {
                    obj[decodeURIComponent(cookie[0].trim())]
                        = decodeURIComponent(cookie[1].trim());

                    return obj;
                }, {})
                return cookieObj;
            }
        </script>

        <!-- UTM script -->
        <script type="text/javascript" id="bbcGetUTM">

            let utm_id = null;
            let utm_source = null;
            let utm_medium = null;
            let utm_name = null;
            let utm_term = null;
            let utm_campaign = null;
            let utm_content = null;
            let utm_gclid = null;

            let myCookie = getCookie("bbcCookie");
            <?php if ( $params['debug'] === 'true' ) { ?>
                console.log(myCookie);
            <?php } ?>

            let hasParameters = null;

            let getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = window.location.search.substring(1),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');
                    
                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                        
                    }
                }
                
                return false;
            };

            let searchParams = 'false';
            searchParams = new URLSearchParams(window.location.search);
            
            if ( searchParams.size ) {
                searchParams = 'true';
            } else {
                searchParams = 'false';
            }

            if ( searchParams === 'true' ) {
                utm_id = getUrlParameter('utm_id');
                utm_source = getUrlParameter('utm_source');
                utm_medium = getUrlParameter('utm_medium');
                utm_name = getUrlParameter('utm_name');
                utm_term = getUrlParameter('utm_term');
                utm_campaign = getUrlParameter('utm_campaign');
                utm_content = getUrlParameter('utm_content');
                utm_gclid = getUrlParameter('gclid');
            } else if ( myCookie ) {
                // remove characters from cookie
                myCookie = myCookie.split('{').join('');
                myCookie = myCookie.split('}').join('');
                myCookie = myCookie.split('"').join('');
                let cookieObj = cookieParser(myCookie);

                // set equal to cookie values
                utm_id = cookieObj['utm_id'];
                utm_source = cookieObj['utm_source'];
                utm_medium = cookieObj['utm_medium'];
                utm_name = cookieObj['utm_name'];
                utm_term = cookieObj['utm_term'];
                utm_campaign = cookieObj['utm_campaign'];
                utm_content = cookieObj['utm_content'];
                utm_gclid = cookieObj['gclid'];
            }

            // gclid
            let utm_gclid_field = document.querySelector('[value="utm_gclid"]');
            if ( utm_gclid != false ) {
                if ( utm_gclid_field != null ) {
                    if ( utm_gclid_field.value != utm_gclid ) {
                        utm_gclid_field.value = utm_gclid;
                    }
                }
            }
            if ( utm_gclid_field ) {
                if ( utm_gclid_field.value == 'utm_gclid' ) {
                    utm_gclid_field.value = null;
                }
            }

            // id
            let utm_id_field = document.querySelector('[value="utm_id"]');
            if ( utm_id != false ) {
                if ( utm_id_field != null ) {
                    if ( utm_id_field.value != utm_id ) {
                        utm_id_field.value = utm_id;
                    }
                }
            }
            if ( utm_id_field ) {
                if ( utm_id_field.value == 'utm_id' ) {
                    utm_id_field.value = null;
                }
            }

            // source
            let utm_source_field = document.querySelector('[value="utm_source"]');
            if ( utm_source != false ) {
                if ( utm_source_field != null ) {
                    if ( utm_source_field.value != utm_source ) {
                        utm_source_field.value = utm_source;
                    }
                }
            }
            if ( utm_source_field ) {
                if ( utm_source_field.value == 'utm_source' ) {
                    utm_source_field.value = null;
                }
            }

            // medium
            let utm_medium_field = document.querySelector('[value="utm_medium"]');
            if ( utm_medium != false ) {
                if ( utm_medium_field != null ) {
                    if ( utm_medium_field.value != utm_medium ) {
                        utm_medium_field.value = utm_medium;
                    }
                }
            }
            if ( utm_medium_field ) {
                if ( utm_medium_field.value == 'utm_medium' ) {
                    utm_medium_field.value = null;
                }
            }

            // name
            let utm_name_field = document.querySelector('[value="utm_name"]');
            if ( utm_name != false ) {
                if ( utm_name_field != null ) {
                    if ( utm_name_field.value != utm_name ) {
                        utm_name_field.value = utm_name;
                    }
                }
            }
            if ( utm_name_field ) {
                if ( utm_name_field.value == 'utm_name' ) {
                    utm_name_field.value = null;
                }
            }

            // term
            let utm_term_field = document.querySelector('[value="utm_term"]');
            if ( utm_term != false ) {
                if ( utm_term_field != null ) {
                    if ( utm_term_field.value != utm_term ) {
                        utm_term_field.value = utm_term;
                    }
                }
            }
            if ( utm_term_field ) {
                if ( utm_term_field.value == 'utm_term' ) {
                    utm_term_field.value = null;
                }
            }

            // campaign
            let utm_campaign_field = document.querySelector('[value="utm_campaign"]');
            if ( utm_campaign != false ) {
                if ( utm_campaign_field != null ) {
                    if ( utm_campaign_field.value != utm_campaign ) {
                        utm_campaign_field.value = utm_campaign;
                    }
                }
            }
            if ( utm_campaign_field ) {
                if ( utm_campaign_field.value == 'utm_campaign' ) {
                    utm_campaign_field.value = null;
                }
            }

            // content
            let utm_content_field = document.querySelector('[value="utm_content"]');
            if ( utm_content != false ) {
                if ( utm_content_field != null ) {
                    if ( utm_content_field.value != utm_content ) {
                        utm_content_field.value = utm_content;
                        
                    }
                }
            }
            if ( utm_content_field ) {
                if ( utm_content_field.value == 'utm_content' ) {
                    utm_content_field.value = null;
                }
            }

            <?php if ( $params['debug'] === 'true' ) { ?>
                if ( utm_id_field ) {
                    console.log("utm_id_field value: " + utm_id_field.value);
                }
                if ( utm_source_field ) {
                    console.log("utm_source value: " + utm_source_field.value);
                }
                if ( utm_medium_field ) {
                    console.log("utm_medium_field value: " + utm_medium_field.value);
                }
                if ( utm_name_field ) {
                    console.log("utm_name_field value: " + utm_name_field.value);
                }
                if ( utm_term_field ) {
                    console.log("utm_term_field value: " + utm_term_field.value);
                }
                if ( utm_campaign_field ) {
                    console.log("utm_campaign_field value: " + utm_campaign_field.value);
                }
                if ( utm_content_field ) {
                    console.log("utm_content_field value: " + utm_content_field.value);
                }
                if ( utm_gclid_field ) {
                    console.log("utm_gclid_field value: " + utm_gclid_field.value);
                }
            <?php } ?>
            
        </script>

        <?php

        // return content
        $content = ob_get_clean();
        return $content;

    }
    add_shortcode( 'bbc_populate_utm', 'bbc_populate_utm_shortcode' );

    function bbc_gf_editor_access() {
        $role = get_role( 'editor' );
        $role->add_cap( 'gform_full_access' );
    }
    add_action( 'admin_init', 'bbc_gf_editor_access' );

    if ( ! function_exists('bbc_populate_gtm_shortcode_null') ) {
        function bbc_populate_gtm_shortcode_null() {
            return null;
        }
        add_shortcode( 'bbc_populate_gtm', 'bbc_populate_gtm_shortcode_null' );
    }

}