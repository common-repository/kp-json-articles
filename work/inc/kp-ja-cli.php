<?php
/** 
 * CLI Sync
 * 
 * Setup the CLI only sync method
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// we also only want to allow CLI access
defined( 'WP_CLI' ) || die( 'Only CLI access allowed' );

// check if the class already exists
if( ! class_exists( 'KP_JA_CLI' ) ) {

    /** 
     * Class KP_JA_CLI
     * 
     * The actual class for running our CLI sync
     * 
     * @since 7.3
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Framework
     * 
    */
    class KP_JA_CLI {

        public function __construct( ) {

            // add the actual command
            WP_CLI::add_command( 'kp_ja sync', function( ) : void {

                // include our syncing class
                include_once( KPJA_PATH . '/sync/kp-ja-sync.php' );

                // pull in the sync class and run it
                $_sync = new KP_JA_Sync( );
                $_sync -> run_sync( );

            } );
            
        }

    }

}