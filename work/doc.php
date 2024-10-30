<?php
/** 
 * Documentation
 * 
 * This is the documentation admin page
 * 
 * @since 7.3
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Framework
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

?>
<style type="text/css">
.kpja_settings {margin:0 0 10px 25px;list-style:disc;}
</style>
<h3><?php _e( 'Main Settings', 'kp-json-articles' ); ?></h3>
<ul class="kpja_settings">
    <li><strong><?php _e( 'Page:', 'kp-json-articles' ); ?> </strong> <code>wp-admin/edit.php?post_type=kp_json_art&page=kpja_settings</code></li>
    <li>
        <h4><?php _e( 'Site URL', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'The full URL to the site you wish to attempt to pull articles from, ex...', 'kp-json-articles' ); ?>  http(s)://yoursite.com</p>
        <p><strong><?php _e( 'NOTES:', 'kp-json-articles' ); ?> </strong> <?php _e( 'The site you are pulling from must allow the JSON API, and you do not need to know the endpoints for the posts, the plugin takes care of that for you.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Article Permalink', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'The base permalink for displaying the articles.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Category Permalink', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'The base permalink for displaying the article categories.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Tag Permalink', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'The base permalink for displaying the article tags.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Article Display Count', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'How many articles should be displayed per page by default?', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Article Pull Count', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'How many articles should the sync pull?', 'kp-json-articles' ); ?></p>
        <p><?php _e( '<strong>NOTE: </strong> Wordpress JSON only allows a maximum of 100 articles.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Category Filter', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'Comma-delimited list of categories from the originating site to pull the articles from.', 'kp-json-articles' ); ?></p>
        <p><?php _e( '<strong>NOTE: </strong> Defaults to all Categories', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Tag Filter', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'Comma-delimited list of tags from the originating site to pull the articles from.', 'kp-json-articles' ); ?></p>
        <p><?php _e( '<strong>NOTE: </strong> Defaults to all Tags', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Date Filter - Start', 'kp-json-articles' ); ?></h4>
        <p><?php _e( '"Published On" start date pull the articles from the originating site.', 'kp-json-articles' ); ?></p>
        <p><?php _e( '<strong>NOTE: </strong> Defaults to all', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Date Filter - End', 'kp-json-articles' ); ?></h4>
        <p><?php _e( '"Published On" end date pull the articles from the originating site.', 'kp-json-articles' ); ?></p>
        <p><?php _e( '<strong>NOTE: </strong> Defaults to all', 'kp-json-articles' ); ?></p>
    </li>
</ul>
<h3><?php _e( 'Sync Settings', 'kp-json-articles' ); ?></h3>
<ul class="kpja_settings">
    <li><strong><?php _e( 'Page:', 'kp-json-articles' ); ?> </strong> <code>wp-admin/edit.php?post_type=kp_json_art&page=kpja_sync</code></li>
    <li>
        <h4><?php _e( 'Sync Span', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'Select the span of time that you would like to sync the articles.  Pulls the pre-exising Wordpress scheduling.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Manual Sync', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'Click to manually sync the articles, please stay on the page while it is running to prevent any interuptions.', 'kp-json-articles' ); ?></p>
        <p><?php _e( '<strong>NOTE: </strong> I do not recommend doing this for any pull over 10 or so articles.  It could timeout.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'CLI Sync', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'Log into your webservers shell, and run the following command in your sites root:', 'kp-json-articles' ); ?> <code>wp kp_ja sync</code></p>
        <p><?php _e( 'NOTE: If your site is a multisite install, specifically a subdomain type, please add this flag to your command:', 'kp-json-articles' ); ?> <code>--url=http(s)://THEURLTOYOURSITE.ext</code></p>
    </li>
</ul>
<h3><?php _e( 'Sidebar &amp; Widgets', 'kp-json-articles' ); ?></h3>
<ul class="kpja_settings">
<li>
        <h4><?php _e( 'Sidebar - JSON Articles', 'kp-json-articles' ); ?></h4>
        <p><?php _e( 'We added a sidebar for use in the article templates if you need to.', 'kp-json-articles' ); ?></p>
    </li>
    <li>
        <h4><?php _e( 'Widgets', 'kp-json-articles' ); ?></h4>
        <ul class="kpja_settings">
            <li>
                <h4><?php _e( 'JSON Article Categories', 'kp-json-articles' ); ?></h4>
                <p><?php _e( 'Works exactly like the built-in "Categories" widget, except pulls from these JSON Articles.', 'kp-json-articles' ); ?></p>
            </li>
            <li>
                <h4><?php _e( 'JSON Article Tags', 'kp-json-articles' ); ?></h4>
                <p><?php _e( 'Displays a tag cloud of the JSON Articles Tags.', 'kp-json-articles' ); ?></p>
            </li>
            <li>
                <h4><?php _e( 'JSON Recent Articles', 'kp-json-articles' ); ?></h4>
                <p><?php _e( 'Displays a menu of the most recent JSON Articles.', 'kp-json-articles' ); ?></p>
            </li>
        </ul>
    </li>
</ul>
<h3><?php _e( 'Articles and Templates', 'kp-json-articles' ); ?></h3>
<ul class="kpja_settings">
    <li>
        <h4><?php _e( 'Files - main location:', 'kp-json-articles' ); ?> <code>wp-content/THISPLUGIN/templates/*</code></h4>
        <p><?php _e( 'To override our template simply copy them to your themes root directory, and make your modifications.', 'kp-json-articles' ); ?></p>
        <ul class="kpja_settings">
        <li>
                <code>kpja-archives.php</code>
                <p><?php _e( 'This is the main article listing page, just like your', 'kp-json-articles' ); ?> <code>archive.php</code> <?php _e( 'or', 'kp-json-articles' ); ?> <code>index.php</code> <?php _e( 'template files.', 'kp-json-articles' ); ?></p>
            </li>
            <li>
                <code>kpja-category.php</code>
                <p><?php _e( 'This is the article category listing page, just like your', 'kp-json-articles' ); ?> <code>category.php</code> <?php _e( 'template file.', 'kp-json-articles' ); ?></p>
            </li>
            <li>
                <code>kpja-single.php</code>
                <p><?php _e( 'This is the single article page, just like your', 'kp-json-articles' ); ?> <code>single.php</code> <?php _e( 'template file.', 'kp-json-articles' ); ?></p>
            </li>
            <li>
                <code>kpja-tag.php</code>
                <p><?php _e( 'This is the article tag listing page, just like your', 'kp-json-articles' ); ?> <code>tag.php</code> <?php _e( 'template file.', 'kp-json-articles' ); ?></p>
            </li>
        </ul>
    </li>
</ul>
