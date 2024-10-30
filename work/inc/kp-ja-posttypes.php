<?php
/** 
 * Article Post Types
 * 
 * Setup the article post types
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_PostTypes' ) ) {

	/** 
     * Class KP_JA_PostTypes
     * 
     * The actual class for creating our new CPT
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
	class KP_JA_PostTypes {

		/** 
         * kp_ja_add_post_types
         * 
         * The method creates the CPT for these articles
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
		public function kp_ja_add_post_types( ) : void {

			// fire off an action here
			do_action( 'kpja_pre_cpt_create' );
			
			// get the rewrite base for this
			$_rewrite = ( get_ja_option( 'json_permalink' ) ) ?? 'our-articles';

			// setup some labels for our arguments
			$labels = array( 'name' => __( 'KP JSON Articles', 'kp-json-articles' ), 
							'singular_name' => __( 'Article', 'kp-json-articles' ), 
							'menu_name' => __( 'JSON Articles', 'kp-json-articles' ) );

			// setup the arguments for the post type
			$args = array( 'label' => '', 
				'labels' => $labels, 
				'supports' => array( 'title', 'editor', 'page-attributes', 'thumbnail', 'custom-fields' ), 
				'taxonomies'  => array( 'kp_json_art_cats', 'kp_json_art_tags' ), 
				'hierarchical' => true, 
				'public' => true, 
				'show_ui' => true, 
				'show_in_menu' => true, 
				'menu_position' => 5, 
				'menu_icon'  => 'dashicons-clipboard', 
				'show_in_admin_bar' => false, 
				'show_in_nav_menus' => true, 
				'can_export' => true, 
				'has_archive' => true, 
				'exclude_from_search' => false, 
				'publicly_queryable' => true, 
				'query_var' => 'kp_json_art', 
				'show_in_rest' => false,
				'capability_type' => 'post',
				'rewrite' => array( 'slug' => $_rewrite ),

			);

			// register the post type
			register_post_type( 'kp_json_art', $args );

			// flush the rewrites so we can have permalinks
			flush_rewrite_rules( );

			// fire off an action here
			do_action( 'kpja_post_cpt_create' );

		}

	}

}