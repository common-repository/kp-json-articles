<?php
/** 
 * Uninstall
 * 
 * Process the uninstalling of this plugin
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// make sure we're actually supposed to be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! WP_UNINSTALL_PLUGIN ||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) ) ) {
	exit;
}

// We'll need our db global
global $wpdb;

// setup some variables we'll need for the queries
$p_table = $wpdb -> posts;
$pm_table = $wpdb -> postmeta;
$tr_table = $wpdb -> term_relationships;
$t_table = $wpdb -> terms;
$tt_table = $wpdb -> term_taxonomy;

// delete the posts
$sql = "DELETE p, tr, pm FROM {$p_table} p LEFT JOIN {$tr_table} tr ON (p.ID = tr.object_id) LEFT JOIN {$pm_table} pm ON (p.ID = pm.post_id)
		WHERE p.post_type = 'kp_json_art';";
$wpdb -> query( $sql );

// delete the categories
$sql = "DELETE t, tt FROM {$t_table} t LEFT JOIN {$tt_table} tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy IN ( 'kp_json_art_cats' );";
$wpdb -> query( $sql );

// delete the tags
$sql = "DELETE t, tt FROM {$t_table} t LEFT JOIN {$tt_table} tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy IN ( 'kp_json_art_tags' );";
$wpdb->query( $sql );

// get all potential sites
$_sites = get_sites( array( 'fields' => 'ids' ) );

// loop over the sites
foreach( $_sites as $_site_id ) {

	// switch blog
	switch_to_blog( $_site_id );

	// setup the options table
	$_options_table = $wpdb -> options;

	// delete the settings
	$sql = "DELETE FROM {$_options_table} WHERE `option_name` LIKE '%_kp_ja_%';";
	$wpdb -> query( $sql );

	// switch back to the originating blog
	restore_current_blog( );

}

// un-register the category taxonomy
unregister_taxonomy( 'kp_json_art_cats' );

// un-register the tags taxonomy
unregister_taxonomy( 'kp_json_art_tags' );

// de-register the CPT
unregister_post_type( 'kp_json_art' );
