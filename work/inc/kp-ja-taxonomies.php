<?php
/** 
 * Article Taxonomies
 * 
 * Setup the article taxonomies
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_Taxonomies' ) ) {

    /** 
     * Class KP_JA_Taxonomies
     * 
     * The actual class for creating our article taxonomies
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
    class KP_JA_Taxonomies {

        /** 
         * kp_ja_add_taxonomies
         * 
         * The method creates custom taxonomies for the articles synced
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        public function kp_ja_add_taxonomies( ) : void {

            // fire off an action here
			do_action( 'kpja_pre_tax_create' );

            // get the rewrite base for these
            $_cat_rewrite = ( empty( get_ja_option( 'json_cat_permalink' ) ) ) ? 'category' : get_ja_option( 'json_cat_permalink' );
            $_tag_rewrite = ( empty( get_ja_option( 'json_tag_permalink' ) ) ) ? 'tag' : get_ja_option( 'json_tag_permalink' );
            $_rewrite = ( empty( get_ja_option( 'json_permalink' ) ) ) ? 'our-articles' : get_ja_option( 'json_permalink' );
            
            // categories
            register_taxonomy( 'kp_json_art_cats', 'kp_json_art', array(
                'label' => __( 'Categories', 'kp-json-articles' ),
                'rewrite' => array( 'slug' => $_rewrite . '/' . $_cat_rewrite, 'with_front' => false ),
                'hierarchical' => true,
                'public' => true,
                'publicly_queryable' => true,
                'show_in_rest' => true,
                'show_ui' => true,
                'show_in_menu' => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => true,
            ) );

            // flush the rewrite rules
            flush_rewrite_rules( );
            
            // tags
            register_taxonomy( 'kp_json_art_tags', 'kp_json_art', array(
                'label' => __( 'Tags', 'kp-json-articles' ),
                'rewrite' => array( 'slug' => $_rewrite . '/' . $_tag_rewrite, 'with_front' => false ),
                'hierarchical' => false,
                'public' => true,
                'publicly_queryable' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
                'show_ui' => true,
                'show_in_menu' => false,
                'show_in_nav_menus' => false,
                'show_admin_column' => true,
            ) );

            // flush our rewrites
            flush_rewrite_rules( );

            // fire off an action here
			do_action( 'kpja_post_tax_create' );

        }

    }

}