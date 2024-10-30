<?php
/** 
 * Article Sync WP Cron
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
if( ! class_exists( 'KP_JA_Cron' ) ) {

    /** 
     * Class KP_JA_Cron
     * 
     * The actual class for setting up our WP Cron job for the sync
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
    class KP_JA_Cron {

        /** 
         * kp_ja_create_cron_job
         * 
         * The method sets up the cron job for the configured sync perdiod
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        public function kp_ja_create_cron_job( ) : void {

            // get the span setting
            $_span = get_ja_option( 'article_sync_span' );

            // create a hook to run our sync
            add_action( 'kp_ja_sync_cron', function( ) : void {
            
                // run the sync
                $this -> kp_ja_cron_run_sync( );
                
            } );

            // make sure we're only scheduling this once
            if( ! wp_next_scheduled( 'kp_ja_sync_cron' ) ) {

                // schedule the event
                wp_schedule_single_event( time( ), 'kp_ja_sync_cron' );  
            
            }

        }

        /** 
         * kp_ja_cron_run_sync
         * 
         * Run the actual sync as a job
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return void This method does not return anything.
         * 
        */
        private function kp_ja_cron_run_sync( ) : void {

            // include our sync class
            include_once( KPJA_PATH . '/sync/kp-ja-sync.php' );

            // fire it up
            $_sync = new KP_JA_Sync( );
            $_sync -> run_sync( );

        }

    }

}