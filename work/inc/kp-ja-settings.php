<?php
/** 
 * Article Settings
 * 
 * Setup the article and sync settings for the plugin
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// let's pull in the Carbon Fields Namespaces we're going to need
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// check if the class already exists
if( ! class_exists( 'KP_JA_Settings' ) ) {

	/** 
     * Class KP_JA_Settings
     * 
     * The actual class for setting up and creating the settings
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
	class KP_JA_Settings {

		/** 
         * kp_ja_add_admin_pages
         * 
         * The method adds in our admin pages
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
		public function kp_ja_add_admin_pages( ) : void {

			// create the settings page
			Container::make( 'theme_options', __( 'JSON Articles Settings', 'kp-json-articles' ) )
				-> set_page_menu_title( __( 'Settings', 'kp-json-articles' ) )
				-> set_page_file( 'kpja_settings' )
				-> set_page_parent( 'edit.php?post_type=kp_json_art' )
				-> where( 'current_user_role', 'IN', array( 'contributor', 'author', 'editor', 'administrator', 'super-admin' ) )
				-> add_fields( $this -> kp_ja_add_settings( ) );

			// create the sync page
			Container::make( 'theme_options', __( 'JSON Article Sync', 'kp-json-articles' ) )
				-> set_page_menu_title( __( 'Article Sync', 'kp-json-articles' ) )
				-> set_page_file( 'kpja_sync' )
				-> set_page_parent( 'edit.php?post_type=kp_json_art' )
				-> where( 'current_user_role', 'IN', array( 'contributor', 'author', 'editor', 'administrator', 'super-admin' ) )
				-> add_fields( $this -> kp_ja_add_sync_options( ) );

			// create the documentation page
			Container::make( 'theme_options', __( 'JSON Article Sync Documentation', 'kp-json-articles' ) )
				-> set_page_menu_title( __( 'Documentation', 'kp-json-articles' ) )
				-> set_page_file( 'kpja_documentation' )
				-> set_page_parent( 'edit.php?post_type=kp_json_art' )
				-> where( 'current_user_role', 'IN', array( 'contributor', 'author', 'editor', 'administrator', 'super-admin' ) )
				-> add_fields( $this -> kp_ja_add_documentation( ) );

			// hook into the current screen and change the settings button
			add_action( 'current_screen', function( ) : void { 

				// get the current screen
				global $current_screen;

				// if we're on the settings page
				if( $current_screen -> base == 'kp_json_art_page_kpja_settings' || $current_screen -> base == 'kp_json_art_page_kpja_sync' ) {

					// change the save button content
					add_action( 'admin_footer', function( ) : void {

						// utilize jquery to replace the text in the button
						echo '<script type="text/javascript">jQuery(".button-large").val("Update Your Settings");</script><style type="text/css">.kpja-half-field {width:50% !important; flex:none !important;}.kpja-third-field {width:33% !important; flex:none !important;}</style>';
					} );

				}

				// if we're on the documentation page
				if( $current_screen -> base == 'kp_json_art_page_kpja_documentation' ) {

					// remove the metabox
					add_action( 'admin_footer', function( ) : void {

						// utilize jquery to replace the text in the button
						echo '<script type="text/javascript">jQuery("#postbox-container-1").remove( );</script><style type="text/css">.columns-2 {margin-right:0px !important;}</style>';
					} );

				}

			}, PHP_INT_MAX );

		}

		/** 
         * kp_ja_add_documentation
         * 
         * The method adds in our admin documentation page
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return array Returns an array specifying the plugin documentation
         * 
        */
		private function kp_ja_add_documentation( ) : array {

			// return the field
			return array(

				// message field
				Field::make( 'html', 'kp_ja_doc', __( '' ) )
					-> set_html( function( ) {

						// set the path
						$_path = KPJA_PATH . '/work/doc.php';

						// hold the return
						$_ret = '';

						// if the file exists
						if( is_readable( $_path ) ) {

							// start the output buffer
							ob_start( );
							
							// include the doc file
							include $_path;
							
							// include the documentation
							$_ret = ob_get_contents( );
							
							// clean and end the output buffer
							ob_end_clean( );

						}

						// return it
						return $_ret;

					} ),
			);

		}

		/** 
         * kp_ja_add_sync_options
         * 
         * The method adds in our admin sync info and settings page
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return array Returns an array specifying the plugin sync information
         * 
        */
		private function kp_ja_add_sync_options( ) : array {

			// return our fields
			return array( 

				// Sync span
				Field::make( 'select', 'kp_ja_article_sync_span', __( 'Sync Span', 'kp-json-articles' ) )
					-> set_options( $this -> kp_ja_schedules( ) )
					-> set_default_value( 'weekly' ),

				// message field
				Field::make( 'html', 'kp_ja_sync_message', __( 'Information', 'kp-json-articles' ) )
					-> set_html( function( ) {

						// set the path
						$_path = KPJA_PATH . '/work/sync-info.php';

						// hold the return
						$_ret = '';

						// if the file exists
						if( is_readable( $_path ) ) {

							// start the output buffer
							ob_start( );
							
							// include the doc file
							include $_path;
							
							// include the documentation
							$_ret = ob_get_contents( );
							
							// clean and end the output buffer
							ob_end_clean( );
			
						}
			
						// return it
						return $_ret;

					} ),

			);

		}

		/** 
         * kp_ja_schedules
         * 
         * The method pulls the current WP Cron schedules
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return array Returns an array of the existing WP cron schedules
         * 
        */
		private function kp_ja_schedules( ) : array {

			// get the schedules
			$_sched = wp_get_schedules( );

			// setup our returnable array
			$_ret = array( );

			// loop over the schedules
			foreach( $_sched as $_k => $_v ) {

				// populate the returnable array
				$_ret[ $_k ] = __( $_v[ 'display' ], 'kp-json-articles' );
			}

			// return the array
			return $_ret;

		}

		/** 
         * kp_ja_add_settings
         * 
         * The method creates the settings fields
         * 
         * @since 7.3
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return array Returns an array of the settings fields for our articles
         * 
        */
		private function kp_ja_add_settings( ) : array {

			return array(

				// endpoint url
				Field::make( 'text', 'kp_ja_json_endpoint', __( 'Site URL to Pull From', 'kp-json-articles' ) )
					-> set_attributes( array( 'type' => 'url', 'placeholder' => 'http(s)://example.com/' ) )
					-> set_required( true )
					-> help_text( __( '<strong>NOTE: </strong>You will need to make sure your provider allows publicly accessing the Wordpress JSON API, in order for this to work.', 'kp-json-articles' ) ),
			
				// article permalink
				Field::make( 'text', 'kp_ja_json_permalink', __( 'Article Permalink', 'kp-json-articles' ) )
					-> set_default_value( 'our-articles' )
					-> set_classes( 'kpja-third-field' ),

				// category permalink
				Field::make( 'text', 'kp_ja_json_cat_permalink', __( 'Category Permalink', 'kp-json-articles' ) )
					-> set_default_value( 'category' )
					-> set_classes( 'kpja-third-field' ),

				// tag permalink
				Field::make( 'text', 'kp_ja_json_tag_permalink', __( 'Tag Permalink', 'kp-json-articles' ) )
					-> set_default_value( 'tag' )
					-> set_classes( 'kpja-third-field' ),

				// article display count
				Field::make( 'text', 'kp_ja_article_display_count', __( 'Article Display Count', 'kp-json-articles' ) )
					-> set_attributes( array( 'type' => 'number' ) )
					-> set_default_value( 10 )
					-> set_classes( 'kpja-half-field' ),
				
				// article pull count
				Field::make( 'text', 'kp_ja_article_count', __( 'Article Pull Count', 'kp-json-articles' ) )
					-> set_attributes( array( 'type' => 'number', 'max' => 100 ) )
					-> set_default_value( 10 )
					-> set_classes( 'kpja-half-field' )
					-> help_text( __( '<strong>NOTE:</strong> If you are pulling more than 20 articles, wp-admin may timeout.  Please use the CLI instead: <code>wp kp_ja sync</code><br /><strong>NOTE: </strong>Wordpress has imposed a limit of 100 articles.', 'kp-json-articles' ) ),

				// categories to pull
				Field::make( 'text', 'kp_ja_article_specific_cats', __( 'Category Filter', 'kp-json-articles' ) )
					-> help_text( __( 'Comma-delimited list of category slugs to pull from.  Leave blank to pull all.', 'kp-json-articles' ) ),
				
				// tags to pull
				Field::make( 'text', 'kp_ja_article_specific_tags', __( 'Tag Filter', 'kp-json-articles' ) )
					-> help_text( __( 'Comma-delimited list of tag slugs to pull from.  Leave blank to pull all.', 'kp-json-articles' ) ),

				// date to pull start
				Field::make( 'date', 'kp_ja_article_specific_date_start', __( 'Date Filter - Start', 'kp-json-articles' ) )
					-> help_text( __( 'Leave blank to pull all.', 'kp-json-articles' ) )
					-> set_classes( 'kpja-half-field' )
					-> set_input_format( 'm/Y', 'm/Y' ),

				// date to pull end
				Field::make( 'date', 'kp_ja_article_specific_date_end', __( 'Date Filter - End', 'kp-json-articles' ) )
					-> help_text( __( 'Leave blank to pull all.', 'kp-json-articles' ) )
					-> set_classes( 'kpja-half-field' )
					-> set_input_format( 'm/Y', 'm/Y' ),

			);

		}

	}

}