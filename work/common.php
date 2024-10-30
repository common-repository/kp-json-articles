<?php
/** 
 * Common Functionality
 * 
 * Setup the common functionality for the plugin
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// Plugin Activation
register_activation_hook( KPJA_PATH . '/kp-json-articles.php', function( ) : void {
        
    // check the PHP version, and deny if lower than 7.3
    if ( version_compare( PHP_VERSION, '7.3', '<=' ) ) {

        // it is, so throw and error message and exit
        wp_die( __( '<h1>PHP To Low</h1><p>Due to the nature of this plugin, it cannot be run on lower versions of PHP.</p><p>Please contact your hosting provider to upgrade your site to at least version 7.3.</p>', 'kp-json-articles' ), 
            __( 'Cannot Activate: PHP To Low', 'kp-json-articles' ),
            array(
                'back_link' => true,
            ) );
            
    }

} );

// Plugin De-Activation
register_deactivation_hook( KPJA_PATH . '/kp-json-articles.php', function( ) : void {

    // un-register the category taxonomy
    unregister_taxonomy( 'kp_json_art_cats' );

    // un-register the tags taxonomy
    unregister_taxonomy( 'kp_json_art_tags' );

    // de-register the CPT
    unregister_post_type( 'kp_json_art' );

    // unregister our sync cron job
    wp_clear_scheduled_hook( 'kp_ja_sync_cron' );

} );

// let's make sure the plugin is activated
if( in_array( KPJA_DIRNAME . '/' . KPJA_FILENAME, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // if this function does not exist
    if( ! function_exists( 'get_ja_option' ) ) {

        /** 
         * get_js_option
         * 
         * The method is retrieving our plugins options
         * 
         * @since 7.3
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Framework
         * 
         * @return var Returns a variable type based on the value retrieved
         * 
        */
        function get_ja_option( string $_opt ) {

            // get the option
            return get_option( '_kp_ja_' . $_opt );

        }

    }

    // setup our autoload
    spl_autoload_register( function( $_cls )  : void {

        // reformat the class name to match the file name for inclusion
        $_class = strtolower( str_ireplace( '_', '-', $_cls ) );

        // pull in our classes based on the file path
        $_path = KPJA_PATH . '/work/inc/' . $_class . '.php';

        // if the file exists
        if( file_exists( $_path ) ) {

            // include it once
            include_once( $_path );
        }

    } );        

    // inject a custom jquery script to the admin only
    add_action( 'admin_enqueue_scripts', function( $hook ) : void {

        // check if debug is enabled
        if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

            // enqueue our custom script
            wp_enqueue_script( 'kp_ja_script', plugin_dir_url( KPJA_DIRNAME . '/' . KPJA_FILENAME ) . '/assets/js/script.js', array( 'jquery' ) );

        } else {

            // enqueue our custom script
            wp_enqueue_script( 'kp_ja_script', plugin_dir_url( KPJA_DIRNAME . '/' . KPJA_FILENAME ) . '/assets/js/script.min.js', array( 'jquery' ) );

        }

        // localize our ajax
        wp_localize_script( 'kp_ja_script', 'kp_ja_ao', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), ) );

    } );

    // now add in our ajaxian hook
    add_action( 'wp_ajax_kp_ja_manual_sync', function( ) : void {

        // include our sync class
        include_once( KPJA_PATH . '/sync/kp-ja-sync.php' );

        // fire it up
        $_sync = new KP_JA_Sync( );
        $_sync -> run_sync( );

    }, PHP_INT_MAX );

    // fire up the admin pages
    add_action( 'carbon_fields_register_fields', function( ) : void {

        // add in our admin pages
        $_admin = new KP_JA_Settings( );
        $_admin -> kp_ja_add_admin_pages( );

    }, PHP_INT_MAX );

    // fire up everything else
    add_action( 'init', function( ) : void {

        // pull in the taxonomy creation
        $_tax = new KP_JA_Taxonomies( );
        $_tax -> kp_ja_add_taxonomies( );

        // pull in the custom post type
        $_pt = new KP_JA_PostTypes( );
        $_pt -> kp_ja_add_post_types( );

        // post type permissions
        $_perms = new KP_JA_Permissions( );
        $_perms -> kp_ja_mod_permissions( );

        // create our sidebar
        $_sidebar = new KP_JA_Sidebar( );
        $_sidebar -> kp_ja_add_sidebar( );

        // fire up the class to produce and schedule the job
        $_cron = new KP_JA_Cron( );
        $_cron -> kp_ja_create_cron_job( );

    }, PHP_INT_MAX );

    // after the theme is setup we can use it
    add_action( 'after_setup_theme', function( ) : void {

        // require the package
        require_once( KPJA_PATH . '/vendor/autoload.php' );
        \Carbon_Fields\Carbon_Fields::boot( );

        // create our widgets
        $_widget = new KP_JA_Widgets( );
        $_widget -> kp_ja_add_widgets( );
    
    }, ( PHP_INT_MAX - 1 ) );

    // on wp
    add_action( 'wp', function( ) : void {

        // fire up the templating
        $_template = new KP_JA_Templating( );
        $_template -> kp_ja_add_templating( );

    }, PHP_INT_MAX );

    // fired off after we save
    add_filter( 'carbon_fields_theme_options_container_saved', function( ) : void {

        // flush all rewrites
        flush_rewrite_rules( );

        // remove the already scheduled sync
        wp_clear_scheduled_hook( 'kp_ja_sync_cron' );

        // recreate it
        $_cron = new KP_JA_Cron( );
        $_cron -> kp_ja_create_cron_job( );

    } );

    // check if we are in CLI    
    if( defined( 'WP_CLI' ) ) {

        // we are in the CLI
        new KP_JA_CLI( );

    }

}
