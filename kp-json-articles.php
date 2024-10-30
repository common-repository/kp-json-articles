<?php

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

/*
Plugin Name:  KP JSON Articles
Plugin URI:   https://kevinpirnie.com
Description:  KP JSON Articles pulls in articles from another wordpress site based on the json endpoint configured
Version:      0.10.55
Network:      false
Requires PHP: 7.3
Author:       Kevin C Pirnie
Text Domain:  kp-json-articles
License:      GPLv3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
*/

// setup the full page to this plugin
define( 'KPJA_PATH', dirname( __FILE__ ) );

// setup the directory name
define( 'KPJA_DIRNAME', basename( dirname( __FILE__ ) ) );

// setup the primary plugin file name
define( 'KPJA_FILENAME', basename( __FILE__ ) );

// Include our "work"
require dirname( __FILE__ ) . '/work/common.php'; 
