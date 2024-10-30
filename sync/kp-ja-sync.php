<?php
/** 
 * Article Sync
 * 
 * Setup the article sync for the plugin
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_Sync' ) ) {

    /** 
     * Class KP_JA_Sync
     * 
     * The actual class for running our article sync
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
    class KP_JA_Sync {

        /** 
         * run_sync
         * 
         * The method syncs the remote articles to the current site in a new CPT and taxonomies
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        public function run_sync( ) : void {

            // show this only if we're syncing via CLI
            if ( defined( 'WP_CLI' ) || class_exists( '\WP_CLI' ) ) {

                // show a message
                WP_CLI::warning( __( "Pulling your articles.  This may take a bit...", 'kp-json-articles' ) );
            }

            // try to run the pull
            try {

                // run the sync
                $this -> do_the_pull( );

            } catch( Exception $e ) {

                // show the error
                WP_CLI::error( __( $e -> getMessage( ), 'kp-json-articles' ) );

            }

            // show this only if we're syncing via CLI
            if ( defined( 'WP_CLI' ) || class_exists( '\WP_CLI' ) ) {

                // show a success message
                WP_CLI::success( __( "All set.  Your articles have been synced.", 'kp-json-articles' ) );
            }

        }

        /** 
         * do_the_pull
         * 
         * The method makes the remote requests necessary to pull in the articles
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        private function do_the_pull( ) : void {

            // gracefully try to set a maximum execution time limit
            @ini_set( 'max_execution_time', 300 );

            // make sure we are going to sync
            $_sync = filter_var( ( $_GET['syncing'] ) ?? false, FILTER_VALIDATE_BOOLEAN );

            // if we're in the CLI, we just need to set the _sync flag to true
            if ( defined( 'WP_CLI' ) || class_exists( '\WP_CLI' ) ) {
                $_sync = true;
            }
            
            // if we need to sync.. do it
            if( $_sync ) {

                // clean up everything first
                $this -> kp_ja_clean_up( );

                // get our options
                $_site_to_pull_from = ( get_ja_option( 'json_endpoint' ) ) ?? get_site_url( );
                
                // get our endpoints
                $_posts_endpoint = $_site_to_pull_from . '/wp-json/wp/v2/posts';
                $_cats_endpoint = $_site_to_pull_from . '/wp-json/wp/v2/categories';
                $_tags_endpoint = $_site_to_pull_from . '/wp-json/wp/v2/tags';
                $_dates_endpoint = $_site_to_pull_from . '/wp-json/wp/v2/tags';

                // now get the rest of our settings
                $_how_many = ( get_ja_option( 'article_count' ) ) ?? 20;
                $_categories = ( get_ja_option( 'article_specific_cats' ) ) ?? null;
                $_tags = ( get_ja_option( 'article_specific_tags' ) ) ?? null;
                $_date_range = ( get_ja_option( 'article_specific_dates' ) ) ?? null;

                // some needed holders ;)
                $_qry = array( );
                $_cat_qry = array( );
                $_tag_qry = array( );
                $_date_qry = array( );
                $_limit = array( );

                // pull the articles specified by the settings above
                if( $_categories ) { // categories filtering
                    $_cat_string = $this -> pull_categories( $_cats_endpoint, $_categories );
                    $_cat_qry = array(
                        'categories' => $_cat_string
                    );
                }

                // tag filtering
                if( $_tags ) { // tags filtering
                    $_tag_string = $this -> pull_categories( $_tags_endpoint, $_tags );
                    $_tag_qry = array(
                        'tags' => $_tag_string
                    );
                }

                // date range filtering
                if( $_date_range ) { // date range filtering
                    $_dates = explode( ' - ', $_date_range );
                    $_date_qry = array(
                        'after' => $this -> format_date_range( $_dates[0] ) . 'T00:00:00',
                        'before' => $this -> format_date_range( $_dates[1] ) . 'T23:59:59',
                    );
                }

                // how many are we pulling?
                if( $_how_many != 0 ) { // how many results to return... if 0, there's no need for this
                    $_limit = array(
                        'per_page' => $_how_many
                    );
                } else {
                    $_limit = array(
                        'per_page' => 100
                    );
                }

                // merge our query arrays
                $_qry = array_merge( $_cat_qry, $_tag_qry, $_date_qry, $_limit );

                // generate our "endpoint" querystring
                $_pe_qry = http_build_query( $_qry );

                // get our posts
                $_posts = wp_safe_remote_get( $_posts_endpoint . '?' . $_pe_qry );

                // check if there is an error
                if ( is_wp_error( $_posts ) ) {

                    // set the error message
                    $_err_msg = $_posts -> get_error_message( );

                    // throw the error
                    $this -> kp_ja_throw_error( $_err_msg );

                    // exit the method
                    return;
                    
                }

                // make sure we have a valid response, if so we can proceed
                if( $_posts && $_posts['response']['code'] == 200 ) {

                    // get our json decoded response as an array
                    $_resp = json_decode( $_posts['body'] );

                    // get a count of the articles returned
                    $_rCt = count( $_resp );

                    // loop over the return
                    for( $i = 0; $i < $_rCt; ++$i ) {

                        // create the post silently
                        $this -> create_the_post( $_resp[$i], $_cats_endpoint, $_tags_endpoint );

                    }

                    // show this only if we're syncing via Web
                    if ( ! defined( 'WP_CLI' ) || ! class_exists( '\WP_CLI' ) ) {

                        // echo out the success
                        echo '<div class="notice notice-success"><p>Your sync has successfully completed.</p></div>';

                    }

                } else {

                    // get our json decoded response as an array
                    $_resp = json_decode( $_posts['body'] );

                    // throw the error
                    $this -> kp_ja_throw_error( $_resp -> message );

                    // exit the method
                    return;

                }

            }
            
        }

        /** 
         * create_the_post
         * 
         * The method creates the articles under the new CPT
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param object $_post The post object to create
         * @param string $_cat_ep The category endpoint URL
         * @param string $_tag_ep The tag endpoint URL
         * 
         * @return void This method does not return anything.
         * 
        */
        private function create_the_post( object $_post, string $_cat_ep, string $_tag_ep ) : void {

            // setup a string to inject... we want to give credit where it is due
            $_credits = '<blockquote class="">Original Article: <a href="' . esc_url( $_post -> link ) . '" target="_blank" title="' . __( sanitize_text_field( $_post -> title -> rendered ) ) . '">' . esc_url( $_post -> link ) . '</a></blockquote>';
            
            // build up our arguments to save
            $_args = array(
                'guid' => $_post -> guid -> rendered,
                'post_author' => $_post -> author,
                'post_date' => $_post -> date,
                'post_date_gmt' => $_post -> date_gmt,
                'post_modified' => $_post -> modified,
                'post_modified_gmt' => $_post -> modified_gmt,
                'post_content' => $_credits . $_post -> content -> rendered,
                'post_title' => $_post -> title -> rendered,
                'post_excerpt' => $_post -> excerpt -> rendered,
                'post_status' => $_post -> status,
                'post_type' => 'kp_json_art',
                'post_name' => $_post -> slug,
            );

            // check if the post already exists based on the slug... if it does, only update it... otherwise insert.
            $_post_check = get_posts( array( 'name' => $_post -> slug, 'post_type'=> 'kp_json_art', 'posts_per_page' => 1 ) );
            $_res = null;

            // check if the post alrady exists
            if( $_post_check ) {

                // populate the posts ID
                $_post_id = array(
                    'ID' => $_post_check[0] -> ID,
                );

                // update the post
                $_pid = wp_update_post( array_merge( $_args, $_post_id ), true );

            } else {

                // it does not exist, create a new one
                $_pid = wp_insert_post( $_args, true );

            }

            // now that the posts are created, lets add the categories and tags to them
            $this -> create_categories( $_pid, $_post -> categories, $_cat_ep );
            $this -> create_tags( $_pid, $_post -> tags, $_tag_ep );

            // site to get from
            $_site_to_pull_from = ( get_ja_option( 'json_endpoint' ) ) ?? get_site_url( );

            // now get the post image
            if( $_post -> featured_media > 0 ) {

                // create the full endpoint
                $_img_endpoint = $_site_to_pull_from . '/wp-json/wp/v2/media/' . $_post -> featured_media; 
                
                // there is a featured image, pull it and save it to the post
                $this -> create_post_media( $_pid, $_img_endpoint );

            }
            
        }

        /** 
         * create_post_media
         * 
         * The method creates the articles post media
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param int $_post_id The post ID to attach the media to
         * @param string $_endpoint The media endpoint URL
         * 
         * @return void This method does not return anything.
         * 
        */
        private function create_post_media( int $_post_id, string $_endpoint ) : void {

            // get the endpoint data
            $_media = wp_safe_remote_get( $_endpoint );

            // make sure we have a valid response & no error, if so we can proceed
            if( ( ! is_wp_error( $_media ) ) && ( $_media && $_media['response']['code'] == 200 ) ) {

                // setup the image data
                $_data = null;

                // get the info unless the body is empty
                if( $_media['body'] != 'false' ) {
                    $_data = json_decode( $_media['body'] );
                }

                // get the image url
                $_img_url = ( $_data -> guid -> rendered ) ?? null;
                
                // try to get the image
                $_resp = wp_safe_remote_get( $_img_url, array( 'timeout' => 30 ) );

                // hold a data array
                $_data = array( );

                // if there is no error
                if( ! is_wp_error( $_resp ) ) {

                    // get the file's bits
                    $_bits = wp_remote_retrieve_body( $_resp );

                    // setup the filename
                    $_filename = basename( wp_parse_url( $_img_url, PHP_URL_PATH ) );

                    // upload it based on it's bits
                    $_upload = wp_upload_bits( $_filename, null, $_bits );

                    // attachment options
                    $_attachment = array(
                        'post_title'=> $_filename,
                        'post_mime_type' => $_upload['type'],
                        'guid' => $_upload['url']
                    );

                    // insert the attachment
                    $_img_id = wp_insert_attachment( $_attachment, $_upload['file'], 0 );

                    // get the attachment meta data
                    $_data = wp_generate_attachment_metadata( $_img_id, $_upload['file'] );

                    // update the attachment
                    wp_update_attachment_metadata( $_img_id, $_data );

                    // add the thumbnail to the post
                    set_post_thumbnail( $_post_id, $_img_id );
                    
                }

            }

        }

        /** 
         * create_categories
         * 
         * The method creates the articles categories
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param int $_post The post ID
         * @param array $_cats The categories from the originating post
         * @param string $_ep The endpoiont URL for the remote categories
         * 
         * @return void This method does not return anything.
         * 
        */
        private function create_categories( int $_post, array $_cats, string $_ep ) : void {

            // get the categories from the feed, so we can get the names
            $_cdl_cats = implode( ',', $_cats );

            // get the remote categories
            $_rcats = wp_safe_remote_get( $_ep . '?include=' . $_cdl_cats );

            // make sure we have a valid response & no error, if so we can proceed
            if( ( ! is_wp_error( $_rcats ) ) && ( $_rcats && $_rcats['response']['code'] == 200 ) ) {

                // hold our response
                $_resp = null;

                // if there is a return, decode our response
                if( $_rcats ) {
                    $_resp = json_decode( $_rcats['body'] ); 
                }

                // hold our new category id's
                $_new_cats = array( );

                // check if the category returned from the wp already exists... if not create it, otherwise skip it
                foreach( $_resp as $_new_term ) {

                    // get the current categories
                    $_cur_cats = get_terms( array( 'taxonomy' => 'kp_json_art_cats', 'hide_empty' => false, 'slug' => $_new_term -> slug ) );

                    // if there's nothing returned
                    if( ! $_cur_cats ) {

                        // create the category
                        $_new_cat_id = wp_insert_category( array(
                            'taxonomy' => 'kp_json_art_cats',
                            'cat_name' => $_new_term -> name,
                        ) );

                        // add it to the array
                        if( $_new_cat_id != 0 ) {
                            $_new_cats[] = $_new_cat_id;
                        }
                    } else {

                        // add it to the array
                        $_new_cats[] = $_cur_cats[0] -> term_id;
                    }
                }

                // now we need to associate the categories to the posts
                wp_set_post_terms( $_post, $_new_cats, 'kp_json_art_cats' );

            }

        }

        /** 
         * create_tags
         * 
         * The method creates the articles tags
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param int $_post The post ID
         * @param array $_tags The tags from the originating post
         * @param string $_ep The endpoiont URL for the remote categories
         * 
         * @return void This method does not return anything.
         * 
        */
        private function create_tags( int $_post, array $_tags, string $_ep ) : void {

            // get the categories from the feed, so we can get the names
            $_cdl_cats = implode( ',', $_tags );

            // get the remote tags
            $_rcats = wp_safe_remote_get( $_ep . '?include=' . $_cdl_cats );

            // make sure we have a valid response & no error, if so we can proceed
            if( ( ! is_wp_error( $_rcats ) ) && ( $_rcats && $_rcats['response']['code'] == 200 ) ) {

                // hold the response
                $_resp = null;

                // if we have any populate our response
                if( $_rcats ) {
                    $_resp = json_decode( $_rcats['body'] ); 
                }

                // hold our new category id's
                $_new_cats = array( );

                // check if the category returned from the wp already exists... if not create it, otherwise skip it
                foreach( $_resp as $_new_term ) {

                    // get the current tags
                    $_cur_cats = get_terms( array( 'taxonomy' => 'kp_json_art_tags', 'hide_empty' => false, 'slug' => $_new_term -> slug ) );
                    
                    // if we currently do not have any
                    if( ! $_cur_cats ) {

                        // create the tag
                        $_new_cat_id = wp_insert_category( array(
                            'taxonomy' => 'kp_json_art_tags',
                            'cat_name' => $_new_term -> name,
                        ), true );
                        
                        // add it to the array
                        if( $_new_cat_id != 0 ) {
                            $_new_cats[] = $_new_cat_id;
                        }
                    } else {

                        // add it to the array
                        $_new_cats[] = $_cur_cats[0] -> term_id;
                    }
                }

                // now we need to associate the categories to the posts
                wp_set_post_terms( $_post, $_new_cats, 'kp_json_art_tags' );

            }

        }

        /** 
         * pull_categories
         * 
         * The method pulls the categories or tags for the article
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param string $_ep The endpoint to pull
         * @param string $_cl The comma-delimited list of categories/tags to actually pull
         * 
         * @return string Returns a comma-delimited list of categories/tags
         * 
        */
        private function pull_categories( string $_ep, string $_cl ) : string {

            // hold our cat string
            $_cat_string = '';

            // we need a cdl of the category id's to pass to our posts endpoint
            $_categories = trim( $_cl ); // make sure to remove the whitespace
            
            // get the remote categories
            $_cats = wp_safe_remote_get( $_ep . '?slug=' . $_categories );

            // make sure we have a valid response & no error, if so we can proceed
            if( ( ! is_wp_error( $_cats ) ) && ( $_cats && $_cats['response']['code'] == 200 ) ) {
            
                // hold the response
                $_resp = null;
                
                // if we have a response
                if( $_cats ) {
                    $_resp = $_cats['body'];
                }
                
                // decode the returned json
                $_cats = json_decode( $_resp );
                
                // we're good
                if( $_cats ){
                
                    // how many are there?
                    $_cCt = count( $_cats );
                
                    // loop over them
                    for( $i = 0; $i < $_cCt; ++$i ) {

                        // append it to the cat string
                        $_cat_string .= $_cats[$i] -> id . ',';
                    }
                
                    // remove the final comma
                    $_cat_string = rtrim( $_cat_string, ',' );
                }

            }

            // return the cat string
            return $_cat_string;

        }

        /** 
         * kp_ja_clean_up
         * 
         * Cleans out the original articles, categories, media, and tags
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        private function kp_ja_clean_up( ) : void {

            // query the post type
            $_posts = get_posts( array( 'post_type' => 'kp_json_art', 'numberposts' => -1 ) );

            // loop over them
            foreach ( $_posts as $_post ) {

                // remove the post attachment
                wp_delete_attachment( get_post_thumbnail_id( $_post -> ID ), true );

                // remove the post
                wp_delete_post( $_post -> ID, true );
            }

            // clean out the categories
            $_cats = get_terms( 'kp_json_art_cats', array( 'fields' => 'ids', 'hide_empty' => false ) );

            // loop over the return
            foreach ( $_cats as $_cat ) {

                // remove the term
                wp_delete_term( $_cat, 'kp_json_art_cats' );
            }

            // clean out the tags
            $_tags = get_terms( 'kp_json_art_tags', array( 'fields' => 'ids', 'hide_empty' => false ) );

            // loop over the return
            foreach ( $_tags as $_tag ) {

                // remove the term
                wp_delete_term( $_tag, 'kp_json_art_tags' );
            }

        }

        /** 
         * format_date_range
         * 
         * The method creates the articles tags
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param string $_str The original date string to format
         * 
         * @return string Returns a formatted date string
         * 
        */
        private function format_date_range( string $_str ) : string {

            // format our date range
            return date( 'Y-m-d', strtotime( date( 'Y-d-m', strtotime( '01/' . $_str ) ) ) );
        }

        /** 
         * kp_ja_throw_error
         * 
         * Throws the proper error based on where we are syncing
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @param string $_msg The error message to present
         * 
         * @return void This method does not return anything.
         * 
        */
        private function kp_ja_throw_error( string $_msg ) : void {

            // if we are in CLI
            if ( defined( 'WP_CLI' ) || class_exists( '\WP_CLI' ) ) {

                // thrown an exception
                throw new Exception( $_msg );

            } else {

                // set an error response code
                http_response_code( 500 );

                // render out an error message
                echo '<div id="message" class="notice notice-error"><p>' . __( $_msg ) . '</p></div>';
            }

        }

    }

}