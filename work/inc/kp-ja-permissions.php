<?php
/** 
 * Article Permissions
 * 
 * Setup the article permissions
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_Permissions' ) ) {

    /** 
     * Class KP_JA_Permissions
     * 
     * The actual class for running our the permissions setups for the articles
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
    class KP_JA_Permissions {

        /** 
         * kp_ja_mod_permissions
         * 
         * The method modifies admin pages for managing these articles
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        public function kp_ja_mod_permissions( ) : void {

            // make sure some columns are dumped...
            add_filter( 'manage_edit-kp_json_art_columns', function ( $_cols ) : array {

                // unset all the columns
                foreach( $_cols as $key => $title ) {

                    // unset the column
                    unset( $_cols[$key] );
                }

                // add a couple new column for these
                $_cols['kp_ja_title'] = __( 'Title', 'kp-json-articles' );
                $_cols['kp_ja_cats'] = __( 'Categories', 'kp-json-articles' );
                $_cols['kp_ja_tags'] = __( 'Tags', 'kp-json-articles' );
                $_cols['kp_ja_date'] = __( 'Date', 'kp-json-articles' );

                // return the columns array
                return $_cols;

            }, 10, 2 );

            // now let's populate the data we want in the columns
            add_action( 'manage_kp_json_art_posts_custom_column', function( $_col, $_id ) : void {

                // populate the post object
                $_post = get_post( $_id );

                // if the column is the title
                if( $_col == 'kp_ja_title' ) {

                    // write out the post title, as the permalink
                    ?>
                    <a href="<?php echo esc_url( get_permalink( $_id ) ); ?>" target="_blank"><?php _e( $_post -> post_title, 'kp-json-articles' ); ?></a>
                    <?php
                    
                }

                // the categories columns
                if( $_col == 'kp_ja_cats' ) {

                    // get the posts categories
                    $_cats = wp_get_post_terms( $_id, 'kp_json_art_cats' );
                    
                    // now write it out as a list
                    foreach( $_cats as $_cat ) {
                        _e( $_cat -> name . ', ', 'kp-json-articles' );
                    };
                }

                // the tags columns
                if( $_col == 'kp_ja_tags' ) {

                    // get the posts tags
                    $_tags = wp_get_post_terms( $_id, 'kp_json_art_tags' );
                    
                    // now write it out as a list
                    foreach( $_tags as $_tag ) {
                        _e( $_tag -> name . ', ', 'kp-json-articles' );
                    };
                }

                // the date columns
                if( $_col == 'kp_ja_date' ) {

                    // write out the date
                    _e( 'Published On:<br />' . $_post -> post_date, 'kp-json-articles' );
                }

            }, 10, 2 );

            // remove the add links
            add_action( 'admin_menu', function( ) : void {

                // Hide sidebar link
                global $submenu;
                unset( $submenu['edit.php?post_type=kp_json_art'][10] );

                // Hide link on listing page
                if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'kp_json_art' ) {
                    echo '<style type="text/css">.page-title-action { display:none; }</style>';
                }

            } );
            
            // remove the links from the list page
            add_filter( 'page_row_actions', function( $_actions ) : array {

                // make sure we're only doing this for our json articles
                if( get_post_type( ) === 'kp_json_art' )
                    unset( $_actions['edit'] );
                    unset( $_actions['view'] );
                    unset( $_actions['trash'] );
                    unset( $_actions['inline hide-if-no-js'] );

                // return the rest of the array
                return $_actions;
                    
            }, 10, 2 );

            // try to remove the bulk actions
            add_filter( 'bulk_actions-edit-kp_json_art', function( $_actions ) : array {

                // return an empty array
                return array( );

            } );

            // create an action hook to be fired off here
            do_action( 'kpja_admin_permissions' );

        }

    }

}