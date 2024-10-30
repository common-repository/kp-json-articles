<?php
/** 
 * Article Sidebar
 * 
 * Creates a new sidebar strictly for use in the JSON article templates
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_Sidebar' ) ) {

    /** 
     * Class KP_JA_Sidebar
     * 
     * The actual class for creating the new sidebar
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
    class KP_JA_Sidebar {

        /** 
         * kp_ja_add_sidebar
         * 
         * The method creates the new sidebar
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        public function kp_ja_add_sidebar( ) : void {

            // register our sidebar
            register_sidebar(
                array (
                    'name' => __( 'JSON Articles', 'kp-json-articles' ),
                    'id' => 'kp_ja_sidebar',
                    'description' => __( 'Widgets in this sidebar will be shown on the JSON Article pages.', 'kp-json-articles' ),
                    'before_widget' => '<div class="widget-content">',
                    'after_widget' => "</div>",
                    'before_title' => '<h2 class="widget-title">',
                    'after_title' => '</h2>',
                )
            );
        }

    }

}