=== KP JSON Articles ===
Contributors: kevp75
Donate link: https://paypal.me/kevinpirnie
Tags: remote articles, json, json articles, remote posts, remote blog
Requires at least: 5.0
Tested up to: 6.0
Requires PHP: 7.3
Stable tag: 0.10.55
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

KP JSON Articles pulls in articles from another wordpress site through the normal json endpoints, it then parses the categories, tags, and attempts to pull the media.  You can change the permalinks for each, create a sidebar and add widgets to existing sidebars.

== Features ==

1. Ability to pull blog articles from another Wordpress website
    1. Configured site must have the JSON API enabled.
2. Creates or Updates posts, categories, tags, and tries to create the media for the posts
3. Configurable and filterable by category, tag, or date range of articles to pull.
4. Configurable permalinks
5. Number of articles to pull (max 100, this is a Wordpress JSON API limit)
6. Manual, cron, and cli based syncing.
    1. I would not recommend manual syncing for anything over 20 pullable articles
7. Includes basic templates
    1. To use them, simply copy the files in this plugins "templates/" directory to your theme's root directory and make your modifications.
8. Documentation and Usage in WP Admin

== Installation ==

1. Download the plugin, unzip it, and upload to your sites `/wp-content/plugins/` directory
    1. You can also upload it directly to your Plugins admin
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why would I need this plugin? =

I personally had a use case where I created my articles in my support website, but wanted to display them in my personal website, so I developed this plugin to handle it.

= Can I remove the "Original Article" link? =

No.  At this time we have no plans to create an option for that either.  Keep credit where credit is due.

= Can I edit the posts/categories/tags? =

No.  We do this to keep the originality of the posts.

= Can I use my own templates? =

Absolutely.  Check the plugins "templates/" directory, and copy the files to your theme.  You can make your modifications there.

== Screenshots ==

1. Main Settings
2. Sync Settings
3. Articles
4. Sync WP Cron Job

== Changelog ==

= 0.10.55 =
* Test: Up to 6.0 compliant
* Test: Up to PHP 8.1 Compliant
* New: Plugin Icon =)

= 0.9.37 =
* FEATURE: Translation ready

= 0.8.77 =
* VERIFY: Core 5.9 Compliant
* FEATURE: create some action hooks, listed below
    * `kpja_admin_permissions` - fires off after the admin pages permissions are squared away for the articles.
    done in case developers need more control over the permissions already set
    * `kpja_pre_cpt_create` - fires off before the custom post type is created
    * `kpja_post_cpt_create` - fires off after the custom post type is created
    * `kpja_pre_tax_create` - fires off before the taxonomies are created
    * `kpja_post_tax_create` - fires off after the taxonomies are created
* FIX: Revamped the article image pulling.  Should get all post thumbnails attached to a remote post now.
    - implemented a sample in the template kpja-single.php to show it working.

= 0.7.14 =
* VERIFY: Core 5.8.1 Compliant

= 0.7.13 =
* FIX: remote pull issue on non-https
* FIX: throw proper error messages on issue
* FIX: proper OR for file DIE on direct access
* FIX: properly pull the documentation and sync info pages
* FIX/FEATURE: replace get with safe get
* FEATURE: strongly type methods
* FEATURE: updated comments to phpdoc format

= 0.6.97 =
* framework update
* 5.8 compatibility
* force php 7.3 minimum

= 0.3.13 =
* framework update
* WP Core Version update

= 0.2.07 =
* Check for existing classes
* Test for 5.7 compliance
* Remove un-necessary includes
* CLI sync performance
* Update field framework

= 0.1.91 =
* First public release
