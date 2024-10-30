<?php
/** 
 * Sync Info
 * 
 * This is the sync info admin page
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

?>
<h1><?php _e( 'Sync Information', 'kp-json-articles' ); ?></h1>
<p>
    <?php _e( 'We hook into Wordpress Cron to schedule the syncing of your configured articles.  ', 'kp-json-articles' ); ?>
    <?php _e( 'Unfortunately, this does not work quite like a server based cron job, and will only fire off when you have browsers clicking through your site.  ', 'kp-json-articles' ); ?>
    <?php _e( 'However, It will fire the job the next available slot.', 'kp-json-articles' ); ?>
</p>
<p><?php _e( 'If you would like to force the sync to occur right now, click the button.  Please only click the button once, and keep this window open until you receive a message back that the sync has been completed.', 'kp-json-articles' ); ?></p>
<button class="button button-primary button-large kpja-sync-button" type="button"><?php _e( 'Manually Sync Now', 'kp-json-articles' ); ?></button>
<div class="kpja-sync-message"></div>
