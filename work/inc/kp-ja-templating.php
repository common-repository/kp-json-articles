<?php
/** 
 * Article Templating
 * 
 * Setup the templates for the articles
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_Templating' ) ) {

	/** 
     * Class KP_JA_Templating
     * 
     * The actual class for setting up the article templates
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
	class KP_JA_Templating {

		/** 
         * kp_ja_add_templating
         * 
         * The method adds the filters for the articles
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
		public function kp_ja_add_templating( ) : void {

			// hook into our templating filter
			add_filter( 'template_include', function( $template ) : string {

				// get our post global
				global $post;

				// check what we've got here
				if ( ( isset( $post ) ) && 'kp_json_art' == $post -> post_type ) {

					// its the archive page
					if( is_post_type_archive( 'kp_json_art' ) ) {
						$temp = locate_template( array( "kpja-archive.php" ) );
						$template = ( ! empty( $temp ) ) ? $temp : KPJA_PATH . '/templates/kpja-archive.php';
					}

					// single
					if( is_singular( 'kp_json_art' ) ) {
						$temp = locate_template( array( "kpja-single.php" ) );
						$template = ( ! empty( $temp ) ) ? $temp :  KPJA_PATH . '/templates/kpja-single.php';
					}

					// category
					if( is_tax( 'kp_json_art_cats' ) ) {
						$temp = locate_template( array( "kpja-category.php" ) );
						$template = ( ! empty( $temp ) ) ? $temp : KPJA_PATH . '/templates/kpja-category.php';
					}

					// tag
					if( is_tax( 'kp_json_art_tags' ) ) {
						$temp = locate_template( array( "kpja-tag.php" ) );
						$template = ( ! empty( $temp ) ) ? $temp : KPJA_PATH . '/templates/kpja-tag.php';
					}

				}
				
				// return our template
				return $template;

			} );
		}

	}

}
